<?php

namespace App\Controllers;

use App\Models\ProdukModel;

/**
 * Keranjang belanja berbasis session: [produk_id => qty].
 *
 * Harga dan stok selalu dihitung ulang dari database (lihat hitung_keranjang()),
 * jadi perubahan harga oleh admin langsung terpakai dan nilai di session tidak
 * bisa dimanipulasi untuk mengubah harga.
 */
class Keranjang extends BaseController
{
    public function index()
    {
        return view('keranjang/index', [
            'title'  => 'Keranjang Belanja',
            'keranjang' => hitung_keranjang(),
        ]);
    }

    public function tambah()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $qty      = max(1, (int) $this->request->getPost('qty'));

        $produk = (new ProdukModel())->where('aktif', 1)->find($produkId);

        if ($produk === null) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        if ((int) $produk['stok'] < 1) {
            return redirect()->back()->with('error', 'Stok produk ini sudah habis.');
        }

        $keranjang = session()->get('keranjang');
        $keranjang = is_array($keranjang) ? $keranjang : [];

        $sekarang = (int) ($keranjang[$produkId] ?? 0);
        $baru     = min($sekarang + $qty, (int) $produk['stok']);
        $keranjang[$produkId] = $baru;
        session()->set('keranjang', $keranjang);

        return redirect()->to(site_url('keranjang'))
            ->with('sukses', $baru < $sekarang + $qty
                ? 'Jumlah disesuaikan dengan stok yang tersedia (' . $baru . ').'
                : 'Produk ditambahkan ke keranjang.');
    }

    public function update()
    {
        $masukan   = $this->request->getPost('qty');
        $keranjang = session()->get('keranjang');
        $keranjang = is_array($keranjang) ? $keranjang : [];
        $model     = new ProdukModel();

        if (is_array($masukan)) {
            foreach ($masukan as $produkId => $qty) {
                $produkId = (int) $produkId;
                $qty      = (int) $qty;

                if ($qty < 1) {
                    unset($keranjang[$produkId]);
                    continue;
                }

                $produk = $model->find($produkId);

                if ($produk === null) {
                    unset($keranjang[$produkId]);
                    continue;
                }

                $keranjang[$produkId] = min($qty, (int) $produk['stok']);
            }
        }

        session()->set('keranjang', $keranjang);

        return redirect()->to(site_url('keranjang'))->with('sukses', 'Keranjang diperbarui.');
    }

    public function hapus()
    {
        $produkId  = (int) $this->request->getPost('produk_id');
        $keranjang = session()->get('keranjang');
        $keranjang = is_array($keranjang) ? $keranjang : [];

        unset($keranjang[$produkId]);
        session()->set('keranjang', $keranjang);

        return redirect()->to(site_url('keranjang'))->with('sukses', 'Produk dihapus dari keranjang.');
    }
}
