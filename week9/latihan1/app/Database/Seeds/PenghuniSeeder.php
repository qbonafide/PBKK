<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder 6 penghuni aktif. Setiap penghuni terhubung ke satu akun user
 * (relasi 1-1) dan satu kamar yang statusnya "terisi".
 */
class PenghuniSeeder extends Seeder
{
    public function run()
    {
        $kamar = [];
        foreach ($this->db->table('kamar')->get()->getResultArray() as $row) {
            $kamar[$row['nomor_kamar']] = (int) $row['id'];
        }

        $user = [];
        foreach ($this->db->table('users')->get()->getResultArray() as $row) {
            $user[$row['username']] = (int) $row['id'];
        }

        $data = [
            [
                'user_id' => $user['penghuni1'], 'kamar_id' => $kamar['A2'],
                'nama' => 'Rina Pratiwi', 'no_hp' => '081234567801',
                'alamat_asal' => 'Jl. Kaliurang KM 8, Sleman, DI Yogyakarta',
                'pekerjaan' => 'Mahasiswa', 'foto_ktp' => 'ktp-demo-1.png',
                'tanggal_masuk' => '2025-08-01', 'status' => 'aktif',
            ],
            [
                'user_id' => $user['penghuni2'], 'kamar_id' => $kamar['A3'],
                'nama' => 'Bagas Saputra', 'no_hp' => '081234567802',
                'alamat_asal' => 'Jl. Diponegoro 12, Semarang, Jawa Tengah',
                'pekerjaan' => 'Karyawan swasta', 'foto_ktp' => 'ktp-demo-2.png',
                'tanggal_masuk' => '2025-09-15', 'status' => 'aktif',
            ],
            [
                'user_id' => $user['penghuni3'], 'kamar_id' => $kamar['B1'],
                'nama' => 'Siti Aminah', 'no_hp' => '081234567803',
                'alamat_asal' => 'Jl. Ahmad Yani 45, Surabaya, Jawa Timur',
                'pekerjaan' => 'Guru', 'foto_ktp' => 'ktp-demo-3.png',
                'tanggal_masuk' => '2025-10-01', 'status' => 'aktif',
            ],
            [
                'user_id' => $user['penghuni4'], 'kamar_id' => $kamar['B2'],
                'nama' => 'Dimas Prakoso', 'no_hp' => '081234567804',
                'alamat_asal' => 'Jl. Merdeka 7, Bandung, Jawa Barat',
                'pekerjaan' => 'Programmer', 'foto_ktp' => 'ktp-demo-4.png',
                'tanggal_masuk' => '2026-01-10', 'status' => 'aktif',
            ],
            [
                'user_id' => $user['penghuni5'], 'kamar_id' => $kamar['C1'],
                'nama' => 'Nadia Ayu', 'no_hp' => '081234567805',
                'alamat_asal' => 'Jl. Sudirman 21, Malang, Jawa Timur',
                'pekerjaan' => 'Mahasiswa', 'foto_ktp' => 'ktp-demo-5.png',
                'tanggal_masuk' => '2026-02-01', 'status' => 'aktif',
            ],
            [
                'user_id' => $user['penghuni6'], 'kamar_id' => $kamar['C2'],
                'nama' => 'Yoga Pratama', 'no_hp' => '081234567806',
                'alamat_asal' => 'Jl. Gajah Mada 33, Denpasar, Bali',
                'pekerjaan' => 'Desainer grafis', 'foto_ktp' => 'ktp-demo-6.png',
                'tanggal_masuk' => '2026-03-01', 'status' => 'aktif',
            ],
        ];

        $this->db->table('penghuni')->truncate();
        $this->db->table('penghuni')->insertBatch($data);
    }
}
