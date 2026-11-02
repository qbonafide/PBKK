<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk akun pengguna (admin & penghuni).
 * Password TIDAK di-hash otomatis di sini; hashing dilakukan di controller
 * dan seeder supaya prosesnya eksplisit dan mudah diaudit.
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'username', 'email', 'password', 'role'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|alpha_numeric|min_length[3]|max_length[50]',
        'email'    => 'permit_empty|valid_email|max_length[100]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,penghuni]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.',
        ],
        'username' => [
            'required'      => 'Username wajib diisi.',
            'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
            'min_length'    => 'Username minimal 3 karakter.',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid.',
        ],
        'password' => [
            'required'   => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.',
        ],
        'role' => [
            'in_list' => 'Role harus admin atau penghuni.',
        ],
    ];

    protected $skipValidation = false;

    public function cariBerdasarkanUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
