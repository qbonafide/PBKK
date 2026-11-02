<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder akun demo. Password di-hash dengan password_hash() (bcrypt),
 * sama seperti yang dipakai Auth::attempt() saat login.
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'     => 'Christina Tan',
                'username' => 'admin',
                'email'    => 'admin@kosku.test',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'nama'     => 'Rina Pratiwi',
                'username' => 'penghuni1',
                'email'    => 'rina@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                'nama'     => 'Bagas Saputra',
                'username' => 'penghuni2',
                'email'    => 'bagas@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                'nama'     => 'Siti Aminah',
                'username' => 'penghuni3',
                'email'    => 'siti@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                'nama'     => 'Dimas Prakoso',
                'username' => 'penghuni4',
                'email'    => 'dimas@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                'nama'     => 'Nadia Ayu',
                'username' => 'penghuni5',
                'email'    => 'nadia@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                'nama'     => 'Yoga Pratama',
                'username' => 'penghuni6',
                'email'    => 'yoga@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
            [
                // Akun ini sengaja belum punya data penghuni, dipakai untuk
                // mendemokan alur "tambah penghuni" di panel admin.
                'nama'     => 'Dewi Lestari',
                'username' => 'penghuni7',
                'email'    => 'dewi@kosku.test',
                'password' => password_hash('penghuni123', PASSWORD_DEFAULT),
                'role'     => 'penghuni',
            ],
        ];

        $this->db->table('users')->truncate();
        $this->db->table('users')->insertBatch($data);
    }
}
