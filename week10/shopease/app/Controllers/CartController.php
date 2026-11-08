<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;

/**
 * Keranjang belanja customer.
 *
 * Semua nominal dihitung dari harga di database, bukan dari nilai
 * yang dikirim browser. Setiap aksi memvalidasi bahwa item memang
 * berada di keranjang milik pengguna yang login.
 */
class CartController extends BaseController
{
    public function index(): string
    {
        $cartModel = new CartModel();
        $itemModel = new CartItemModel();

        $cart  = $cartModel->forUser($this->userId());
        $items = $itemModel->withProduct((int) $cart['id']);

        return view('cart/index', [
            'title'   => 'Keranjang Belanja',
            'items'   => $items,
            'summary' => $this->summarize($items),
        ]);
    }

    /**
     * Tambah produk ke keranjang (menumpuk bila produk sudah ada).
     */
    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (int) $this->request->getPost('quantity');

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Jumlah produk minimal 1.');
        }

        $product = (new ProductModel())->findActive($productId);

        if ($product === null) {
            return redirect()->back()->with('error', 'Produk tidak tersedia atau sudah tidak dijual.');
        }

        if ((int) $product['stock'] < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi. Sisa stok: ' . (int) $product['stock'] . '.');
        }

        $cartModel = new CartModel();
        $itemModel = new CartItemModel();
        $cart      = $cartModel->forUser($this->userId());

        $existing = $itemModel->findProductInCart((int) $cart['id'], $productId);
        $wanted   = $existing === null ? $quantity : (int) $existing['quantity'] + $quantity;

        if ($wanted > (int) $product['stock']) {
            return redirect()->back()
                ->with('error', 'Total jumlah di keranjang melebihi stok tersedia (' . (int) $product['stock'] . ').');
        }

        if ($existing === null) {
            $itemModel->insert([
                'cart_id'    => (int) $cart['id'],
                'product_id' => $productId,
                'quantity'   => $quantity,
            ]);
        } else {
            $itemModel->update((int) $existing['id'], ['quantity' => $wanted]);
        }

        return redirect()->to(site_url('cart'))
            ->with('success', $product['name'] . ' masuk ke keranjang.');
    }

    /**
     * Ubah kuantitas salah satu item.
     */
    public function update()
    {
        $itemId   = (int) $this->request->getPost('item_id');
        $quantity = (int) $this->request->getPost('quantity');

        if ($quantity < 1) {
            return redirect()->to(site_url('cart'))->with('error', 'Jumlah produk minimal 1.');
        }

        $cartModel = new CartModel();
        $itemModel = new CartItemModel();
        $cart      = $cartModel->forUser($this->userId());

        $item = $itemModel->findInCart($itemId, (int) $cart['id']);

        if ($item === null) {
            return redirect()->to(site_url('cart'))->with('error', 'Item keranjang tidak ditemukan.');
        }

        $product = (new ProductModel())->findActive((int) $item['product_id']);

        if ($product === null) {
            return redirect()->to(site_url('cart'))->with('error', 'Produk sudah tidak dijual, silakan hapus dari keranjang.');
        }

        if ($quantity > (int) $product['stock']) {
            return redirect()->to(site_url('cart'))
                ->with('error', 'Stok hanya tersedia ' . (int) $product['stock'] . ' unit.');
        }

        $itemModel->update($itemId, ['quantity' => $quantity]);

        return redirect()->to(site_url('cart'))->with('success', 'Jumlah produk diperbarui.');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function remove(int $itemId)
    {
        $cartModel = new CartModel();
        $itemModel = new CartItemModel();
        $cart      = $cartModel->forUser($this->userId());

        $item = $itemModel->findInCart($itemId, (int) $cart['id']);

        if ($item === null) {
            return redirect()->to(site_url('cart'))->with('error', 'Item keranjang tidak ditemukan.');
        }

        $itemModel->delete($itemId);

        return redirect()->to(site_url('cart'))->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Hitung subtotal per item dan total keranjang dari harga di database.
     *
     * @param list<array<string, mixed>> $items
     *
     * @return array{lines: list<float>, subtotal: float, total_quantity: int}
     */
    private function summarize(array $items): array
    {
        $lines = [];
        $total = 0.0;
        $qty   = 0;

        foreach ($items as $item) {
            $line    = (float) $item['price'] * (int) $item['quantity'];
            $lines[] = $line;
            $total += $line;
            $qty += (int) $item['quantity'];
        }

        return [
            'lines'          => $lines,
            'subtotal'       => $total,
            'total_quantity' => $qty,
        ];
    }
}
