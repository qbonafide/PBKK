<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;

/**
 * Katalog publik: pencarian, filter kategori/ukuran/kondisi, urut harga,
 * pagination, dan detail produk.
 */
class Katalog extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();

        $filter = [
            'keyword'  => (string) ($this->request->getGet('q') ?? ''),
            'kategori' => (string) ($this->request->getGet('kategori') ?? ''),
            'ukuran'   => (string) ($this->request->getGet('ukuran') ?? ''),
            'kondisi'  => (string) ($this->request->getGet('kondisi') ?? ''),
            'urut'     => (string) ($this->request->getGet('urut') ?? 'terbaru'),
        ];

        service('pager')->only(['q', 'kategori', 'ukuran', 'kondisi', 'urut']);

        return view('katalog/index', [
            'title'    => 'Katalog ThriftIn',
            'produk'   => $produkModel->untukKatalog($filter, 9),
            'pager'    => $produkModel->pager,
            'filter'   => $filter,
            'kategori' => (new KategoriModel())->orderBy('nama', 'ASC')->findAll(),
            'ukuran'   => $produkModel->daftarUkuran(),
        ]);
    }

    public function detail($slug = null)
    {
        $produk = (new ProdukModel())->cariBerdasarkanSlug((string) $slug);

        if ($produk === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        // Produk lain dari kategori yang sama sebagai rekomendasi.
        $serupa = (new ProdukModel())
            ->where('kategori_id', (int) $produk['kategori_id'])
            ->where('id !=', (int) $produk['id'])
            ->where('aktif', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        return view('katalog/detail', [
            'title'  => $produk['nama'],
            'produk' => $produk,
            'serupa' => $serupa,
        ]);
    }
}
