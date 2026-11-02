<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Akademik;

/**
 * Model KRS (kartu rencana studi) sekaligus tempat perhitungan nilai.
 *
 * Nilai angka dari dosen dikonversi ke huruf dan bobot memakai skala di
 * Config\Akademik. IP semester = total (SKS x bobot) / total SKS semester,
 * IPK kumulatif memakai cara yang sama tetapi menjumlahkan semua semester.
 */
class KrsModel extends Model
{
    protected $table            = 'krs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id', 'matakuliah_id', 'tahun_ajaran',
        'nilai_angka', 'nilai_huruf', 'bobot',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'mahasiswa_id'  => 'required|integer',
        'matakuliah_id' => 'required|integer',
        'tahun_ajaran'  => 'required|max_length[30]',
    ];

    protected $validationMessages = [
        'mahasiswa_id'  => ['required' => 'Mahasiswa wajib dipilih.'],
        'matakuliah_id' => ['required' => 'Mata kuliah wajib dipilih.'],
        'tahun_ajaran'  => ['required' => 'Tahun ajaran wajib diisi.'],
    ];

    protected $skipValidation = false;

    /**
     * Mengubah nilai angka menjadi huruf + bobot sesuai Config\Akademik::$skalaNilai.
     *
     * @return array{huruf: string, bobot: float}
     */
    public function konversiNilai(int $angka): array
    {
        /** @var Akademik $akademik */
        $akademik = config('Akademik');

        foreach ($akademik->skalaNilai as $skala) {
            if ($angka >= $skala['min']) {
                return ['huruf' => $skala['huruf'], 'bobot' => (float) $skala['bobot']];
            }
        }

        return ['huruf' => 'E', 'bobot' => 0.0];
    }

    /**
     * Total SKS yang sudah diambil mahasiswa pada satu tahun ajaran.
     */
    public function totalSks(int $mahasiswaId, string $tahunAjaran): int
    {
        $row = $this->db->table('krs')
            ->selectSum('matakuliah.sks', 'total')
            ->join('matakuliah', 'matakuliah.id = krs.matakuliah_id')
            ->where('krs.mahasiswa_id', $mahasiswaId)
            ->where('krs.tahun_ajaran', $tahunAjaran)
            ->get()
            ->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Cek apakah mahasiswa sudah mengambil mata kuliah yang sama di tahun ajaran ini.
     */
    public function sudahDiambil(int $mahasiswaId, int $matakuliahId, string $tahunAjaran): bool
    {
        return $this->where('mahasiswa_id', $mahasiswaId)
            ->where('matakuliah_id', $matakuliahId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first() !== null;
    }

    /**
     * Jumlah peserta kelas pada tahun ajaran tertentu (dipakai untuk cek kuota).
     */
    public function jumlahPeserta(int $matakuliahId, string $tahunAjaran): int
    {
        return $this->where('matakuliah_id', $matakuliahId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->countAllResults();
    }

    /**
     * Baris KRS seorang mahasiswa beserta data mata kuliahnya.
     * Jika $tahunAjaran null, semua tahun ajaran diambil.
     */
    public function barisMahasiswa(int $mahasiswaId, ?string $tahunAjaran = null): array
    {
        $builder = $this->db->table('krs')
            ->select('krs.*, matakuliah.kode, matakuliah.nama, matakuliah.sks,
                      matakuliah.semester, dosen.nama AS nama_dosen')
            ->join('matakuliah', 'matakuliah.id = krs.matakuliah_id')
            ->join('dosen', 'dosen.id = matakuliah.dosen_id', 'left')
            ->where('krs.mahasiswa_id', $mahasiswaId);

        if ($tahunAjaran !== null && $tahunAjaran !== '') {
            $builder->where('krs.tahun_ajaran', $tahunAjaran);
        }

        return $builder->orderBy('matakuliah.semester', 'ASC')
            ->orderBy('matakuliah.kode', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Daftar tahun ajaran yang punya peserta pada satu kelas, diurutkan dari
     * yang terbaru. Dipakai agar dosen dapat memilih periode penilaian.
     *
     * @return list<array{tahun_ajaran: string, jumlah: int}>
     */
    public function tahunAjaranKelas(int $matakuliahId): array
    {
        return $this->db->table('krs')
            ->select('tahun_ajaran, COUNT(*) AS jumlah')
            ->where('matakuliah_id', $matakuliahId)
            ->groupBy('tahun_ajaran')
            ->orderBy('tahun_ajaran', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Baris peserta satu kelas (untuk halaman input nilai dosen).
     */
    public function barisKelas(int $matakuliahId, string $tahunAjaran): array
    {
        return $this->db->table('krs')
            ->select('krs.*, mahasiswa.nim, mahasiswa.nama, prodi.nama AS nama_prodi')
            ->join('mahasiswa', 'mahasiswa.id = krs.mahasiswa_id')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->where('krs.matakuliah_id', $matakuliahId)
            ->where('krs.tahun_ajaran', $tahunAjaran)
            ->orderBy('mahasiswa.nim', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Rekap KHS per semester berdasarkan semester mata kuliah.
     *
     * Setiap semester berisi: baris (mata kuliah), total sks, total mutu, dan IP.
     */
    public function rekapPerSemester(int $mahasiswaId): array
    {
        $rekap = [];

        foreach ($this->barisMahasiswa($mahasiswaId) as $baris) {
            $semester = (int) $baris['semester'];

            if (! isset($rekap[$semester])) {
                $rekap[$semester] = [
                    'semester'  => $semester,
                    'baris'     => [],
                    'total_sks' => 0,
                    'total_mutu' => 0.0,
                    'ip'        => 0.0,
                ];
            }

            $sks  = (int) $baris['sks'];
            $bobot = $baris['bobot'] === null ? null : (float) $baris['bobot'];
            $mutu  = $bobot === null ? 0.0 : $sks * $bobot;

            $baris['mutu'] = $mutu;
            $rekap[$semester]['baris'][] = $baris;

            // Hanya baris yang sudah dinilai yang dihitung ke IP.
            if ($bobot !== null) {
                $rekap[$semester]['total_sks'] += $sks;
                $rekap[$semester]['total_mutu'] += $mutu;
            }
        }

        foreach ($rekap as $semester => $data) {
            $rekap[$semester]['ip'] = $data['total_sks'] > 0
                ? round($data['total_mutu'] / $data['total_sks'], 2)
                : 0.0;
        }

        ksort($rekap);

        return $rekap;
    }

    /**
     * IPK kumulatif seluruh semester yang sudah dinilai.
     *
     * @return array{sks: int, mutu: float, ipk: float}
     */
    public function ipk(int $mahasiswaId): array
    {
        $totalSks  = 0;
        $totalMutu = 0.0;

        foreach ($this->rekapPerSemester($mahasiswaId) as $semester) {
            $totalSks += $semester['total_sks'];
            $totalMutu += $semester['total_mutu'];
        }

        return [
            'sks'  => $totalSks,
            'mutu' => $totalMutu,
            'ipk'  => $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.0,
        ];
    }
}
