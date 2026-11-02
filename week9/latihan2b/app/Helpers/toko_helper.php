<?php

/**
 * Helper toko: perhitungan keranjang dan format harga.
 *
 * Harga & stok SELALU dibaca ulang dari database setiap kali keranjang dihitung,
 * supaya perubahan harga atau stok oleh admin langsung terpakai dan isi session
 * tidak bisa dipalsukan lewat browser.
 */

if (! function_exists('format_rupiah')) {
    function format_rupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (! function_exists('ongkir_untuk')) {
    /**
     * Ongkir flat Rp 20.000, gratis jika subtotal minimal Rp 200.000.
     */
    function ongkir_untuk(int $subtotal): int
    {
        if ($subtotal <= 0) {
            return 0;
        }

        return $subtotal >= 200000 ? 0 : 20000;
    }
}

if (! function_exists('hitung_keranjang')) {
    /**
     * Menghitung isi keranjang dari session dengan data terbaru dari database.
     *
     * Qty otomatis dibatasi jumlah stok yang tersedia; produk yang stoknya habis
     * tetap ditampilkan agar pembeli tahu dan bisa menghapusnya.
     *
     * @return array{items: list<array<string, mixed>>, subtotal: int, ongkir: int, total: int, jumlah: int}
     */
    function hitung_keranjang(): array
    {
        $keranjang = session()->get('keranjang');
        $keranjang = is_array($keranjang) ? $keranjang : [];

        $hasil = [
            'items'    => [],
            'subtotal' => 0,
            'ongkir'   => 0,
            'total'    => 0,
            'jumlah'   => 0,
        ];

        if ($keranjang === []) {
            return $hasil;
        }

        $produkModel = new \App\Models\ProdukModel();
        $daftar      = $produkModel->whereIn('id', array_keys($keranjang))->findAll();

        $keranjangBaru = [];

        foreach ($daftar as $produk) {
            $id  = (int) $produk['id'];
            $qty = (int) ($keranjang[$id] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            $stok = (int) $produk['stok'];

            // Qty tidak boleh melebihi stok yang tersedia.
            $qty = min($qty, max(0, $stok));

            if ($qty === 0) {
                continue;
            }

            $keranjangBaru[$id] = $qty;
            $subtotal           = $qty * (int) $produk['harga'];

            $hasil['items'][] = [
                'produk_id' => $id,
                'nama'      => $produk['nama'],
                'slug'      => $produk['slug'],
                'foto'      => $produk['foto'],
                'harga'     => (int) $produk['harga'],
                'qty'       => $qty,
                'stok'      => $stok,
                'subtotal'  => $subtotal,
            ];

            $hasil['subtotal'] += $subtotal;
            $hasil['jumlah']   += $qty;
        }

        // Simpan kembali qty yang sudah disesuaikan stok.
        session()->set('keranjang', $keranjangBaru);

        $hasil['ongkir'] = ongkir_untuk($hasil['subtotal']);
        $hasil['total']  = $hasil['subtotal'] + $hasil['ongkir'];

        return $hasil;
    }
}

if (! function_exists('label_kondisi')) {
    function label_kondisi(string $kondisi): string
    {
        return match ($kondisi) {
            'like_new'  => 'Like new',
            'bagus'     => 'Bagus',
            'ada_minus' => 'Ada minus',
            default     => $kondisi,
        };
    }
}
