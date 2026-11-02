<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Tiga program studi.
 */
class ProdiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode' => 'IF', 'nama' => 'Informatika'],
            ['kode' => 'SI', 'nama' => 'Sistem Informasi'],
            ['kode' => 'TI', 'nama' => 'Teknologi Informasi'],
        ];

        $this->db->table('prodi')->truncate();
        $this->db->table('prodi')->insertBatch($data);
    }
}
