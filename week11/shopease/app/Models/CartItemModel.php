<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model item keranjang beserta perhitungan subtotal dari data produk di database.
 */
class CartItemModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['cart_id', 'product_id', 'quantity'];

    /**
     * Semua item pada satu keranjang + data produk terkini.
     */
    public function withProduct(int $cartId): array
    {
        return $this->db->table('cart_items')
            ->select('cart_items.*, products.name, products.price, products.stock, products.image, products.status')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.cart_id', $cartId)
            ->orderBy('cart_items.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Satu baris item berdasarkan id, dibatasi pada keranjang tertentu (otorisasi).
     */
    public function findInCart(int $itemId, int $cartId): ?array
    {
        return $this->where('id', $itemId)->where('cart_id', $cartId)->first();
    }

    /**
     * Cari item produk tertentu di dalam keranjang.
     */
    public function findProductInCart(int $cartId, int $productId): ?array
    {
        return $this->where('cart_id', $cartId)->where('product_id', $productId)->first();
    }

    /**
     * Total item (jumlah kuantitas) untuk badge navbar.
     */
    public function totalQuantity(int $cartId): int
    {
        $row = $this->db->table('cart_items')
            ->selectSum('quantity', 'total')
            ->where('cart_id', $cartId)
            ->get()
            ->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Kosongkan keranjang (dipakai setelah checkout berhasil).
     */
    public function clearCart(int $cartId): void
    {
        $this->where('cart_id', $cartId)->delete();
    }
}
