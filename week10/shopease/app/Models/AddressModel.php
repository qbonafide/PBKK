<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model alamat pengiriman customer.
 */
class AddressModel extends Model
{
    protected $table            = 'addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'user_id', 'recipient_name', 'phone', 'address', 'city', 'province', 'postal_code', 'is_default',
    ];

    protected $validationRules = [
        'recipient_name' => 'required|min_length[3]|max_length[100]',
        'phone'          => 'required|min_length[8]|max_length[20]',
        'address'        => 'required|min_length[10]',
        'city'           => 'required|max_length[100]',
        'province'       => 'required|max_length[100]',
    ];

    /**
     * Daftar alamat milik satu user (default di urutan pertama).
     */
    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Simpan alamat baru; alamat pertama otomatis menjadi default.
     */
    public function saveForUser(int $userId, array $input): int
    {
        $isFirst = $this->where('user_id', $userId)->countAllResults() === 0;

        $this->insert([
            'user_id'        => $userId,
            'recipient_name' => $input['recipient_name'],
            'phone'          => $input['phone'],
            'address'        => $input['address'],
            'city'           => $input['city'],
            'province'       => $input['province'],
            'postal_code'    => $input['postal_code'] ?? null,
            'is_default'     => $isFirst ? 1 : 0,
        ]);

        return (int) $this->getInsertID();
    }

    /**
     * Pastikan alamat benar-benar milik user (dipakai di checkout).
     */
    public function belongsToUser(int $addressId, int $userId): bool
    {
        return $this->where('id', $addressId)->where('user_id', $userId)->countAllResults() > 0;
    }
}
