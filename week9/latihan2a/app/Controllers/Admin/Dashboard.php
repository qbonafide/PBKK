<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;
use App\Models\ProdiModel;

/**
 * Dashboard admin SIAKAD: jumlah master data + daftar KRS terbaru.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $krsTerbaru = $db->table('krs')
            ->select('krs.*, mahasiswa.nim, mahasiswa.nama AS nama_mahasiswa,
                      matakuliah.kode, matakuliah.nama AS nama_matakuliah, matakuliah.sks')
            ->join('mahasiswa', 'mahasiswa.id = krs.mahasiswa_id', 'left')
            ->join('matakuliah', 'matakuliah.id = krs.matakuliah_id', 'left')
            ->orderBy('krs.created_at', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $sebaranProdi = $db->table('mahasiswa')
            ->select('prodi.nama, COUNT(mahasiswa.id) AS jumlah')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->groupBy('prodi.id')
            ->orderBy('prodi.nama', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'title'         => 'Dashboard Admin',
            'jumlahProdi'   => (new ProdiModel())->countAllResults(),
            'jumlahDosen'   => (new DosenModel())->countAllResults(),
            'jumlahMhs'     => (new MahasiswaModel())->countAllResults(),
            'jumlahMatkul'  => (new MatakuliahModel())->countAllResults(),
            'krsTerbaru'    => $krsTerbaru,
            'sebaranProdi'  => $sebaranProdi,
        ]);
    }
}
