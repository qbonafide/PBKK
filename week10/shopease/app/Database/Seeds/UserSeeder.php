<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Empat akun demo: 1 admin, 1 operator, 2 customer.
 * Semua memakai password yang sama untuk praktikum: Password123!
 * Password disimpan sebagai hash (password_hash), bukan teks biasa.
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        $now      = date('Y-m-d H:i:s');

        $users = [
            [
                'name'          => 'Aditya Pratama',
                'email'         => 'admin@shopease.test',
                'password_hash' => $password,
                'role'          => 'admin',
                'phone'         => '081200000001',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'name'          => 'Rina Kusuma',
                'email'         => 'operator@shopease.test',
                'password_hash' => $password,
                'role'          => 'operator',
                'phone'         => '081200000002',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'name'          => 'Bagas Saputra',
                'email'         => 'bagas@shopease.test',
                'password_hash' => $password,
                'role'          => 'customer',
                'phone'         => '081200000003',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'name'          => 'Nadia Rahmawati',
                'email'         => 'nadia@shopease.test',
                'password_hash' => $password,
                'role'          => 'customer',
                'phone'         => '081200000004',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
