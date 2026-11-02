<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dua puluh mahasiswa: 12 Informatika, 5 Sistem Informasi, 3 Teknologi Informasi.
 * Dosen wali dibagi bergiliran ke lima dosen.
 */
class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        $user = [];
        foreach ($this->db->table('users')->where('role', 'mahasiswa')->get()->getResultArray() as $row) {
            $user[$row['username']] = (int) $row['id'];
        }

        $prodi = [];
        foreach ($this->db->table('prodi')->get()->getResultArray() as $row) {
            $prodi[$row['kode']] = (int) $row['id'];
        }

        $dosenIds = array_column(
            $this->db->table('dosen')->orderBy('id', 'ASC')->get()->getResultArray(),
            'id'
        );

        $daftar = [
            ['2025001', 'Andi Prasetyo', 'IF'],
            ['2025002', 'Bunga Lestari', 'IF'],
            ['2025003', 'Candra Nugroho', 'IF'],
            ['2025004', 'Dewi Anggraini', 'IF'],
            ['2025005', 'Eko Saputra', 'IF'],
            ['2025006', 'Fitri Ramadhani', 'IF'],
            ['2025007', 'Gilang Ramadhan', 'IF'],
            ['2025008', 'Hana Safitri', 'IF'],
            ['2025009', 'Irfan Maulana', 'IF'],
            ['2025010', 'Jihan Aulia', 'IF'],
            ['2025011', 'Kevin Ardiansyah', 'IF'],
            ['2025012', 'Laras Puspita', 'IF'],
            ['2025013', 'Muhammad Rizky', 'SI'],
            ['2025014', 'Nabila Zahra', 'SI'],
            ['2025015', 'Oscar Simanjuntak', 'SI'],
            ['2025016', 'Putri Maharani', 'SI'],
            ['2025017', 'Qori Ananda', 'SI'],
            ['2025018', 'Rafi Kurniawan', 'TI'],
            ['2025019', 'Sinta Permata', 'TI'],
            ['2025020', 'Taufik Hidayat', 'TI'],
        ];

        $data = [];
        foreach ($daftar as $i => [$nim, $nama, $kodeProdi]) {
            $data[] = [
                'user_id'       => $user[$nim],
                'prodi_id'      => $prodi[$kodeProdi],
                'dosen_wali_id' => $dosenIds[$i % count($dosenIds)],
                'nim'           => $nim,
                'nama'          => $nama,
                'angkatan'      => 2025,
                'alamat'        => 'Jl. Pendidikan No. ' . ($i + 1) . ', Yogyakarta',
                'no_hp'         => '0822' . str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT),
            ];
        }

        $this->db->table('mahasiswa')->truncate();
        $this->db->table('mahasiswa')->insertBatch($data);
    }
}
