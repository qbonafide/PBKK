<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProdukModel;

/**
 * Checkout pesanan.
 *
 * Seluruh proses berjalan di dalam transaksi database:
 *  - stok dikurangi dengan query bersyarat (stok >= qty) sehingga tidak minus,
 *  - order dan order_items dibuat bersamaan,
 *  - jika satu item gagal, semua perubahan dibatalkan.
 */
class Checkout extends BaseController
{
    public function index()
    {
        $keranjang = hitung_keranjang();

        if ($keranjang['items'] === []) {
            return redirect()->to(site_url('keranjang'))
                ->with('error', 'Keranjang masih kosong. Pilih produk terlebih dahulu.');
        }

        $user = (new \App\Models\UserModel())->find((int) session()->get('user_id'));

        return view('checkout/index', [
            'title'     => 'Checkout',
            'keranjang' => $keranjang,
            'user'      => $user,
        ]);
    }

    public function proses()
    {
        $aturan = [
            'nama_penerima' => 'required|min_length[3]|max_length[100]',
            'no_hp'         => 'required|max_length[20]',
            'alamat_kirim'  => 'required|min_length[10]',
            'catatan'       => 'permit_empty|max_length[255]',
        ];

        $pesan = [
            'nama_penerima' => [
                'required'   => 'Nama penerima wajib diisi.',
                'min_length' => 'Nama penerima minimal 3 karakter.',
            ],
            'no_hp'         => ['required' => 'Nomor HP wajib diisi.'],
            'alamat_kirim'  => [
                'required'   => 'Alamat pengiriman wajib diisi.',
                'min_length' => 'Alamat minimal 10 karakter.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $keranjang = hitung_keranjang();

        if ($keranjang['items'] === []) {
            return redirect()->to(site_url('keranjang'))
                ->with('error', 'Keranjang kosong atau stok produk sudah habis.');
        }

        $db          = db_connect();
        $orderModel  = new OrderModel();
        $itemModel   = new OrderItemModel();
        $produkModel = new ProdukModel();

        $db->transStart();

        try {
            $invoice = $orderModel->buatInvoice();

            $orderModel->insert([
                'user_id'       => (int) session()->get('user_id'),
                'invoice'       => $invoice,
                'nama_penerima' => (string) $this->request->getPost('nama_penerima'),
                'no_hp'         => (string) $this->request->getPost('no_hp'),
                'alamat_kirim'  => (string) $this->request->getPost('alamat_kirim'),
                'catatan'       => (string) $this->request->getPost('catatan'),
                'subtotal'      => $keranjang['subtotal'],
                'ongkir'        => $keranjang['ongkir'],
                'total'         => $keranjang['total'],
                'status'        => 'menunggu_pembayaran',
                'expired_at'    => date('Y-m-d H:i:s', strtotime('+24 hours')),
            ]);

            $orderId = (int) $orderModel->getInsertID();

            foreach ($keranjang['items'] as $item) {
                $produkId = (int) $item['produk_id'];
                $qty      = (int) $item['qty'];

                // Pengurangan stok bersyarat: hanya berhasil bila stok mencukupi.
                $db->table('produk')
                    ->set('stok', 'stok - ' . $qty, false)
                    ->where('id', $produkId)
                    ->where('stok >=', $qty)
                    ->update();

                if ($db->affectedRows() !== 1) {
                    throw new \RuntimeException('Stok tidak mencukupi untuk ' . $item['nama'] . '.');
                }

                $itemModel->insert([
                    'order_id'    => $orderId,
                    'produk_id'   => $produkId,
                    'nama_produk' => $item['nama'],
                    'harga'       => $item['harga'],
                    'qty'         => $qty,
                    'subtotal'    => $item['subtotal'],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi pesanan gagal disimpan.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->to(site_url('keranjang'))
                ->with('error', 'Checkout gagal: ' . $e->getMessage());
        }

        session()->remove('keranjang');

        return redirect()->to(site_url('pesanan/' . $orderId))
            ->with('sukses', 'Pesanan ' . $invoice . ' dibuat. Silakan unggah bukti transfer sebelum 24 jam.');
    }

    /**
     * Menyimpan URL checkout sebagai tujuan setelah login.
     */
    public function simpanTujuan()
    {
        session()->set('intended', site_url('checkout'));

        return redirect()->to(site_url('login'));
    }
}
