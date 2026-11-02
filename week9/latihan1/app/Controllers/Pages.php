<?php

namespace App\Controllers;

use App\Models\KamarModel;

/**
 * Controller halaman publik: beranda, tentang, dan kontak.
 * Semua method di sini bisa diakses tanpa login.
 */
class Pages extends BaseController
{
    public function index()
    {
        $kamarModel = new KamarModel();

        $data = [
            'title'        => 'KosKu - Kos Nyaman di Yogyakarta',
            'kamarKosong'  => $kamarModel->where('status', 'kosong')->orderBy('nomor_kamar', 'ASC')->findAll(3),
            'jumlahKosong' => $kamarModel->where('status', 'kosong')->countAllResults(),
            'jumlahTotal'  => $kamarModel->countAllResults(),
        ];

        return view('pages/home', $data);
    }

    public function about()
    {
        return view('pages/about', ['title' => 'Tentang KosKu']);
    }

    public function contact()
    {
        return view('pages/contact', ['title' => 'Kontak KosKu']);
    }

    /**
     * Memproses form kontak. Pesan tidak disimpan ke database (tidak ada tabel
     * pesan pada model data), tetapi divalidasi lalu dicatat ke log aplikasi
     * supaya admin tetap bisa membacanya di writable/logs.
     */
    public function sendContact()
    {
        $aturan = [
            'nama'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'pesan' => 'required|min_length[10]',
        ];

        $pesan = [
            'nama' => [
                'required'   => 'Nama wajib diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'pesan' => [
                'required'   => 'Pesan wajib diisi.',
                'min_length' => 'Pesan minimal 10 karakter.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        log_message('info', 'Pesan kontak dari {nama} ({email}): {pesan}', [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'pesan' => $this->request->getPost('pesan'),
        ]);

        return redirect()->to(site_url('kontak'))
            ->with('sukses', 'Terima kasih, pesan Anda sudah kami terima. Admin akan membalas lewat email.');
    }
}
