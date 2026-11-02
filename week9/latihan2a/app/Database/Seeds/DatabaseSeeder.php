<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Urutan seed mengikuti relasi foreign key:
 * users -> prodi -> dosen -> mahasiswa -> matakuliah -> krs.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // TRUNCATE gagal jika tabel direferensikan foreign key, jadi pengecekan
        // foreign key dimatikan sementara selama proses seed.
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        $this->call('UserSeeder');
        $this->call('ProdiSeeder');
        $this->call('DosenSeeder');
        $this->call('MahasiswaSeeder');
        $this->call('MatakuliahSeeder');
        $this->call('KrsSeeder');

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
