<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model produk thrift.
 *
 * Method untukKatalog() memasang filter pencarian langsung pada model supaya
 * ikut terpakai oleh paginate().
 */
class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kategori_id', 'nama', 'slug', 'brand', 'ukuran', 'kondisi',
        'deskripsi', 'harga', 'stok', 'foto', 'aktif',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kategori_id' => 'required|integer',
        'nama'        => 'required|min_length[3]|max_length[120]',
        'ukuran'      => 'permit_empty|max_length[20]',
        'kondisi'     => 'required|in_list[like_new,bagus,ada_minus]',
        'harga'       => 'required|integer|greater_than[0]',
        'stok'        => 'required|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'kategori_id' => ['required' => 'Kategori wajib dipilih.'],
        'nama'        => [
            'required'   => 'Nama produk wajib diisi.',
            'min_length' => 'Nama produk minimal 3 karakter.',
        ],
        'kondisi'     => [
            'required' => 'Kondisi wajib dipilih.',
            'in_list'  => 'Kondisi tidak dikenal.',
        ],
        'harga'       => [
            'required'     => 'Harga wajib diisi.',
            'integer'      => 'Harga harus berupa angka.',
            'greater_than' => 'Harga harus lebih besar dari nol.',
        ],
        'stok'        => [
            'required'              => 'Stok wajib diisi.',
            'integer'               => 'Stok harus berupa angka.',
            'greater_than_equal_to' => 'Stok tidak boleh negatif.',
        ],
    ];

    protected $skipValidation = false;

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

    /**
     * Daftar produk untuk katalog publik.
     *
     * @param array{keyword?: string, kategori?: string, ukuran?: string, kondisi?: string, urut?: string} $filter
     */
    public function untukKatalog(array $filter, int $perPage = 9)
    {
        $this->select('produk.*, kategori.nama AS nama_kategori, kategori.slug AS slug_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left')
            ->where('produk.aktif', 1);

        $keyword = trim((string) ($filter['keyword'] ?? ''));
        if ($keyword !== '') {
            $this->groupStart()
                ->like('produk.nama', $keyword)
                ->orLike('produk.brand', $keyword)
                ->orLike('produk.deskripsi', $keyword)
                ->groupEnd();
        }

        if (! empty($filter['kategori'])) {
            $this->where('kategori.slug', $filter['kategori']);
        }

        if (! empty($filter['ukuran'])) {
            $this->where('produk.ukuran', $filter['ukuran']);
        }

        if (! empty($filter['kondisi'])) {
            $this->where('produk.kondisi', $filter['kondisi']);
        }

        $urut = (string) ($filter['urut'] ?? 'terbaru');
        $this->orderBy(
            match ($urut) {
                'termurah' => 'produk.harga',
                'termahal' => 'produk.harga',
                default    => 'produk.created_at',
            },
            $urut === 'termurah' ? 'ASC' : 'DESC'
        );

        return $this->paginate($perPage);
    }

    public function produkTerbaru(int $jumlah = 6): array
    {
        return $this->select('produk.*, kategori.nama AS nama_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left')
            ->where('produk.aktif', 1)
            ->orderBy('produk.created_at', 'DESC')
            ->findAll($jumlah);
    }

    public function cariBerdasarkanSlug(string $slug): ?array
    {
        return $this->select('produk.*, kategori.nama AS nama_kategori, kategori.slug AS slug_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left')
            ->where('produk.slug', $slug)
            ->where('produk.aktif', 1)
            ->first();
    }

    /**
     * Daftar ukuran yang benar-benar dipakai produk aktif, untuk pilihan filter.
     *
     * @return list<string>
     */
    public function daftarUkuran(): array
    {
        $baris = $this->db->table('produk')
            ->select('ukuran')
            ->where('aktif', 1)
            ->where('ukuran IS NOT NULL')
            ->distinct()
            ->orderBy('ukuran', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($baris, 'ukuran');
    }
}
