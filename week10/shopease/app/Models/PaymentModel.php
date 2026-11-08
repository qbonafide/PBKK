<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pembayaran manual.
 * payment_status sengaja terpisah dari orders.order_status (BR-008).
 */
class PaymentModel extends Model
{
    public const STATUS_UNPAID    = 'unpaid';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED  = 'verified';
    public const STATUS_REJECTED  = 'rejected';

    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'order_id', 'method', 'amount', 'proof_file', 'payment_status',
        'verified_by', 'verified_at', 'notes',
    ];

    /**
     * Pembayaran milik satu pesanan.
     */
    public function forOrder(int $orderId): ?array
    {
        return $this->where('order_id', $orderId)->first();
    }

    /**
     * Daftar pembayaran yang menunggu verifikasi admin.
     */
    public function waitingVerification(int $perPage = 10): array
    {
        return $this->select('payments.*, orders.order_number, orders.total, orders.order_status, users.name AS customer_name')
            ->join('orders', 'orders.id = payments.order_id')
            ->join('users', 'users.id = orders.user_id')
            ->where('payments.payment_status', self::STATUS_SUBMITTED)
            ->orderBy('payments.updated_at', 'ASC')
            ->paginate($perPage);
    }

    /**
     * Semua pembayaran dengan filter status (riwayat verifikasi).
     */
    public function listByStatus(?string $status = null, int $perPage = 15): array
    {
        $builder = $this->select('payments.*, orders.order_number, orders.order_status, users.name AS customer_name')
            ->join('orders', 'orders.id = payments.order_id')
            ->join('users', 'users.id = orders.user_id');

        if ($status !== null && $status !== '') {
            $builder->where('payments.payment_status', $status);
        }

        return $builder->orderBy('payments.updated_at', 'DESC')->paginate($perPage);
    }

    /**
     * Hitung pembayaran menunggu verifikasi (kartu statistik dashboard).
     */
    public function countWaiting(): int
    {
        return $this->where('payment_status', self::STATUS_SUBMITTED)->countAllResults();
    }
}
