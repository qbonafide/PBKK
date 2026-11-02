<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder tagihan tiga bulan terakhir dengan status bervariasi
 * (lunas, menunggu verifikasi, ditolak, belum bayar) agar grafik dan
 * tabel di dashboard tidak kosong.
 */
class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $penghuni = [];
        foreach ($this->db->table('penghuni')->get()->getResultArray() as $row) {
            $penghuni[$row['nama']] = $row;
        }

        $periode = [
            ['bulan' => 9, 'tahun' => 2026],
            ['bulan' => 10, 'tahun' => 2026],
            ['bulan' => 11, 'tahun' => 2026],
        ];

        // Pola status per penghuni untuk tiga periode di atas.
        $pola = [
            'Rina Pratiwi'  => ['lunas', 'ditolak', 'belum_bayar'],
            'Bagas Saputra' => ['lunas', 'lunas', 'belum_bayar'],
            'Siti Aminah'   => ['lunas', 'ditolak', 'belum_bayar'],
            'Dimas Prakoso' => ['lunas', 'lunas', 'lunas'],
            'Nadia Ayu'     => ['lunas', 'menunggu_verifikasi', 'belum_bayar'],
            'Yoga Pratama'  => ['lunas', 'lunas', 'belum_bayar'],
        ];

        $data = [];
        foreach ($penghuni as $nama => $row) {
            foreach ($periode as $i => $p) {
                $status = $pola[$nama][$i];
                $data[] = [
                    'penghuni_id'   => (int) $row['id'],
                    'bulan'         => $p['bulan'],
                    'tahun'         => $p['tahun'],
                    'jumlah'        => 750000,
                    'status'        => $status,
                    'tanggal_bayar' => in_array($status, ['lunas', 'menunggu_verifikasi'], true)
                        ? sprintf('%04d-%02d-05', $p['tahun'], $p['bulan'])
                        : null,
                    'bukti_transfer' => in_array($status, ['lunas', 'menunggu_verifikasi'], true)
                        ? 'bukti-demo-' . ((int) $row['id']) . '.png'
                        : null,
                    'catatan' => $status === 'ditolak'
                        ? 'Nominal transfer tidak sesuai tagihan.'
                        : null,
                ];
            }
        }

        $this->db->table('pembayaran')->truncate();
        $this->db->table('pembayaran')->insertBatch($data);
    }
}
