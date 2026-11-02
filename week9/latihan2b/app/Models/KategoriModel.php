<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model kategori produk (Atasan, Bawahan, Outer, Dress, Sepatu, Aksesoris).
 */
class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama', 'slug'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[50]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 3 karakter.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Slug unik dari nama kategori, ditambah akhiran angka bila sudah dipakai.
     */
    public function buatSlug(string $nama, ?int $abaikanId = null): string
    {
        $dasar = url_title($nama, '-', true);
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

    public function denganJumlahProduk(): array
    {
        return $this->db->table('kategori')
            ->select('kategori.*, COUNT(produk.id) AS jumlah_produk')
            ->join('produk', 'produk.kategori_id = kategori.id', 'left')
            ->groupBy('kategori.id')
            ->orderBy('kategori.nama', 'ASC')
            ->get()
            ->getResultArray();
    }
}
