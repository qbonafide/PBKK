<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Pesanan demo dengan status berbeda-beda agar dashboard admin, halaman
 * riwayat pembeli, dan halaman kelola pesanan tidak kosong.
 *
 * Stok produk ikut disesuaikan untuk pesanan yang masih menahan barang
 * (semua status kecuali dibatalkan), sama seperti perilaku checkout asli.
 */
class OrderSeeder extends Seeder
{
    public function run()
    {
        $pembeli = [];
        foreach ($this->db->table('users')->where('role', 'pembeli')->get()->getResultArray() as $row) {
            $pembeli[$row['username']] = (int) $row['id'];
        }

        $produk = [];
        foreach ($this->db->table('produk')->get()->getResultArray() as $row) {
            $produk[$row['slug']] = $row;
        }

        // [username, status, umur pesanan (hari), items[[slug, qty]], no_resi, bukti]
        $daftar = [
            ['pembeli1', 'selesai', 60, [
                ['jaket-denim-oversize', 1],
                ['topi-baseball-polos', 1],
            ], 'JNE8800112233', 'bukti-demo-1.png'],

            ['pembeli1', 'menunggu_verifikasi', 2, [
                ['sneakers-kanvas-putih', 1],
            ], null, 'bukti-demo-2.png'],

            ['pembeli1', 'menunggu_pembayaran', 0, [
                ['dress-midi-bunga-kecil', 1],
            ], null, null],

            ['pembeli2', 'diproses', 5, [
                ['kemeja-flanel-kotak-lengan-panjang', 1],
                ['celana-chino-slim-fit-khaki', 1],
            ], null, 'bukti-demo-3.png'],

            ['pembeli2', 'dikirim', 12, [
                ['blazer-formal-hitam', 1],
            ], 'JNE8800445566', 'bukti-demo-4.png'],

            ['pembeli2', 'dibatalkan', 35, [
                ['boots-kulit-ankle', 1],
            ], null, null],

            ['pembeli1', 'selesai', 40, [
                ['kaos-basic-cotton-combed-putih', 2],
            ], 'JNE8800778899', 'bukti-demo-5.png'],
        ];

        $this->db->table('order_items')->truncate();
        $this->db->table('orders')->truncate();

        foreach ($daftar as $i => [$username, $status, $umurHari, $items, $noResi, $bukti]) {
            $subtotal = 0;
            $baris    = [];

            foreach ($items as [$slug, $qty]) {
                $p          = $produk[$slug];
                $harga      = (int) $p['harga'];
                $subItem    = $harga * $qty;
                $subtotal  += $subItem;

                $baris[] = [
                    'produk_id'   => (int) $p['id'],
                    'nama_produk' => $p['nama'],
                    'harga'       => $harga,
                    'qty'         => $qty,
                    'subtotal'    => $subItem,
                ];
            }

            $ongkir = $subtotal >= 200000 ? 0 : 20000;
            $waktu  = date('Y-m-d H:i:s', strtotime('-' . $umurHari . ' days'));

            $this->db->table('orders')->insert([
                'user_id'       => $pembeli[$username],
                'invoice'       => 'TI-' . date('Ymd', strtotime($waktu)) . '-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'nama_penerima' => $username === 'pembeli1' ? 'Rani Puspitasari' : 'Fajar Hidayat',
                'no_hp'         => $username === 'pembeli1' ? '081234560001' : '081234560002',
                'alamat_kirim'  => $username === 'pembeli1'
                    ? 'Jl. Merdeka No. 10, Bandung, Jawa Barat 40111'
                    : 'Jl. Diponegoro No. 21, Semarang, Jawa Tengah 50231',
                'catatan'       => $status === 'diproses' ? 'Tolong dibungkus rapi, untuk hadiah.' : null,
                'subtotal'      => $subtotal,
                'ongkir'        => $ongkir,
                'total'         => $subtotal + $ongkir,
                'status'        => $status,
                'bukti_bayar'   => $bukti,
                'no_resi'       => $noResi,
                'expired_at'    => date('Y-m-d H:i:s', strtotime($waktu . ' +24 hours')),
                'created_at'    => $waktu,
                'updated_at'    => $waktu,
            ]);

            $orderId = (int) $this->db->insertID();

            foreach ($baris as $b) {
                $b['order_id'] = $orderId;
                $this->db->table('order_items')->insert($b);
            }

            // Pesanan yang tidak dibatalkan menahan stok, jadi stok produk dikurangi.
            if ($status !== 'dibatalkan') {
                foreach ($baris as $b) {
                    $this->db->table('produk')
                        ->set('stok', 'stok - ' . (int) $b['qty'], false)
                        ->where('id', (int) $b['produk_id'])
                        ->update();
                }
            }
        }
    }
}
