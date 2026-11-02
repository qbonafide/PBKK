<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter global untuk area yang butuh login.
 * Jika session user_id tidak ada, pengunjung diarahkan ke halaman login
 * dan pesan aslinya disimpan sebagai flashdata.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_id')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu untuk membuka halaman tersebut.');

            return redirect()->to(site_url('login'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak ada aksi setelah response
    }
}
