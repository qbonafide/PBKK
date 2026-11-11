<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model rincian pesanan (snapshot nama & harga saat transaksi).
 */
class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['order_id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'];

    /**
     * Item pesanan + gambar produk terkini untuk tampilan detail.
     */
    public function forOrder(int $orderId): array
    {
        return $this->db->table('order_items')
            ->select('order_items.*, products.image')
            ->join('products', 'products.id = order_items.product_id', 'left')
            ->where('order_items.order_id', $orderId)
            ->orderBy('order_items.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah item yang pernah dipesan untuk satu produk
     * (menentukan produk boleh dihapus fisik atau dinonaktifkan).
     */
    public function countByProduct(int $productId): int
    {
        return $this->where('product_id', $productId)->countAllResults();
    }
}
