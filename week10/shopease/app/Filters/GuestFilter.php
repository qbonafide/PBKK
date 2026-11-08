<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter kebalikan AuthFilter: halaman login/register hanya untuk tamu.
 *
 * Pengguna yang sudah login diarahkan ke halaman yang sesuai rolenya
 * supaya tidak melihat formulir login lagi.
 */
class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');

        if (! $session->get('logged_in')) {
            return null;
        }

        $target = in_array($session->get('role'), ['admin', 'operator'], true)
            ? 'admin/dashboard'
            : '';

        return redirect()->to(site_url($target))->with('info', 'Anda sudah masuk sebagai ' . $session->get('name') . '.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses setelah respons.
    }
}
