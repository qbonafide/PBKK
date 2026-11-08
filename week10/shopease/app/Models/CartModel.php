<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model keranjang: satu keranjang aktif per customer.
 */
class CartModel extends Model
{
    protected $table            = 'carts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['user_id'];

    /**
     * Ambil keranjang milik user, buat bila belum ada.
     */
    public function forUser(int $userId): array
    {
        $cart = $this->where('user_id', $userId)->first();

        if ($cart !== null) {
            return $cart;
        }

        $id = $this->insert(['user_id' => $userId], true);

        return ['id' => $id, 'user_id' => $userId];
    }
}
