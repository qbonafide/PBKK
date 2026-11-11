<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model kategori produk.
 */
class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['name', 'slug', 'description', 'is_active'];

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
    ];

    /**
     * Kategori aktif beserta jumlah produk aktif di dalamnya (untuk homepage).
     */
    public function withProductCount(bool $onlyActive = true): array
    {
        $builder = $this->db->table('categories')
            ->select('categories.*, COUNT(products.id) AS product_count')
            ->join('products', 'products.category_id = categories.id AND products.status = "active"', 'left')
            ->groupBy('categories.id');

        if ($onlyActive) {
            $builder->where('categories.is_active', 1);
        }

        return $builder->orderBy('categories.name', 'ASC')->get()->getResultArray();
    }

    /**
     * Apakah kategori masih dipakai produk (menentukan boleh/tidaknya hapus).
     */
    public function hasProducts(int $categoryId): bool
    {
        return $this->db->table('products')
            ->where('category_id', $categoryId)
            ->countAllResults() > 0;
    }
}
