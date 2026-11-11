<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter autentikasi + otorisasi berbasis role.
 *
 * Pemakaian pada route:
 *   ['filter' => 'auth']              -> wajib login
 *   ['filter' => 'auth:admin']        -> wajib login sebagai admin
 *   ['filter' => 'auth:admin,operator'] -> admin atau operator
 *
 * Perilaku:
 * - belum login  : redirect ke /login dengan pesan
 * - role salah   : halaman 403 (bukan redirect) agar URL terbukti ditolak
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');

        if (! $session->get('logged_in')) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman tersebut.');
        }

        $allowedRoles = is_array($arguments) ? $arguments : [];

        if ($allowedRoles !== [] && ! in_array((string) $session->get('role'), $allowedRoles, true)) {
            $response = service('response');
            $response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
            $response->setBody(view('errors/html/error_403', [
                'title'   => 'Akses Ditolak',
                'message' => 'Role akun Anda tidak memiliki izin untuk membuka halaman ini.',
            ]));

            return $response;
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses setelah respons.
    }
}
