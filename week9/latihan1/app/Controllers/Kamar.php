<?php

namespace App\Controllers;

use App\Models\KamarModel;

/**
 * Controller katalog kamar untuk pengunjung.
 * Halaman ini sengaja tidak memakai filter auth supaya calon penyewa bisa
 * melihat kamar sebelum mendaftar.
 */
class Kamar extends BaseController
{
    public function index()
    {
        $kamarModel = new KamarModel();

        $keyword = (string) ($this->request->getGet('q') ?? '');
        $tipe    = (string) ($this->request->getGet('tipe') ?? '');
        $status  = (string) ($this->request->getGet('status') ?? '');

        // Simpan parameter filter ke pager agar tautan halaman berikutnya
        // tidak kehilangan kata kunci/filter yang sedang aktif.
        service('pager')->only(['q', 'tipe', 'status']);

        $data = [
            'title'   => 'Daftar Kamar',
            'kamar'   => $kamarModel->untukPublik($keyword, $tipe, $status, 6),
            'pager'   => $kamarModel->pager,
            'keyword' => $keyword,
            'tipe'    => $tipe,
            'status'  => $status,
        ];

        return view('kamar/index', $data);
    }

    public function detail($slug = null)
    {
        $kamarModel = new KamarModel();
        $kamar      = $kamarModel->cariBerdasarkanSlug((string) $slug);

        if ($kamar === null) {
            // Slug tidak ditemukan -> halaman 404 kustom.
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kamar "' . $slug . '" tidak ditemukan.');
        }

        return view('kamar/detail', [
            'title' => 'Kamar ' . $kamar['nomor_kamar'],
            'kamar' => $kamar,
        ]);
    }
}
