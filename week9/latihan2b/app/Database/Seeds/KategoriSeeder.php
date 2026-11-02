<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Enam kategori sesuai ketentuan tugas.
 */
class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama' => 'Atasan', 'slug' => 'atasan'],
            ['nama' => 'Bawahan', 'slug' => 'bawahan'],
            ['nama' => 'Outer', 'slug' => 'outer'],
            ['nama' => 'Dress', 'slug' => 'dress'],
            ['nama' => 'Sepatu', 'slug' => 'sepatu'],
            ['nama' => 'Aksesoris', 'slug' => 'aksesoris'],
        ];

        $this->db->table('kategori')->truncate();
        $this->db->table('kategori')->insertBatch($data);
    }
}
