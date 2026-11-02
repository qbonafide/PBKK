<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model kamar kos.
 * `slug` dibentuk otomatis dari nomor kamar lewat method buatSlug() supaya URL
 * detail kamar selalu rapi dan unik (mis. kamar-a1).
 */
class KamarModel extends Model
{
    protected $table            = 'kamar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_kamar', 'slug', 'tipe', 'harga_bulanan',
        'luas', 'fasilitas', 'status', 'foto',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nomor_kamar'   => 'required|max_length[20]',
        'tipe'          => 'required|in_list[standar,ac,premium]',
        'harga_bulanan' => 'required|integer|greater_than[0]',
        'luas'          => 'permit_empty|max_length[20]',
        'status'        => 'required|in_list[kosong,terisi,perbaikan]',
    ];

    protected $validationMessages = [
        'nomor_kamar' => [
            'required' => 'Nomor kamar wajib diisi.',
        ],
        'tipe' => [
            'required' => 'Tipe kamar wajib dipilih.',
            'in_list'  => 'Tipe kamar tidak dikenal.',
        ],
        'harga_bulanan' => [
            'required'     => 'Harga bulanan wajib diisi.',
            'integer'      => 'Harga harus berupa angka.',
            'greater_than' => 'Harga harus lebih besar dari nol.',
        ],
        'status' => [
            'required' => 'Status kamar wajib dipilih.',
            'in_list'  => 'Status kamar tidak dikenal.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Membuat slug unik dari nomor kamar. Jika sudah dipakai kamar lain,
     * tambahkan akhiran -2, -3, dan seterusnya.
     */
    public function buatSlug(string $nomorKamar, ?int $abaikanId = null): string
    {
        $dasar = 'kamar-' . url_title($nomorKamar, '-', true);
        $slug  = $dasar;
        $urut  = 2;

        while (true) {
            $query = $this->where('slug', $slug);
            if ($abaikanId !== null) {
                $query->where('id !=', $abaikanId);
            }

            if ($query->countAllResults() === 0) {
                return $slug;
            }

            $slug = $dasar . '-' . $urut;
            $urut++;
        }
    }

    public function cariBerdasarkanSlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Data kamar untuk halaman publik dengan filter pencarian, tipe, dan status.
     */
    public function untukPublik(?string $keyword, ?string $tipe, ?string $status, int $perPage)
    {
        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('nomor_kamar', $keyword)
                ->orLike('fasilitas', $keyword)
                ->groupEnd();
        }

        if ($tipe !== null && $tipe !== '') {
            $this->where('tipe', $tipe);
        }

        if ($status !== null && $status !== '') {
            $this->where('status', $status);
        }

        return $this->orderBy('nomor_kamar', 'ASC')->paginate($perPage);
    }
}
