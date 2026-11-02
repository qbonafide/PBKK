<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Akun demo SIAKAD:
 *  - admin / admin123
 *  - dosen1..dosen5 / dosen123
 *  - 2025001 / mhs123 (mahasiswa contoh dengan KHS lengkap)
 *  - 2025002..2025020 dengan password sama dengan NIM
 *
 * Catatan: akun yang dibuat lewat panel admin memakai aturan berbeda
 * (username = NIP/NIM, password awal = NIP/NIM).
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'     => 'Christina Tan',
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
        ];

        $namaDosen = [
            'dosen1' => 'Dr. Ratna Kusuma, M.Kom.',
            'dosen2' => 'Bambang Wijaya, S.Kom., M.T.',
            'dosen3' => 'Sri Handayani, S.T., M.Kom.',
            'dosen4' => 'Agus Setiawan, S.Kom., M.Cs.',
            'dosen5' => 'Retno Wulandari, S.Kom., M.Kom.',
        ];

        foreach ($namaDosen as $username => $nama) {
            $data[] = [
                'nama'     => $nama,
                'username' => $username,
                'password' => password_hash('dosen123', PASSWORD_DEFAULT),
                'role'     => 'dosen',
            ];
        }

        $namaMahasiswa = [
            '2025001' => 'Andi Prasetyo',
            '2025002' => 'Bunga Lestari',
            '2025003' => 'Candra Nugroho',
            '2025004' => 'Dewi Anggraini',
            '2025005' => 'Eko Saputra',
            '2025006' => 'Fitri Ramadhani',
            '2025007' => 'Gilang Ramadhan',
            '2025008' => 'Hana Safitri',
            '2025009' => 'Irfan Maulana',
            '2025010' => 'Jihan Aulia',
            '2025011' => 'Kevin Ardiansyah',
            '2025012' => 'Laras Puspita',
            '2025013' => 'Muhammad Rizky',
            '2025014' => 'Nabila Zahra',
            '2025015' => 'Oscar Simanjuntak',
            '2025016' => 'Putri Maharani',
            '2025017' => 'Qori Ananda',
            '2025018' => 'Rafi Kurniawan',
            '2025019' => 'Sinta Permata',
            '2025020' => 'Taufik Hidayat',
        ];

        foreach ($namaMahasiswa as $nim => $nama) {
            // PHP mengubah kunci array numerik menjadi integer, jadi NIM
            // di-cast ke string lebih dulu sebelum dibandingkan.
            $nim          = (string) $nim;
            $passwordAwal = $nim === '2025001' ? 'mhs123' : $nim;

            $data[] = [
                'nama'     => $nama,
                'username' => $nim,
                'password' => password_hash($passwordAwal, PASSWORD_DEFAULT),
                'role'     => 'mahasiswa',
            ];
        }

        $this->db->table('users')->truncate();
        $this->db->table('users')->insertBatch($data);
    }
}
