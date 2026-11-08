<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Satu alamat contoh untuk tiap customer (data fiktif).
 */
class AddressSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $customers = $this->db->table('users')
            ->where('role', 'customer')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if ($customers === []) {
            return;
        }

        $samples = [
            [
                'recipient_name' => 'Bagas Saputra',
                'phone'          => '081200000003',
                'address'        => 'Jl. Melati Raya No. 21, Kelurahan Sukamaju, Kecamatan Cilandak',
                'city'           => 'Jakarta Selatan',
                'province'       => 'DKI Jakarta',
                'postal_code'    => '12430',
                'is_default'     => 1,
            ],
            [
                'recipient_name' => 'Nadia Rahmawati',
                'phone'          => '081200000004',
                'address'        => 'Jl. Cendana No. 8, Kelurahan Cigadung, Kecamatan Coblong',
                'city'           => 'Bandung',
                'province'       => 'Jawa Barat',
                'postal_code'    => '40191',
                'is_default'     => 1,
            ],
        ];

        $rows = [];
        foreach ($customers as $index => $customer) {
            $sample = $samples[$index] ?? $samples[0];

            $rows[] = array_merge($sample, [
                'user_id'    => (int) $customer['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->db->table('addresses')->insertBatch($rows);
    }
}
