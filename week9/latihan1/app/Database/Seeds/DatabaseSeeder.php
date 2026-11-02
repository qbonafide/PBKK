<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Urutan seed penting: users -> kamar -> penghuni -> pembayaran,
 * karena tabel berikutnya memakai foreign key dari tabel sebelumnya.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Truncate pada tabel yang punya relasi foreign key gagal jika ada baris
        // anak, jadi pengecekan FK dimatikan sementara selama proses seed.
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        $this->call('UserSeeder');
        $this->call('KamarSeeder');
        $this->call('PenghuniSeeder');
        $this->call('PembayaranSeeder');

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
