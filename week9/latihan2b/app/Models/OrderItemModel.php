<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model item pesanan (snapshot nama & harga produk saat checkout).
 */
class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['order_id', 'produk_id', 'nama_produk', 'harga', 'qty', 'subtotal'];
    protected $useTimestamps    = false;
    protected $skipValidation   = true;
}
