<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model akun pengguna SIAKAD (admin, dosen, mahasiswa).
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'username', 'password', 'role'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[3]|max_length[50]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,dosen,mahasiswa]',
    ];

    protected $validationMessages = [
        'nama'     => ['required' => 'Nama wajib diisi.'],
        'username' => ['required' => 'Username wajib diisi.'],
        'password' => [
            'required'   => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.',
        ],
        'role' => ['in_list' => 'Role tidak dikenal.'],
    ];

    protected $skipValidation = false;

    public function cariBerdasarkanUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
