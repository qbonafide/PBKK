<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;

/**
 * Daftar mahasiswa perwalian milik dosen yang sedang login, lengkap dengan IPK.
 */
class Perwalian extends BaseController
{
    public function index()
    {
        $dosen = (new DosenModel())->milikUser((int) session()->get('user_id'));

        if ($dosen === null) {
            return redirect()->to(site_url('dosen/dashboard'))
                ->with('error', 'Akun Anda belum terhubung dengan data dosen.');
        }

        $mahasiswa = (new MahasiswaModel())->perDosenWali((int) $dosen['id']);
        $krsModel  = new KrsModel();

        foreach ($mahasiswa as $index => $mhs) {
            $mahasiswa[$index]['ipk'] = $krsModel->ipk((int) $mhs['id']);
        }

        return view('dosen/perwalian', [
            'title'     => 'Mahasiswa Perwalian',
            'dosen'     => $dosen,
            'mahasiswa' => $mahasiswa,
        ]);
    }
}
