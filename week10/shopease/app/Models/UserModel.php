<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model akun pengguna (admin, operator, customer).
 * Password selalu disimpan sebagai hash, bukan teks biasa.
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['name', 'email', 'password_hash', 'role', 'phone', 'is_active'];

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|max_length[150]',
        'role'  => 'required|in_list[admin,customer,operator]',
    ];

    protected $validationMessages = [
        'email' => [
            'valid_email' => 'Format email tidak valid.',
            'is_unique'   => 'Email sudah terdaftar.',
        ],
    ];

    /**
     * Cari akun aktif berdasarkan email (dipakai saat login).
     */
    public function findActiveByEmail(string $email): ?array
    {
        return $this->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Daftar customer untuk halaman pelanggan di area admin.
     */
    public function customers(int $perPage = 10): array
    {
        return $this->where('role', 'customer')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }
}
