<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model tagihan/pembayaran bulanan.
 * Kombinasi penghuni + bulan + tahun unik, sehingga rekap pemasukan tidak
 * pernah menghitung satu tagihan dua kali.
 */
class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penghuni_id', 'bulan', 'tahun', 'jumlah', 'bukti_transfer',
        'status', 'tanggal_bayar', 'catatan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'penghuni_id' => 'required|integer',
        'bulan'       => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
        'tahun'       => 'required|integer|greater_than[2000]',
        'jumlah'      => 'required|integer|greater_than[0]',
        'status'      => 'required|in_list[belum_bayar,menunggu_verifikasi,lunas,ditolak]',
    ];

    protected $validationMessages = [
        'bulan' => [
            'required'                  => 'Bulan wajib dipilih.',
            'greater_than_equal_to'     => 'Bulan harus antara 1 sampai 12.',
            'less_than_equal_to'        => 'Bulan harus antara 1 sampai 12.',
        ],
        'tahun' => [
            'required'   => 'Tahun wajib diisi.',
            'integer'    => 'Tahun harus berupa angka.',
            'greater_than' => 'Tahun tidak valid.',
        ],
        'jumlah' => [
            'required'     => 'Jumlah tagihan wajib diisi.',
            'integer'      => 'Jumlah harus berupa angka.',
            'greater_than' => 'Jumlah harus lebih besar dari nol.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Daftar pembayaran lengkap dengan nama penghuni dan nomor kamar.
     */
    public function denganPenghuni(?string $status = null, ?string $keyword = null, int $perPage = 10)
    {
        $builder = $this->db->table('pembayaran')
            ->select('pembayaran.*, penghuni.nama AS nama_penghuni, kamar.nomor_kamar')
            ->join('penghuni', 'penghuni.id = pembayaran.penghuni_id', 'left')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left');

        if ($status !== null && $status !== '') {
            $builder->where('pembayaran.status', $status);
        }

        if ($keyword !== null && $keyword !== '') {
            $builder->like('penghuni.nama', $keyword);
        }

        return $builder->orderBy('pembayaran.tahun', 'DESC')
            ->orderBy('pembayaran.bulan', 'DESC');
    }

    public function milikPenghuni(int $penghuniId)
    {
        return $this->where('penghuni_id', $penghuniId)
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();
    }

    public function totalLunasPerBulan(int $tahun): array
    {
        $baris = $this->db->table('pembayaran')
            ->select('bulan, SUM(jumlah) AS total')
            ->where('status', 'lunas')
            ->where('tahun', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->get()
            ->getResultArray();

        $hasil = array_fill(1, 12, 0);
        foreach ($baris as $row) {
            $hasil[(int) $row['bulan']] = (int) $row['total'];
        }

        return $hasil;
    }
}
