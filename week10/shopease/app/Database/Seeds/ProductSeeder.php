<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 18 produk fiktif: 16 aktif dan 2 nonaktif
 * (produk nonaktif dipakai untuk menguji BR-002: tidak tampil di katalog).
 *
 * Gambar produk dibuat oleh docs/gen_placeholders.py dengan nama file = slug.png.
 */
class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = $this->db->table('categories')->get()->getResultArray();
        $categoryId = [];

        foreach ($categories as $category) {
            $categoryId[$category['slug']] = (int) $category['id'];
        }

        $base = strtotime('-18 days');

        $products = [
            ['elektronik', 'Wireless Headphone BT-100', 'Headphone Bluetooth over-ear dengan driver 40mm dan baterai tahan 24 jam.', 299000, 20, 'active'],
            ['elektronik', 'Smart Watch Fit S2', 'Jam tangan pintar dengan pemantau detak jantung, penghitung langkah, dan notifikasi pesan.', 449000, 12, 'active'],
            ['elektronik', 'Speaker Bluetooth Mini', 'Speaker portabel tahan air dengan suara bass yang tetap jernih.', 189000, 25, 'active'],
            ['elektronik', 'Power Bank 10000mAh', 'Pengisi daya portabel dengan dua port USB dan indikator kapasitas.', 159000, 30, 'active'],
            ['elektronik', 'Keyboard Mekanik TKL', 'Keyboard mekanik tanpa numpad dengan switch tactile dan lampu latar putih.', 549000, 8, 'inactive'],
            ['elektronik', 'Mouse Wireless Silent', 'Mouse nirkabel tanpa suara klik dengan sensitivitas yang dapat diatur.', 129000, 40, 'active'],
            ['fashion', 'Kaos Polos Premium', 'Kaos katun combed 30s dengan potongan regular fit untuk pemakaian harian.', 89000, 50, 'active'],
            ['fashion', 'Kemeja Flanel Lengan Panjang', 'Kemeja flanel motif kotak dengan bahan lembut untuk cuaca dingin.', 199000, 18, 'active'],
            ['fashion', 'Hoodie Oversize', 'Hoodie berbahan fleece dengan potongan longgar dan kantong kanguru.', 249000, 15, 'active'],
            ['fashion', 'Celana Chino Slim', 'Celana chino potongan slim dengan bahan stretch yang tidak mudah kusut.', 219000, 22, 'active'],
            ['fashion', 'Sneakers Casual Daily', 'Sepatu sneakers ringan dengan sol karet dan alas kaki yang empuk.', 349000, 14, 'active'],
            ['fashion', 'Jaket Bomber', 'Jaket bomber berbahan parasut dengan lapisan dalam yang hangat.', 289000, 10, 'inactive'],
            ['aksesoris', 'Topi Baseball Katun', 'Topi baseball dengan tali pengatur ukuran dan bahan katun yang menyerap keringat.', 79000, 35, 'active'],
            ['aksesoris', 'Tas Ransel Laptop 14 Inci', 'Ransel dengan kompartemen laptop 14 inci, kantong botol, dan bahan tahan air.', 329000, 12, 'active'],
            ['aksesoris', 'Dompet Kulit Sintetis', 'Dompet lipat dengan banyak slot kartu dan ruang kartu identitas.', 99000, 45, 'active'],
            ['aksesoris', 'Kacamata Anti UV', 'Kacamata dengan lensa anti UV400 dan frame ringan untuk aktivitas luar ruang.', 139000, 20, 'active'],
            ['rumah-tangga', 'Lampu Meja LED', 'Lampu meja LED dengan tiga tingkat kecerahan dan leher yang dapat ditekuk.', 119000, 28, 'active'],
            ['rumah-tangga', 'Botol Minum Termal 500ml', 'Botol stainless steel yang menjaga suhu minuman hingga 12 jam.', 149000, 33, 'active'],
        ];

        $rows = [];
        foreach ($products as $index => [$categorySlug, $name, $description, $price, $stock, $status]) {
            $slug      = url_title($name, '-', true);
            $createdAt = date('Y-m-d H:i:s', $base + ($index * 43200));

            $rows[] = [
                'category_id' => $categoryId[$categorySlug],
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'price'       => $price,
                'stock'       => $stock,
                'image'       => $slug . '.png',
                'status'      => $status,
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ];
        }

        $this->db->table('products')->insertBatch($rows);
    }
}
