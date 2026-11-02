<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Urutan seed mengikuti relasi foreign key:
 * users -> kategori -> produk -> orders -> order_items.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // TRUNCATE pada tabel berelasi gagal bila foreign key masih aktif.
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        $this->call('UserSeeder');
        $this->call('KategoriSeeder');
        $this->call('ProdukSeeder');
        $this->call('OrderSeeder');

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
