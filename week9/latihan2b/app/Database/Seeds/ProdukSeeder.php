<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 26 produk thrift dengan foto lokal hasil generator GD
 * (scripts/buat-placeholder-thriftin.php), bukan hotlink ke situs lain.
 *
 * Stok awal di sini adalah stok sebelum pesanan demo dibuat; OrderSeeder
 * mengurangi stok untuk pesanan yang masih menahan barang.
 */
class ProdukSeeder extends Seeder
{
    public function run()
    {
        $kategori = [];
        foreach ($this->db->table('kategori')->get()->getResultArray() as $row) {
            $kategori[$row['slug']] = (int) $row['id'];
        }

        // [nama, kategori, brand, ukuran, kondisi, harga, stok]
        $daftar = [
            ['Kemeja Flanel Kotak Lengan Panjang', 'atasan', 'Uniqlo', 'M', 'bagus', 95000, 1],
            ['Kaos Basic Cotton Combed Putih', 'atasan', 'H&M', 'L', 'like_new', 55000, 8],
            ['Blouse Kantor Warna Kream', 'atasan', 'Zara', 'S', 'bagus', 110000, 1],
            ['Polo Shirt Navy Stripe', 'atasan', 'Lacoste', 'L', 'ada_minus', 85000, 1],
            ['Sweater Rajut Abu Melange', 'atasan', 'Mango', 'M', 'bagus', 120000, 4],
            ['Celana Chino Slim Fit Khaki', 'bawahan', 'Uniqlo', '32', 'bagus', 135000, 1],
            ['Jeans Straight Cut Denim Biru', 'bawahan', 'Levi\'s', '30', 'ada_minus', 195000, 1],
            ['Rok Plisket Midi Hitam', 'bawahan', 'Bershka', 'M', 'like_new', 99000, 1],
            ['Celana Jogger Fleece Hitam', 'bawahan', 'Adidas', 'L', 'bagus', 145000, 1],
            ['Celana Pendek Cargo Army', 'bawahan', 'Terranova', 'M', 'bagus', 89000, 5],
            ['Jaket Denim Oversize', 'outer', 'Levi\'s', 'L', 'bagus', 245000, 1],
            ['Blazer Formal Hitam', 'outer', 'Zara', 'M', 'like_new', 210000, 1],
            ['Cardigan Knit Cokelat', 'outer', 'H&M', 'S', 'bagus', 105000, 1],
            ['Jaket Bomber Parasut', 'outer', 'Adidas', 'M', 'ada_minus', 130000, 1],
            ['Coat Wol Abu Tua', 'outer', 'Uniqlo', 'L', 'bagus', 265000, 1],
            ['Dress Midi Bunga Kecil', 'dress', 'Dorothy Perkins', 'S', 'like_new', 175000, 1],
            ['Dress Kerja Lengan Panjang', 'dress', 'Mango', 'M', 'bagus', 160000, 1],
            ['Dress Pesta Hitam Polos', 'dress', 'Zara', 'S', 'bagus', 230000, 1],
            ['Sneakers Kanvas Putih', 'sepatu', 'Converse', '42', 'ada_minus', 185000, 1],
            ['Sepatu Formal Kulit Cokelat', 'sepatu', 'Clarks', '41', 'bagus', 275000, 1],
            ['Sandal Slide Kulit', 'sepatu', 'Havaianas', '40', 'bagus', 75000, 4],
            ['Boots Kulit Ankle', 'sepatu', 'Dr. Martens', '39', 'ada_minus', 320000, 1],
            ['Tote Bag Kanvas', 'aksesoris', 'Uniqlo', 'All size', 'like_new', 65000, 5],
            ['Topi Baseball Polos', 'aksesoris', 'New Era', 'All size', 'bagus', 85000, 1],
            ['Ikat Pinggang Kulit', 'aksesoris', 'Bonia', 'M', 'bagus', 95000, 1],
            ['Scarf Motif Geometris', 'aksesoris', 'Hermes (preloved)', 'All size', 'like_new', 150000, 1],
        ];

        $data = [];
        foreach ($daftar as $i => [$nama, $slugKategori, $brand, $ukuran, $kondisi, $harga, $stok]) {
            $data[] = [
                'kategori_id' => $kategori[$slugKategori],
                'nama'        => $nama,
                'slug'        => url_title($nama, '-', true),
                'brand'       => $brand,
                'ukuran'      => $ukuran,
                'kondisi'     => $kondisi,
                'deskripsi'   => 'Barang preloved pilihan. Sudah dicuci bersih sebelum difoto. '
                    . 'Kondisi sesuai keterangan, silakan cek foto untuk detail jahitan dan warna.',
                'harga'       => $harga,
                'stok'        => $stok,
                'foto'        => sprintf('produk-%02d.png', $i + 1),
                'aktif'       => 1,
            ];
        }

        $this->db->table('produk')->truncate();
        $this->db->table('produk')->insertBatch($data);
    }
}
