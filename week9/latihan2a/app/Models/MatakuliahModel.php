<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model mata kuliah (kelas yang diampu dosen).
 */
class MatakuliahModel extends Model
{
    protected $table            = 'matakuliah';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'prodi_id', 'dosen_id', 'kode', 'nama', 'sks', 'semester',
        'kuota', 'hari', 'jam_mulai', 'jam_selesai', 'ruang',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'prodi_id' => 'required|integer',
        'dosen_id' => 'required|integer',
        'kode'     => 'required|max_length[20]',
        'nama'     => 'required|min_length[3]|max_length[100]',
        'sks'      => 'required|integer|greater_than[0]|less_than_equal_to[6]',
        'semester' => 'required|integer|greater_than[0]|less_than_equal_to[8]',
        'kuota'    => 'required|integer|greater_than[0]',
    ];

    protected $validationMessages = [
        'prodi_id' => ['required' => 'Program studi wajib dipilih.'],
        'dosen_id' => ['required' => 'Dosen pengampu wajib dipilih.'],
        'kode'     => ['required' => 'Kode mata kuliah wajib diisi.'],
        'nama'     => [
            'required'   => 'Nama mata kuliah wajib diisi.',
            'min_length' => 'Nama mata kuliah minimal 3 karakter.',
        ],
        'sks'      => [
            'required'             => 'SKS wajib diisi.',
            'greater_than'         => 'SKS harus lebih dari nol.',
            'less_than_equal_to'   => 'SKS maksimal 6.',
        ],
        'semester' => [
            'required'           => 'Semester wajib diisi.',
            'less_than_equal_to' => 'Semester maksimal 8.',
        ],
        'kuota'    => [
            'required'     => 'Kuota wajib diisi.',
            'greater_than' => 'Kuota harus lebih dari nol.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Daftar mata kuliah dengan nama prodi & dosen, mendukung pencarian.
     */
    public function denganRelasi(?string $keyword = null)
    {
        $builder = $this->db->table('matakuliah')
            ->select('matakuliah.*, prodi.nama AS nama_prodi, dosen.nama AS nama_dosen')
            ->join('prodi', 'prodi.id = matakuliah.prodi_id', 'left')
            ->join('dosen', 'dosen.id = matakuliah.dosen_id', 'left');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('matakuliah.nama', $keyword)
                ->orLike('matakuliah.kode', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('matakuliah.semester', 'ASC')
            ->orderBy('matakuliah.kode', 'ASC');
    }

    public function milikDosen(int $dosenId)
    {
        return $this->db->table('matakuliah')
            ->select('matakuliah.*, prodi.nama AS nama_prodi')
            ->join('prodi', 'prodi.id = matakuliah.prodi_id', 'left')
            ->where('matakuliah.dosen_id', $dosenId)
            ->orderBy('matakuliah.semester', 'ASC')
            ->get()
            ->getResultArray();
    }
}
