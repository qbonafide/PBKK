<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ShipmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Riwayat & detail pesanan customer, termasuk unggah bukti pembayaran.
 *
 * Setiap aksi memeriksa kepemilikan pesanan (BR-006) sebelum menyentuh data.
 */
class OrderController extends BaseController
{
    public function index(): string
    {
        $orderModel = new OrderModel();

        return view('orders/index', [
            'title'  => 'Riwayat Pesanan',
            'orders' => $orderModel->forUser($this->userId(), 10),
            'pager'  => $orderModel->pager,
        ]);
    }

    /**
     * Detail pesanan milik sendiri.
     */
    public function detail(int $id): string|ResponseInterface
    {
        $orderModel = new OrderModel();
        $order      = $orderModel->detail($id);

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        if ((int) $order['user_id'] !== $this->userId()) {
            return $this->forbidden('Pesanan ini bukan milik akun Anda.');
        }

        return view('orders/detail', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'payment'  => (new PaymentModel())->forOrder($id),
            'shipment' => (new ShipmentModel())->forOrder($id),
        ]);
    }

    /**
     * Unggah bukti pembayaran manual.
     *
     * Validasi: kepemilikan pesanan, status yang masih boleh dibayar,
     * tipe file, ukuran maksimal 2 MB, dan nama file acak.
     */
    public function uploadProof(int $id)
    {
        $orderModel = new OrderModel();
        $order      = $orderModel->find($id);

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        if ((int) $order['user_id'] !== $this->userId()) {
            return $this->forbidden('Pesanan ini bukan milik akun Anda.');
        }

        $payment = (new PaymentModel())->forOrder($id);

        if ($payment === null) {
            return redirect()->to(site_url('orders/' . $id))->with('error', 'Data pembayaran untuk pesanan ini tidak ditemukan.');
        }

        if ($payment['payment_status'] === PaymentModel::STATUS_VERIFIED) {
            return redirect()->to(site_url('orders/' . $id))->with('error', 'Pembayaran pesanan ini sudah terverifikasi.');
        }

        if (! in_array($order['order_status'], [OrderModel::STATUS_PENDING, OrderModel::STATUS_WAITING], true)) {
            return redirect()->to(site_url('orders/' . $id))->with('error', 'Pesanan ini tidak lagi menerima bukti pembayaran.');
        }

        $rules = [
            'proof' => 'uploaded[proof]|max_size[proof,2048]|ext_in[proof,jpg,jpeg,png,webp]'
                . '|mime_in[proof,image/jpeg,image/png,image/webp]|is_image[proof]',
        ];

        $messages = [
            'proof' => [
                'uploaded' => 'Pilih berkas bukti pembayaran terlebih dahulu.',
                'max_size' => 'Ukuran berkas maksimal 2 MB.',
                'ext_in'   => 'Format berkas harus JPG, JPEG, PNG, atau WEBP.',
                'mime_in'  => 'Isi berkas tidak dikenali sebagai gambar yang diizinkan.',
                'is_image' => 'Berkas harus berupa gambar.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to(site_url('orders/' . $id))
                ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('proof');
        $name = $file->getRandomName();

        if (! $file->move(FCPATH . 'uploads/payments', $name)) {
            return redirect()->to(site_url('orders/' . $id))->with('error', 'Berkas gagal disimpan. Silakan coba lagi.');
        }

        $paymentModel = new PaymentModel();
        $paymentModel->update((int) $payment['id'], [
            'method'         => (string) ($this->request->getPost('method') ?? 'Transfer Bank'),
            'proof_file'     => $name,
            'payment_status' => PaymentModel::STATUS_SUBMITTED,
            'notes'          => null,
            'verified_by'    => null,
            'verified_at'    => null,
        ]);

        if (OrderModel::canTransition($order['order_status'], OrderModel::STATUS_WAITING)) {
            $orderModel->update($id, ['order_status' => OrderModel::STATUS_WAITING]);
        }

        return redirect()->to(site_url('orders/' . $id))
            ->with('success', 'Bukti pembayaran terkirim. Mohon tunggu verifikasi admin.');
    }
}
