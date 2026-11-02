<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MatakuliahModel;

/**
 * Dashboard dosen: jumlah kelas yang diampu, jumlah mahasiswa peserta,
 * dan daftar kelas beserta jumlah peserta pada tahun ajaran terbaru.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $dosen = (new DosenModel())->milikUser((int) session()->get('user_id'));

        if ($dosen === null) {
            return view('dosen/dashboard', [
                'title'      => 'Dashboard Dosen',
                'dosen'      => null,
                'kelas'      => [],
                'totalKelas' => 0,
                'totalMhs'   => 0,
            ]);
        }

        $krsModel = new KrsModel();
        $kelas    = (new MatakuliahModel())->milikDosen((int) $dosen['id']);
        $totalMhs = 0;

        foreach ($kelas as $index => $baris) {
            $daftarTa = $krsModel->tahunAjaranKelas((int) $baris['id']);
            $peserta  = 0;

            foreach ($daftarTa as $ta) {
                $peserta += (int) $ta['jumlah'];
            }

            $kelas[$index]['peserta']      = $peserta;
            $kelas[$index]['tahun_ajaran'] = $daftarTa === [] ? '-' : $daftarTa[0]['tahun_ajaran'];
            $totalMhs += $peserta;
        }

        return view('dosen/dashboard', [
            'title'      => 'Dashboard Dosen',
            'dosen'      => $dosen,
            'kelas'      => $kelas,
            'totalKelas' => count($kelas),
            'totalMhs'   => $totalMhs,
        ]);
    }
}
