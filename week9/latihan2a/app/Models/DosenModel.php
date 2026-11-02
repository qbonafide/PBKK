<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model dosen. NIP dipakai juga sebagai username akun login
 * (lihat Admin\Dosen::store yang membuat akun dalam satu transaksi).
 */
class DosenModel extends Model
{
    protected $table            = 'dosen';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'nip', 'nama', 'email', 'no_hp'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nip'   => 'required|max_length[30]',
        'nama'  => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'nip'  => ['required' => 'NIP wajib diisi.'],
        'nama' => [
            'required'   => 'Nama dosen wajib diisi.',
            'min_length' => 'Nama dosen minimal 3 karakter.',
        ],
        'email' => ['valid_email' => 'Format email tidak valid.'],
    ];

    protected $skipValidation = false;

    /**
     * Daftar dosen beserta username akunnya, mendukung pencarian.
     */
    public function denganAkun(?string $keyword = null)
    {
        $builder = $this->db->table('dosen')
            ->select('dosen.*, users.username')
            ->join('users', 'users.id = dosen.user_id', 'left');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('dosen.nama', $keyword)
                ->orLike('dosen.nip', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('dosen.nama', 'ASC');
    }

    public function milikUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }
}
