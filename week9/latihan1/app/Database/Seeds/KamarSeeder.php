<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder 9 kamar dengan campuran status supaya dashboard dan halaman publik
 * langsung punya data yang layak difoto.
 */
class KamarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nomor_kamar' => 'A1', 'slug' => 'kamar-a1', 'tipe' => 'standar',
                'harga_bulanan' => 750000, 'luas' => '3x3', 'status' => 'kosong',
                'fasilitas' => 'Kasur, lemari, meja belajar, kamar mandi dalam',
                'foto' => 'kamar-a1.png',
            ],
            [
                'nomor_kamar' => 'A2', 'slug' => 'kamar-a2', 'tipe' => 'standar',
                'harga_bulanan' => 750000, 'luas' => '3x3', 'status' => 'terisi',
                'fasilitas' => 'Kasur, lemari, meja belajar, kamar mandi dalam',
                'foto' => 'kamar-a2.png',
            ],
            [
                'nomor_kamar' => 'A3', 'slug' => 'kamar-a3', 'tipe' => 'ac',
                'harga_bulanan' => 1100000, 'luas' => '3x4', 'status' => 'terisi',
                'fasilitas' => 'AC, kasur spring bed, lemari, meja, kamar mandi dalam',
                'foto' => 'kamar-a3.png',
            ],
            [
                'nomor_kamar' => 'B1', 'slug' => 'kamar-b1', 'tipe' => 'ac',
                'harga_bulanan' => 1200000, 'luas' => '3x4', 'status' => 'terisi',
                'fasilitas' => 'AC, kasur spring bed, lemari, meja, kamar mandi dalam, water heater',
                'foto' => 'kamar-b1.png',
            ],
            [
                'nomor_kamar' => 'B2', 'slug' => 'kamar-b2', 'tipe' => 'premium',
                'harga_bulanan' => 1750000, 'luas' => '4x4', 'status' => 'terisi',
                'fasilitas' => 'AC, kasur king size, TV, lemari, meja, kamar mandi dalam, water heater, balkon',
                'foto' => 'kamar-b2.png',
            ],
            [
                'nomor_kamar' => 'B3', 'slug' => 'kamar-b3', 'tipe' => 'premium',
                'harga_bulanan' => 1800000, 'luas' => '4x4', 'status' => 'perbaikan',
                'fasilitas' => 'AC, kasur king size, TV, lemari, meja, kamar mandi dalam, water heater',
                'foto' => 'kamar-b3.png',
            ],
            [
                'nomor_kamar' => 'C1', 'slug' => 'kamar-c1', 'tipe' => 'standar',
                'harga_bulanan' => 650000, 'luas' => '3x3', 'status' => 'terisi',
                'fasilitas' => 'Kasur, lemari, meja belajar, kamar mandi luar',
                'foto' => 'kamar-c1.png',
            ],
            [
                'nomor_kamar' => 'C2', 'slug' => 'kamar-c2', 'tipe' => 'ac',
                'harga_bulanan' => 1050000, 'luas' => '3x4', 'status' => 'terisi',
                'fasilitas' => 'AC, kasur, lemari, meja, kamar mandi dalam',
                'foto' => 'kamar-c2.png',
            ],
            [
                'nomor_kamar' => 'C3', 'slug' => 'kamar-c3', 'tipe' => 'premium',
                'harga_bulanan' => 1600000, 'luas' => '4x4', 'status' => 'kosong',
                'fasilitas' => 'AC, kasur king size, TV, lemari, meja, kamar mandi dalam, water heater',
                'foto' => 'kamar-c3.png',
            ],
        ];

        $this->db->table('kamar')->truncate();
        $this->db->table('kamar')->insertBatch($data);
    }
}
