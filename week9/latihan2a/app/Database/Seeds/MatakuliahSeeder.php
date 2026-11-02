<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dua belas mata kuliah dengan jadwal, kuota, dan dosen pengampu.
 */
class MatakuliahSeeder extends Seeder
{
    public function run()
    {
        $prodi = [];
        foreach ($this->db->table('prodi')->get()->getResultArray() as $row) {
            $prodi[$row['kode']] = (int) $row['id'];
        }

        $dosen = [];
        foreach ($this->db->table('dosen')->orderBy('id', 'ASC')->get()->getResultArray() as $row) {
            $dosen[] = (int) $row['id'];
        }

        $data = [
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[0], 'kode' => 'IF101',
                'nama' => 'Pengantar Teknologi Informasi', 'sks' => 3, 'semester' => 1,
                'kuota' => 40, 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Ruang A.101',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[1], 'kode' => 'IF102',
                'nama' => 'Algoritma dan Pemrograman', 'sks' => 4, 'semester' => 1,
                'kuota' => 40, 'hari' => 'Selasa', 'jam_mulai' => '08:00', 'jam_selesai' => '11:20',
                'ruang' => 'Lab Pemrograman 1',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[2], 'kode' => 'IF103',
                'nama' => 'Matematika Diskrit', 'sks' => 3, 'semester' => 1,
                'kuota' => 40, 'hari' => 'Rabu', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30',
                'ruang' => 'Ruang A.102',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[1], 'kode' => 'IF201',
                'nama' => 'Struktur Data', 'sks' => 4, 'semester' => 2,
                'kuota' => 35, 'hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '16:20',
                'ruang' => 'Lab Pemrograman 2',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[3], 'kode' => 'IF202',
                'nama' => 'Basis Data', 'sks' => 3, 'semester' => 2,
                'kuota' => 35, 'hari' => 'Selasa', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30',
                'ruang' => 'Lab Basis Data',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[0], 'kode' => 'IF203',
                'nama' => 'Pemrograman Web', 'sks' => 3, 'semester' => 2,
                'kuota' => 35, 'hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Lab Web',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[4], 'kode' => 'IF301',
                'nama' => 'Pemrograman Berbasis Kerangka Kerja', 'sks' => 3, 'semester' => 3,
                'kuota' => 30, 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Lab RPL 1',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[2], 'kode' => 'IF302',
                'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 3,
                'kuota' => 30, 'hari' => 'Rabu', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Ruang B.201',
            ],
            [
                'prodi_id' => $prodi['IF'], 'dosen_id' => $dosen[3], 'kode' => 'IF303',
                'nama' => 'Jaringan Komputer', 'sks' => 3, 'semester' => 3,
                'kuota' => 30, 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30',
                'ruang' => 'Lab Jaringan',
            ],
            [
                'prodi_id' => $prodi['SI'], 'dosen_id' => $dosen[0], 'kode' => 'SI201',
                'nama' => 'Sistem Informasi Manajemen', 'sks' => 3, 'semester' => 2,
                'kuota' => 30, 'hari' => 'Jumat', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Ruang C.301',
            ],
            [
                'prodi_id' => $prodi['SI'], 'dosen_id' => $dosen[4], 'kode' => 'SI301',
                'nama' => 'Analisis Proses Bisnis', 'sks' => 3, 'semester' => 3,
                'kuota' => 30, 'hari' => 'Selasa', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Ruang C.302',
            ],
            [
                'prodi_id' => $prodi['TI'], 'dosen_id' => $dosen[1], 'kode' => 'TI201',
                'nama' => 'Multimedia Interaktif', 'sks' => 3, 'semester' => 2,
                'kuota' => 30, 'hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
                'ruang' => 'Lab Multimedia',
            ],
        ];

        $this->db->table('matakuliah')->truncate();
        $this->db->table('matakuliah')->insertBatch($data);
    }
}
