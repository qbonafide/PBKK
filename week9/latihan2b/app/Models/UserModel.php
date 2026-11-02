<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model akun ThriftIn (admin & pembeli).
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama', 'username', 'email', 'password', 'role', 'no_hp', 'alamat'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|alpha_numeric|min_length[3]|max_length[50]',
        'email'    => 'permit_empty|valid_email|max_length[100]',
        'no_hp'    => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'nama'     => ['required' => 'Nama wajib diisi.'],
        'username' => [
            'required'      => 'Username wajib diisi.',
            'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
            'min_length'    => 'Username minimal 3 karakter.',
        ],
        'email'    => ['valid_email' => 'Format email tidak valid.'],
    ];

    protected $skipValidation = false;

    public function cariBerdasarkanUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
