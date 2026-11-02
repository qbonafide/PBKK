<?php

namespace App\Controllers;

/**
 * Halaman depan SIAKAD: hanya menampilkan ringkasan aplikasi dan tautan login.
 * Tamu yang sudah login langsung diarahkan ke dashboard sesuai role.
 */
class Home extends BaseController
{
    public function index()
    {
        if (session()->get('user_id')) {
            return match (session()->get('role')) {
                'admin'     => redirect()->to(site_url('admin/dashboard')),
                'dosen'     => redirect()->to(site_url('dosen/dashboard')),
                'mahasiswa' => redirect()->to(site_url('mahasiswa/dashboard')),
                default     => redirect()->to(site_url('login')),
            };
        }

        return redirect()->to(site_url('login'));
    }
}
