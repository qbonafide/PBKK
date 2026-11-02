<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;

/**
 * Kartu Hasil Studi: nilai per semester, IP semester, dan IPK kumulatif.
 * Tersedia versi cetak yang memakai stylesheet @media print.
 */
class Khs extends BaseController
{
    public function index()
    {
        $mhs = $this->mahasiswaSaya();

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'))
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $krsModel = new KrsModel();

        return view('mahasiswa/khs', [
            'title'  => 'Kartu Hasil Studi',
            'mhs'    => $mhs,
            'rekap'  => $krsModel->rekapPerSemester((int) $mhs['id']),
            'ipk'    => $krsModel->ipk((int) $mhs['id']),
        ]);
    }

    public function cetak()
    {
        $mhs = $this->mahasiswaSaya();

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'));
        }

        $krsModel = new KrsModel();

        return view('mahasiswa/cetak_khs', [
            'title' => 'Cetak KHS',
            'mhs'   => $mhs,
            'rekap' => $krsModel->rekapPerSemester((int) $mhs['id']),
            'ipk'   => $krsModel->ipk((int) $mhs['id']),
        ]);
    }

    private function mahasiswaSaya(): ?array
    {
        return (new MahasiswaModel())->milikUser((int) session()->get('user_id'));
    }
}
