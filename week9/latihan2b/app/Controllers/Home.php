<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;

/**
 * Beranda toko: banner, produk terbaru, dan kategori.
 */
class Home extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();

        return view('home', [
            'title'     => 'ThriftIn - Preloved Berkualitas',
            'produk'    => $produkModel->produkTerbaru(6),
            'kategori'  => (new KategoriModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }
}
