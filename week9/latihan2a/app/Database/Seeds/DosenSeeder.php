<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Lima dosen yang terhubung ke akun dosen1..dosen5.
 */
class DosenSeeder extends Seeder
{
    public function run()
    {
        $user = [];
        foreach ($this->db->table('users')->where('role', 'dosen')->get()->getResultArray() as $row) {
            $user[$row['username']] = (int) $row['id'];
        }

        $data = [
            [
                'user_id' => $user['dosen1'], 'nip' => '198501012010011001',
                'nama' => 'Dr. Ratna Kusuma, M.Kom.',
                'email' => 'ratna@siakad.test', 'no_hp' => '081200000101',
            ],
            [
                'user_id' => $user['dosen2'], 'nip' => '198703152011011002',
                'nama' => 'Bambang Wijaya, S.Kom., M.T.',
                'email' => 'bambang@siakad.test', 'no_hp' => '081200000102',
            ],
            [
                'user_id' => $user['dosen3'], 'nip' => '199001202012012003',
                'nama' => 'Sri Handayani, S.T., M.Kom.',
                'email' => 'sri@siakad.test', 'no_hp' => '081200000103',
            ],
            [
                'user_id' => $user['dosen4'], 'nip' => '198812102013011004',
                'nama' => 'Agus Setiawan, S.Kom., M.Cs.',
                'email' => 'agus@siakad.test', 'no_hp' => '081200000104',
            ],
            [
                'user_id' => $user['dosen5'], 'nip' => '199205052015012005',
                'nama' => 'Retno Wulandari, S.Kom., M.Kom.',
                'email' => 'retno@siakad.test', 'no_hp' => '081200000105',
            ],
        ];

        $this->db->table('dosen')->truncate();
        $this->db->table('dosen')->insertBatch($data);
    }
}
