<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\PenghuniModel;

/**
 * Menyajikan berkas privat (foto KTP & bukti transfer) dari writable/uploads.
 * Berkas TIDAK bisa diakses langsung lewat URL publik; setiap permintaan
 * diperiksa dulu hak aksesnya.
 */
class Berkas extends BaseController
{
    public function ktp(string $namaFile)
    {
        $penghuni = (new PenghuniModel())->where('foto_ktp', $namaFile)->first();

        if ($penghuni === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berkas tidak ditemukan.');
        }

        $bolehAkses = session()->get('role') === 'admin'
            || (int) session()->get('user_id') === (int) $penghuni['user_id'];

        if (! $bolehAkses) {
            return $this->tolakAkses();
        }

        return $this->kirimBerkas(WRITEPATH . 'uploads/ktp/' . $namaFile);
    }

    public function bukti(string $namaFile)
    {
        $pembayaran = (new PembayaranModel())->where('bukti_transfer', $namaFile)->first();

        if ($pembayaran === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berkas tidak ditemukan.');
        }

        $adalahAdmin = session()->get('role') === 'admin';
        $pemilik     = (new PenghuniModel())->where('user_id', session()->get('user_id'))->first();
        $adalahPemilik = $pemilik !== null && (int) $pemilik['id'] === (int) $pembayaran['penghuni_id'];

        if (! $adalahAdmin && ! $adalahPemilik) {
            return $this->tolakAkses();
        }

        return $this->kirimBerkas(WRITEPATH . 'uploads/bukti/' . $namaFile);
    }

    private function kirimBerkas(string $path)
    {
        if (! is_file($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berkas tidak ada di server.');
        }

        $mime = mime_content_type($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('Cache-Control', 'private, max-age=600')
            ->setBody(file_get_contents($path));
    }

    private function tolakAkses()
    {
        return $this->response
            ->setStatusCode(403)
            ->setBody(view('errors/html/error_403', ['role' => session()->get('role') ?? 'tamu']));
    }
}
