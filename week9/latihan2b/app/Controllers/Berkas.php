<?php

namespace App\Controllers;

use App\Models\OrderModel;

/**
 * Menyajikan bukti transfer dari folder privat writable/uploads/bukti.
 * Berkas hanya bisa dibuka oleh pemilik pesanan atau admin.
 */
class Berkas extends BaseController
{
    public function bukti(string $namaFile)
    {
        $pesanan = (new OrderModel())->where('bukti_bayar', $namaFile)->first();

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berkas tidak ditemukan.');
        }

        $bolehAkses = session()->get('role') === 'admin'
            || (int) session()->get('user_id') === (int) $pesanan['user_id'];

        if (! $bolehAkses) {
            return $this->response
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', ['role' => session()->get('role') ?? 'tamu']));
        }

        $path = WRITEPATH . 'uploads/bukti/' . $namaFile;

        if (! is_file($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berkas tidak ada di server.');
        }

        return $this->response
            ->setHeader('Content-Type', (string) mime_content_type($path))
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('Cache-Control', 'private, max-age=600')
            ->setBody(file_get_contents($path));
    }
}
