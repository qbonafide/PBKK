<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Empat kategori produk.
 */
class CategorySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $categories = [
            [
                'name'        => 'Elektronik',
                'slug'        => 'elektronik',
                'description' => 'Perangkat audio, wearable, dan aksesori komputer untuk kebutuhan harian.',
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Fashion',
                'slug'        => 'fashion',
                'description' => 'Pakaian kasual dan alas kaki dengan bahan yang nyaman dipakai.',
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Aksesoris',
                'slug'        => 'aksesoris',
                'description' => 'Pelengkap gaya seperti topi, tas, dompet, dan kacamata.',
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Rumah Tangga',
                'slug'        => 'rumah-tangga',
                'description' => 'Perlengkapan rumah dan alat tulis meja yang praktis.',
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);
    }
}
