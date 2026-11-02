<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;

/**
 * Dashboard mahasiswa: IPK, SKS yang diambil pada tahun ajaran aktif,
 * dan status KRS.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $mhs = (new MahasiswaModel())->milikUser((int) session()->get('user_id'));

        if ($mhs === null) {
            return view('mahasiswa/dashboard', [
                'title'   => 'Dashboard Mahasiswa',
                'mhs'     => null,
                'prodi'   => null,
                'ipk'     => ['sks' => 0, 'mutu' => 0.0, 'ipk' => 0.0],
                'sksAmbil' => 0,
                'krsAktif' => [],
            ]);
        }

        $tahunAjaran = (string) config('Akademik')->tahunAjaranAktif;
        $krsModel    = new KrsModel();
        $prodi       = (new ProdiModel())->find((int) $mhs['prodi_id']);
        $krsAktif    = $krsModel->barisMahasiswa((int) $mhs['id'], $tahunAjaran);

        return view('mahasiswa/dashboard', [
            'title'    => 'Dashboard Mahasiswa',
            'mhs'      => $mhs,
            'prodi'    => $prodi,
            'ipk'      => $krsModel->ipk((int) $mhs['id']),
            'sksAmbil' => $krsModel->totalSks((int) $mhs['id'], $tahunAjaran),
            'krsAktif' => $krsAktif,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    public function profil()
    {
        $mhs = (new MahasiswaModel())->milikUser((int) session()->get('user_id'));

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'))
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $db = db_connect();

        $lengkap = $db->table('mahasiswa')
            ->select('mahasiswa.*, prodi.nama AS nama_prodi, dosen.nama AS nama_wali')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->where('mahasiswa.id', (int) $mhs['id'])
            ->get()
            ->getRowArray();

        return view('mahasiswa/profil', [
            'title' => 'Profil Saya',
            'mhs'   => $lengkap,
        ]);
    }
}
