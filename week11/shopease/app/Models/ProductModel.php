<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model produk: katalog, stok, dan relasi ke kategori.
 */
class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'category_id', 'name', 'slug', 'description', 'price', 'stock', 'image', 'status',
    ];

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'name'        => 'required|min_length[3]|max_length[150]',
        'price'       => 'required|decimal|greater_than_equal_to[0]',
        'stock'       => 'required|is_natural',
        'status'      => 'required|in_list[active,inactive]',
    ];

    /**
     * Query dasar yang sudah menempelkan nama kategori.
     * Dipakai bersama oleh katalog customer dan daftar produk admin.
     */
    public function withCategory(): self
    {
        return $this->select('products.*, categories.name AS category_name')
            ->join('categories', 'categories.id = products.category_id', 'left');
    }

    /**
     * Katalog publik: hanya produk aktif.
     *
     * @param array{q?: string, category?: int|null, sort?: string} $filters
     */
    public function catalog(array $filters = [], int $perPage = 8): array
    {
        $builder = $this->withCategory()->where('products.status', 'active');

        if (! empty($filters['q'])) {
            $builder->like('products.name', $filters['q']);
        }

        if (! empty($filters['category'])) {
            $builder->where('products.category_id', (int) $filters['category']);
        }

        $sort = $filters['sort'] ?? '';
        if ($sort === 'price_asc') {
            $builder->orderBy('products.price', 'ASC');
        } elseif ($sort === 'price_desc') {
            $builder->orderBy('products.price', 'DESC');
        } elseif ($sort === 'name_asc') {
            $builder->orderBy('products.name', 'ASC');
        } else {
            $builder->orderBy('products.created_at', 'DESC');
        }

        return $builder->paginate($perPage);
    }

    /**
     * Produk aktif untuk homepage & related produk.
     */
    public function latestActive(int $limit = 8): array
    {
        return $this->withCategory()
            ->where('products.status', 'active')
            ->orderBy('products.created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Produk terlaris berdasarkan kuantitas terjual pada order yang sah.
     */
    public function bestSellers(int $limit = 4): array
    {
        return $this->db->table('order_items')
            ->select('products.id, products.name, products.price, products.image, SUM(order_items.quantity) AS sold')
            ->join('products', 'products.id = order_items.product_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->whereIn('orders.order_status', ['processing', 'shipped', 'completed'])
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil produk aktif berdasarkan id (validasi cart/checkout).
     */
    public function findActive(int $id): ?array
    {
        return $this->where('status', 'active')->find($id);
    }

    /**
     * Kurangi stok secara aman (dipakai di dalam transaksi checkout).
     */
    public function decreaseStock(int $productId, int $quantity): bool
    {
        return $this->db->table('products')
            ->set('stock', 'stock - ' . $quantity, false)
            ->where('id', $productId)
            ->where('stock >=', $quantity)
            ->update();
    }
}
