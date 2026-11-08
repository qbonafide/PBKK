<?php

namespace App\Controllers;

use App\Models\AddressModel;
use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use RuntimeException;

/**
 * Checkout: mengubah isi keranjang menjadi pesanan.
 *
 * Aturan penting yang dijaga di sini:
 * - total dihitung ulang dari harga di database (BR-005)
 * - stok divalidasi dan dikurangi setelah validasi, di dalam satu transaksi (BR-009, BR-010)
 * - nama & harga produk disimpan sebagai snapshot pada order_items (BR-004)
 * - pembayaran dibuat terpisah dengan status `unpaid` (BR-008)
 */
class CheckoutController extends BaseController
{
    /** Ongkos kirim tetap untuk versi pembelajaran. */
    private const SHIPPING_COST = 20000.0;

    private const CHECKOUT_TOKEN_KEY = 'checkout_token';

    /**
     * Ringkasan pesanan sebelum dikonfirmasi.
     */
    public function index()
    {
        $cartModel = new CartModel();
        $itemModel = new CartItemModel();
        $cart      = $cartModel->forUser($this->userId());
        $items     = $itemModel->withProduct((int) $cart['id']);

        if ($items === []) {
            return redirect()->to(site_url('cart'))->with('error', 'Keranjang masih kosong.');
        }

        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        // Token sekali pakai untuk mencegah submit ganda.
        $token = bin2hex(random_bytes(16));
        $this->session->set(self::CHECKOUT_TOKEN_KEY, $token);

        return view('checkout/index', [
            'title'    => 'Checkout',
            'items'    => $items,
            'addresses' => (new AddressModel())->forUser($this->userId()),
            'subtotal' => $subtotal,
            'shipping' => self::SHIPPING_COST,
            'total'    => $subtotal + self::SHIPPING_COST,
            'token'    => $token,
        ]);
    }

    /**
     * Simpan alamat baru dari halaman checkout.
     */
    public function storeAddress()
    {
        $rules = [
            'recipient_name' => 'required|min_length[3]|max_length[100]',
            'phone'          => 'required|min_length[8]|max_length[20]',
            'address'        => 'required|min_length[10]',
            'city'           => 'required|max_length[100]',
            'province'       => 'required|max_length[100]',
            'postal_code'    => 'permit_empty|max_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('checkout'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        (new AddressModel())->saveForUser($this->userId(), [
            'recipient_name' => (string) $this->request->getPost('recipient_name'),
            'phone'          => (string) $this->request->getPost('phone'),
            'address'        => (string) $this->request->getPost('address'),
            'city'           => (string) $this->request->getPost('city'),
            'province'       => (string) $this->request->getPost('province'),
            'postal_code'    => (string) ($this->request->getPost('postal_code') ?? ''),
        ]);

        return redirect()->to(site_url('checkout'))->with('success', 'Alamat pengiriman ditambahkan.');
    }

    /**
     * Proses checkout di dalam satu database transaction.
     */
    public function process()
    {
        $addressId = (int) $this->request->getPost('address_id');
        $token     = (string) ($this->request->getPost('checkout_token') ?? '');
        $expected  = (string) ($this->session->get(self::CHECKOUT_TOKEN_KEY) ?? '');

        if ($expected === '' || ! hash_equals($expected, $token)) {
            return redirect()->to(site_url('checkout'))
                ->with('error', 'Sesi checkout kedaluwarsa. Silakan periksa kembali pesanan Anda.');
        }

        if ($addressId < 1 || ! (new AddressModel())->belongsToUser($addressId, $this->userId())) {
            return redirect()->to(site_url('checkout'))->with('error', 'Pilih alamat pengiriman terlebih dahulu.');
        }

        $cartModel = new CartModel();
        $itemModel = new CartItemModel();
        $cart      = $cartModel->forUser($this->userId());
        $items     = $itemModel->withProduct((int) $cart['id']);

        if ($items === []) {
            return redirect()->to(site_url('cart'))->with('error', 'Keranjang masih kosong.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $productModel = new ProductModel();
            $orderModel   = new OrderModel();
            $orderItem    = new OrderItemModel();
            $paymentModel = new PaymentModel();

            $subtotal = 0.0;
            $snapshots = [];

            // 1. Validasi ulang setiap produk langsung dari database.
            foreach ($items as $item) {
                $product = $productModel->find((int) $item['product_id']);

                if ($product === null || $product['status'] !== 'active') {
                    throw new RuntimeException('Produk "' . $item['name'] . '" sudah tidak dijual.');
                }

                if ((int) $product['stock'] < (int) $item['quantity']) {
                    throw new RuntimeException('Stok "' . $product['name'] . '" tidak mencukupi.');
                }

                $line      = (float) $product['price'] * (int) $item['quantity'];
                $subtotal += $line;

                $snapshots[] = [
                    'product_id'   => (int) $product['id'],
                    'product_name' => $product['name'],
                    'price'        => (float) $product['price'],
                    'quantity'     => (int) $item['quantity'],
                    'subtotal'     => $line,
                ];
            }

            $total = $subtotal + self::SHIPPING_COST;

            // 2. Header pesanan.
            $orderId = (int) $orderModel->insert([
                'user_id'       => $this->userId(),
                'address_id'    => $addressId,
                'order_number'  => $orderModel->generateOrderNumber(),
                'order_date'    => date('Y-m-d H:i:s'),
                'subtotal'      => $subtotal,
                'shipping_cost' => self::SHIPPING_COST,
                'total'         => $total,
                'order_status'  => OrderModel::STATUS_PENDING,
                'notes'         => (string) ($this->request->getPost('notes') ?? ''),
            ], true);

            if ($orderId < 1) {
                throw new RuntimeException('Pesanan gagal dibuat.');
            }

            // 3. Rincian pesanan (snapshot nama & harga).
            foreach ($snapshots as $snapshot) {
                $orderItem->insert(array_merge($snapshot, ['order_id' => $orderId]));
            }

            // 4. Kurangi stok; gagal bila stok berubah sejak validasi.
            foreach ($snapshots as $snapshot) {
                if (! $productModel->decreaseStock($snapshot['product_id'], $snapshot['quantity'])) {
                    throw new RuntimeException('Stok "' . $snapshot['product_name'] . '" berubah saat checkout. Silakan ulangi.');
                }
            }

            // 5. Catatan pembayaran awal (status terpisah dari status pesanan).
            $paymentModel->insert([
                'order_id'       => $orderId,
                'amount'         => $total,
                'payment_status' => PaymentModel::STATUS_UNPAID,
            ]);

            // 6. Kosongkan keranjang & token agar tidak bisa submit dua kali.
            $itemModel->clearCart((int) $cart['id']);
            $this->session->remove(self::CHECKOUT_TOKEN_KEY);

            if ($db->transStatus() === false) {
                throw new RuntimeException('Transaksi gagal disimpan.');
            }

            $db->transCommit();

            return redirect()->to(site_url('orders/' . $orderId))
                ->with('success', 'Pesanan berhasil dibuat. Silakan unggah bukti pembayaran.');
        } catch (\Throwable $e) {
            $db->transRollback();
            $this->session->remove(self::CHECKOUT_TOKEN_KEY);

            return redirect()->to(site_url('checkout'))->with('error', $e->getMessage());
        }
    }
}
