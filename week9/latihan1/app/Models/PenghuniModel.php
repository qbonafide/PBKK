<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model penghuni kos.
 * Satu user hanya boleh punya satu baris penghuni (unik pada user_id), dan
 * satu kamar hanya boleh punya satu penghuni berstatus aktif.
 */
class PenghuniModel extends Model
{
    protected $table            = 'penghuni';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'kamar_id', 'nama', 'no_hp', 'alamat_asal', 'pekerjaan',
        'foto_ktp', 'tanggal_masuk', 'tanggal_keluar', 'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kamar_id'      => 'required|integer',
        'nama'          => 'required|min_length[3]|max_length[100]',
        'no_hp'         => 'permit_empty|max_length[20]',
        'tanggal_masuk' => 'permit_empty|valid_date[Y-m-d]',
        'status'        => 'required|in_list[aktif,keluar]',
    ];

    protected $validationMessages = [
        'kamar_id' => [
            'required' => 'Kamar wajib dipilih.',
            'integer'  => 'Kamar tidak valid.',
        ],
        'nama' => [
            'required'   => 'Nama penghuni wajib diisi.',
            'min_length' => 'Nama penghuni minimal 3 karakter.',
        ],
        'tanggal_masuk' => [
            'valid_date' => 'Format tanggal masuk harus YYYY-MM-DD.',
        ],
        'status' => [
            'required' => 'Status penghuni wajib dipilih.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Daftar penghuni beserta nomor kamar dan username akunnya.
     */
    public function denganKamar(?string $keyword = null, int $perPage = 10)
    {
        $builder = $this->db->table('penghuni')
            ->select('penghuni.*, kamar.nomor_kamar, users.username')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left')
            ->join('users', 'users.id = penghuni.user_id', 'left');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('penghuni.nama', $keyword)
                ->orLike('kamar.nomor_kamar', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('penghuni.status', 'ASC')
            ->orderBy('kamar.nomor_kamar', 'ASC');
    }

    public function penghuniAktifPerKamar(int $kamarId, ?int $abaikanId = null): ?array
    {
        $builder = $this->where('kamar_id', $kamarId)->where('status', 'aktif');
        if ($abaikanId !== null) {
            $builder->where('id !=', $abaikanId);
        }

        return $builder->first();
    }

    public function milikUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }
}
