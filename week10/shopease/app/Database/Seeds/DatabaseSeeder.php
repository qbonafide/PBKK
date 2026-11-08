<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder utama: dipanggil dengan `php spark db:seed DatabaseSeeder`.
 * Urutan penting karena ada relasi foreign key.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('CategorySeeder');
        $this->call('ProductSeeder');
        $this->call('AddressSeeder');
    }
}
