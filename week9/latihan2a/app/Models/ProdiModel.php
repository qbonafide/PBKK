<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model program studi.
 */
class ProdiModel extends Model
{
    protected $table            = 'prodi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode', 'nama'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode' => 'required|max_length[10]',
        'nama' => 'required|min_length[3]|max_length[100]',
    ];

    protected $validationMessages = [
        'kode' => ['required' => 'Kode prodi wajib diisi.'],
        'nama' => [
            'required'   => 'Nama prodi wajib diisi.',
            'min_length' => 'Nama prodi minimal 3 karakter.',
        ],
    ];

    protected $skipValidation = false;
}
