<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model mahasiswa. NIM dipakai sebagai username akun login.
 */
class MahasiswaModel extends Model
{
    protected $table            = 'mahasiswa';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'prodi_id', 'dosen_wali_id', 'nim', 'nama',
        'angkatan', 'alamat', 'no_hp',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'prodi_id' => 'required|integer',
        'nim'      => 'required|max_length[20]',
        'nama'     => 'required|min_length[3]|max_length[100]',
        'angkatan' => 'required|integer|greater_than[1999]|less_than[2101]',
    ];

    protected $validationMessages = [
        'prodi_id' => ['required' => 'Program studi wajib dipilih.'],
        'nim'      => ['required' => 'NIM wajib diisi.'],
        'nama'     => [
            'required'   => 'Nama mahasiswa wajib diisi.',
            'min_length' => 'Nama mahasiswa minimal 3 karakter.',
        ],
        'angkatan' => [
            'required'     => 'Angkatan wajib diisi.',
            'integer'      => 'Angkatan harus berupa angka tahun.',
            'greater_than' => 'Angkatan tidak valid.',
            'less_than'    => 'Angkatan tidak valid.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Daftar mahasiswa lengkap dengan prodi, dosen wali, dan username.
     */
    public function denganRelasi(?string $keyword = null)
    {
        $builder = $this->db->table('mahasiswa')
            ->select('mahasiswa.*, prodi.nama AS nama_prodi, prodi.kode AS kode_prodi,
                      dosen.nama AS nama_wali, users.username')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->join('users', 'users.id = mahasiswa.user_id', 'left');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('mahasiswa.nama', $keyword)
                ->orLike('mahasiswa.nim', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('mahasiswa.nim', 'ASC');
    }

    public function milikUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function perDosenWali(int $dosenId)
    {
        return $this->db->table('mahasiswa')
            ->select('mahasiswa.*, prodi.nama AS nama_prodi')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->where('mahasiswa.dosen_wali_id', $dosenId)
            ->orderBy('mahasiswa.nim', 'ASC')
            ->get()
            ->getResultArray();
    }
}
