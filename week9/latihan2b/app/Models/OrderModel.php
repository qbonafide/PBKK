<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pesanan ThriftIn.
 *
 * Aturan perpindahan status ditegakkan lewat konstanta ALUR: perubahan status
 * yang tidak ada di daftar akan ditolak. Pembatalan mengembalikan stok produk
 * yang sebelumnya ditahan saat checkout.
 */
class OrderModel extends Model
{
    /**
     * Alur status yang diizinkan. Kunci = status sekarang,
     * nilai = status tujuan yang boleh dipilih.
     *
     * @var array<string, list<string>>
     */
    public const ALUR = [
        'menunggu_pembayaran' => ['menunggu_verifikasi', 'dibatalkan'],
        'menunggu_verifikasi' => ['diproses', 'menunggu_pembayaran', 'dibatalkan'],
        'diproses'            => ['dikirim', 'dibatalkan'],
        'dikirim'             => ['selesai'],
        'selesai'             => [],
        'dibatalkan'          => [],
    ];

    public const LABEL_STATUS = [
        'menunggu_pembayaran' => 'Menunggu pembayaran',
        'menunggu_verifikasi' => 'Menunggu verifikasi',
        'diproses'            => 'Diproses',
        'dikirim'             => 'Dikirim',
        'selesai'             => 'Selesai',
        'dibatalkan'          => 'Dibatalkan',
    ];

    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'invoice', 'nama_penerima', 'no_hp', 'alamat_kirim', 'catatan',
        'subtotal', 'ongkir', 'total', 'status', 'bukti_bayar', 'no_resi', 'expired_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Membuat nomor invoice unik berformat TI-YYYYMMDD-0001.
     * Dipanggil di dalam transaksi checkout.
     */
    public function buatInvoice(): string
    {
        $prefix = 'TI-' . date('Ymd') . '-';

        $terakhir = $this->db->table('orders')
            ->select('invoice')
            ->like('invoice', $prefix, 'after')
            ->orderBy('invoice', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $urut = 1;
        if ($terakhir !== null) {
            $urut = (int) substr((string) $terakhir['invoice'], strlen($prefix)) + 1;
        }

        return $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Apakah status tujuan boleh dipilih dari status saat ini.
     */
    public function transisiDiizinkan(string $dari, string $ke): bool
    {
        return in_array($ke, self::ALUR[$dari] ?? [], true);
    }

    /**
     * Status tujuan yang boleh dipilih untuk sebuah pesanan.
     *
     * @return list<string>
     */
    public function statusBerikutnya(string $statusSekarang): array
    {
        return self::ALUR[$statusSekarang] ?? [];
    }

    /**
     * Mengubah status pesanan dengan mengikuti ALUR.
     *
     * @return array{ok: bool, pesan: string}
     */
    public function ubahStatus(int $orderId, string $statusBaru, ?string $noResi = null): array
    {
        $pesanan = $this->find($orderId);

        if ($pesanan === null) {
            return ['ok' => false, 'pesan' => 'Pesanan tidak ditemukan.'];
        }

        $statusLama = (string) $pesanan['status'];

        if (! $this->transisiDiizinkan($statusLama, $statusBaru)) {
            return [
                'ok'    => false,
                'pesan' => 'Perubahan status dari ' . $this->label($statusLama) . ' ke '
                    . $this->label($statusBaru) . ' tidak diizinkan.',
            ];
        }

        $db = db_connect();
        $db->transStart();

        // Pembatalan mengembalikan stok yang ditahan saat checkout.
        if ($statusBaru === 'dibatalkan') {
            $this->kembalikanStok($orderId);
        }

        $data = ['status' => $statusBaru];

        if ($noResi !== null && $noResi !== '') {
            $data['no_resi'] = $noResi;
        }

        $this->update($orderId, $data);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['ok' => false, 'pesan' => 'Gagal mengubah status pesanan.'];
        }

        return ['ok' => true, 'pesan' => 'Status pesanan diubah menjadi ' . $this->label($statusBaru) . '.'];
    }

    /**
     * Mengembalikan stok produk dari seluruh item pesanan.
     */
    public function kembalikanStok(int $orderId): void
    {
        $items = $this->db->table('order_items')
            ->where('order_id', $orderId)
            ->get()
            ->getResultArray();

        foreach ($items as $item) {
            if ($item['produk_id'] === null) {
                continue;
            }

            $this->db->table('produk')
                ->set('stok', 'stok + ' . (int) $item['qty'], false)
                ->where('id', (int) $item['produk_id'])
                ->update();
        }
    }

    /**
     * Membatalkan pesanan yang belum dibayar lebih dari 24 jam.
     *
     * @return array{jumlah: int, invoice: list<string>}
     */
    public function batalkanKedaluwarsa(): array
    {
        $kedaluwarsa = $this->where('status', 'menunggu_pembayaran')
            ->where('expired_at <', date('Y-m-d H:i:s'))
            ->findAll();

        $invoice = [];

        foreach ($kedaluwarsa as $pesanan) {
            $hasil = $this->ubahStatus((int) $pesanan['id'], 'dibatalkan');

            if ($hasil['ok']) {
                $invoice[] = (string) $pesanan['invoice'];
            }
        }

        return ['jumlah' => count($invoice), 'invoice' => $invoice];
    }

    public function label(string $status): string
    {
        return self::LABEL_STATUS[$status] ?? $status;
    }

    public function denganItem(int $orderId): ?array
    {
        $pesanan = $this->db->table('orders')
            ->select('orders.*, users.nama AS nama_pembeli')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->where('orders.id', $orderId)
            ->get()
            ->getRowArray();

        if ($pesanan === null) {
            return null;
        }

        $pesanan['items'] = $this->db->table('order_items')
            ->where('order_id', $orderId)
            ->get()
            ->getResultArray();

        return $pesanan;
    }

    public function daftarPembeli(int $userId)
    {
        return $this->db->table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Query daftar pesanan untuk admin. Filter dipasang pada model (bukan pada
     * Query Builder terpisah) supaya ikut dipakai saat paginate() dihitung.
     */
    public function daftarAdmin(?string $status = null, ?string $keyword = null)
    {
        $this->select('orders.*, users.nama AS nama_pembeli')
            ->join('users', 'users.id = orders.user_id', 'left');

        if ($status !== null && $status !== '') {
            $this->where('orders.status', $status);
        }

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('orders.invoice', $keyword)
                ->orLike('users.nama', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('orders.created_at', 'DESC');
    }

    /**
     * Jumlah pesanan per status (untuk dashboard admin).
     *
     * @return array<string, int>
     */
    public function jumlahPerStatus(): array
    {
        $hasil = array_fill_keys(array_keys(self::ALUR), 0);

        $baris = $this->db->table('orders')
            ->select('status, COUNT(*) AS jumlah')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($baris as $row) {
            $hasil[$row['status']] = (int) $row['jumlah'];
        }

        return $hasil;
    }

    /**
     * Omzet dihitung dari pesanan yang sudah dibayar/diproses (bukan pesanan
     * yang masih menunggu pembayaran atau dibatalkan).
     */
    public function omzet(): int
    {
        $row = $this->db->table('orders')
            ->selectSum('total', 'omzet')
            ->whereIn('status', ['menunggu_verifikasi', 'diproses', 'dikirim', 'selesai'])
            ->get()
            ->getRowArray();

        return (int) ($row['omzet'] ?? 0);
    }

    /**
     * Jumlah unit produk terjual (semua pesanan yang tidak dibatalkan).
     */
    public function produkTerjual(): int
    {
        $row = $this->db->table('order_items')
            ->selectSum('order_items.qty', 'terjual')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.status !=', 'dibatalkan')
            ->get()
            ->getRowArray();

        return (int) ($row['terjual'] ?? 0);
    }

    /**
     * Omzet per bulan pada tahun berjalan, untuk grafik dashboard.
     *
     * @return array<int, int> 12 nilai (Januari..Desember)
     */
    public function omzetPerBulan(int $tahun): array
    {
        $baris = $this->db->table('orders')
            ->select("MONTH(created_at) AS bulan, SUM(total) AS total")
            ->where('YEAR(created_at)', $tahun)
            ->whereIn('status', ['menunggu_verifikasi', 'diproses', 'dikirim', 'selesai'])
            ->groupBy('MONTH(created_at)')
            ->get()
            ->getResultArray();

        $hasil = array_fill(1, 12, 0);

        foreach ($baris as $row) {
            $hasil[(int) $row['bulan']] = (int) $row['total'];
        }

        return $hasil;
    }
}
