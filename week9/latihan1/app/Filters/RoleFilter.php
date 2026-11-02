<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter pembatas role. Dipakai dengan argumen, misal `role:admin`.
 * Jika role user tidak termasuk argumen, request dihentikan dengan halaman 403.
 *
 * Filter mengembalikan Response, sehingga controller tidak pernah dijalankan.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        if ($role === null || $arguments === null || ! in_array($role, $arguments, true)) {
            $response = service('response');
            $response->setStatusCode(403);
            $response->setBody(view('errors/html/error_403', [
                'role' => $role ?? 'tamu',
            ]));

            return $response;
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak ada aksi setelah response
    }
}
