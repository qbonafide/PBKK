<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\OrderModel;
use App\Models\ProdukModel;

/**
 * Dashboard admin toko: omzet, jumlah pesanan per status, produk terjual,
 * grafik omzet per bulan, dan pesanan terbaru.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $orderModel  = new OrderModel();
        $produkModel = new ProdukModel();
        $tahun       = (int) date('Y');

        $pesananTerbaru = $orderModel->daftarAdmin()->limit(6)->get()->getResultArray();

        $db         = db_connect();
        $stokRendah = $db->table('produk')
            ->select('id, nama, stok, slug')
            ->where('aktif', 1)
            ->where('stok <=', 1)
            ->orderBy('stok', 'ASC')
            ->orderBy('nama', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'title'          => 'Dashboard Admin',
            'omzet'          => $orderModel->omzet(),
            'produkTerjual'  => $orderModel->produkTerjual(),
            'jumlahStatus'   => $orderModel->jumlahPerStatus(),
            'totalProduk'    => $produkModel->where('aktif', 1)->countAllResults(),
            'totalKategori'  => (new KategoriModel())->countAllResults(),
            'grafik'         => $orderModel->omzetPerBulan($tahun),
            'tahun'          => $tahun,
            'pesananTerbaru' => $pesananTerbaru,
            'stokRendah'     => $stokRendah,
        ]);
    }
}
