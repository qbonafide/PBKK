<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pesanan (header transaksi).
 *
 * Semua transisi status pesanan divalidasi di sini agar aturan alur
 * transaksi tidak tersebar di controller.
 */
class OrderModel extends Model
{
    public const STATUS_PENDING     = 'pending_payment';
    public const STATUS_WAITING     = 'waiting_verification';
    public const STATUS_PROCESSING  = 'processing';
    public const STATUS_SHIPPED     = 'shipped';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_CANCELLED   = 'cancelled';

    /**
     * Peta transisi status yang diizinkan (state machine sederhana).
     *
     * @var array<string, list<string>>
     */
    public const TRANSITIONS = [
        self::STATUS_PENDING    => [self::STATUS_WAITING, self::STATUS_CANCELLED],
        self::STATUS_WAITING    => [self::STATUS_PROCESSING, self::STATUS_PENDING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED    => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED  => [],
        self::STATUS_CANCELLED  => [],
    ];

    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'user_id', 'address_id', 'order_number', 'order_date', 'subtotal',
        'shipping_cost', 'total', 'order_status', 'notes',
    ];

    /**
     * Nomor pesanan unik dengan format SE-YYYYMMDD-XXXX.
     */
    public function generateOrderNumber(): string
    {
        $prefix = 'SE-' . date('Ymd') . '-';

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $candidate = $prefix . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

            if ($this->where('order_number', $candidate)->countAllResults() === 0) {
                return $candidate;
            }
        }

        // Cadangan terakhir: pakai timestamp agar dijamin unik.
        return $prefix . substr((string) time(), -4) . random_int(10, 99);
    }

    /**
     * Apakah perpindahan status diizinkan.
     */
    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /**
     * Daftar status pesanan yang dikenal sistem.
     *
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_WAITING,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Riwayat pesanan milik satu customer.
     */
    public function forUser(int $userId, int $perPage = 10): array
    {
        return $this->select('orders.*, payments.payment_status')
            ->join('payments', 'payments.order_id = orders.id', 'left')
            ->where('orders.user_id', $userId)
            ->orderBy('orders.order_date', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Detail pesanan lengkap (dipakai halaman customer & admin).
     */
    public function detail(int $orderId): ?array
    {
        return $this->select('orders.*, users.name AS customer_name, users.email AS customer_email, addresses.recipient_name, addresses.phone, addresses.address, addresses.city, addresses.province, addresses.postal_code')
            ->join('users', 'users.id = orders.user_id')
            ->join('addresses', 'addresses.id = orders.address_id', 'left')
            ->where('orders.id', $orderId)
            ->first();
    }

    /**
     * Daftar pesanan untuk admin/operator dengan filter status & pencarian nomor.
     */
    public function adminList(array $filters = [], int $perPage = 10): array
    {
        $builder = $this->select('orders.*, users.name AS customer_name, payments.payment_status, shipments.tracking_number')
            ->join('users', 'users.id = orders.user_id')
            ->join('payments', 'payments.order_id = orders.id', 'left')
            ->join('shipments', 'shipments.order_id = orders.id', 'left');

        if (! empty($filters['status'])) {
            $builder->where('orders.order_status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $builder->groupStart()
                ->like('orders.order_number', $filters['q'])
                ->orLike('users.name', $filters['q'])
                ->groupEnd();
        }

        return $builder->orderBy('orders.order_date', 'DESC')->paginate($perPage);
    }

    /**
     * Rekap penjualan harian untuk grafik dashboard.
     *
     * @return list<array{tanggal: string, penjualan: string, jumlah: string}>
     */
    public function dailySales(int $days = 14): array
    {
        $since = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));

        return $this->db->table('orders')
            ->select('DATE(order_date) AS tanggal, SUM(total) AS penjualan, COUNT(*) AS jumlah')
            ->where('order_date >=', $since . ' 00:00:00')
            ->whereIn('order_status', [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_COMPLETED])
            ->groupBy('DATE(order_date)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Distribusi jumlah pesanan per status.
     *
     * @return array<string, int>
     */
    public function statusDistribution(): array
    {
        $rows = $this->db->table('orders')
            ->select('order_status, COUNT(*) AS jumlah')
            ->groupBy('order_status')
            ->get()
            ->getResultArray();

        $result = array_fill_keys(self::statuses(), 0);

        foreach ($rows as $row) {
            $result[$row['order_status']] = (int) $row['jumlah'];
        }

        return $result;
    }

    /**
     * Total nilai penjualan yang dianggap sah.
     */
    public function totalSales(): float
    {
        $row = $this->db->table('orders')
            ->selectSum('total', 'grand')
            ->whereIn('order_status', [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_COMPLETED])
            ->get()
            ->getRowArray();

        return (float) ($row['grand'] ?? 0);
    }

    /**
     * Total omzet & jumlah transaksi sah pada satu periode.
     *
     * @return array{omzet: float, jumlah: int}
     */
    public function summaryForPeriod(string $start, string $end): array
    {
        $row = $this->db->table('orders')
            ->selectSum('total', 'omzet')
            ->selectCount('id', 'jumlah')
            ->where('DATE(order_date) >=', $start)
            ->where('DATE(order_date) <=', $end)
            ->whereIn('order_status', [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_COMPLETED])
            ->get()
            ->getRowArray();

        return [
            'omzet'  => (float) ($row['omzet'] ?? 0),
            'jumlah' => (int) ($row['jumlah'] ?? 0),
        ];
    }

    /**
     * Laporan penjualan dengan filter periode dan status.
     */
    public function report(string $start, string $end, ?string $status = null, int $perPage = 15): array
    {
        $builder = $this->select('orders.*, users.name AS customer_name')
            ->join('users', 'users.id = orders.user_id')
            ->where('DATE(orders.order_date) >=', $start)
            ->where('DATE(orders.order_date) <=', $end);

        if ($status !== null && $status !== '') {
            $builder->where('orders.order_status', $status);
        }

        return $builder->orderBy('orders.order_date', 'DESC')->paginate($perPage);
    }
}
