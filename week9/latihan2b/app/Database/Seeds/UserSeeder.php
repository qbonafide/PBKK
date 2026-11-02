<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Akun demo ThriftIn:
 *  - admin / admin123
 *  - pembeli1 / pembeli123
 *  - pembeli2 / pembeli123
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'     => 'Christina Tan',
                'username' => 'admin',
                'email'    => 'admin@thriftin.test',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
                'no_hp'    => '081200001111',
                'alamat'   => 'Jl. Kaliurang KM 5, Sleman, Yogyakarta',
            ],
            [
                'nama'     => 'Rani Puspitasari',
                'username' => 'pembeli1',
                'email'    => 'rani@contoh.test',
                'password' => password_hash('pembeli123', PASSWORD_DEFAULT),
                'role'     => 'pembeli',
                'no_hp'    => '081234560001',
                'alamat'   => 'Jl. Merdeka No. 10, Bandung, Jawa Barat 40111',
            ],
            [
                'nama'     => 'Fajar Hidayat',
                'username' => 'pembeli2',
                'email'    => 'fajar@contoh.test',
                'password' => password_hash('pembeli123', PASSWORD_DEFAULT),
                'role'     => 'pembeli',
                'no_hp'    => '081234560002',
                'alamat'   => 'Jl. Diponegoro No. 21, Semarang, Jawa Tengah 50231',
            ],
        ];

        $this->db->table('users')->truncate();
        $this->db->table('users')->insertBatch($data);
    }
}
