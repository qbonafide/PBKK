<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;

/**
 * Pengisian KRS oleh mahasiswa.
 *
 * Validasi dilakukan sebelum menyimpan apa pun:
 *  1. total SKS setelah ditambah tidak boleh melebihi batas di Config\Akademik,
 *  2. kuota kelas belum penuh,
 *  3. mata kuliah yang sama tidak boleh diambil dua kali di tahun ajaran yang sama.
 *
 * Penyimpanan memakai transaksi supaya tidak ada KRS yang tersimpan separuh.
 */
class Krs extends BaseController
{
    public function index()
    {
        $mhs = $this->mahasiswaSaya();

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'))
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $akademik    = config('Akademik');
        $tahunAjaran = (string) $akademik->tahunAjaranAktif;
        $krsModel    = new KrsModel();

        $sudahDiambil = array_column(
            $krsModel->barisMahasiswa((int) $mhs['id'], $tahunAjaran),
            null,
            'matakuliah_id'
        );

        $daftar = (new MatakuliahModel())->denganRelasi()
            ->where('matakuliah.prodi_id', (int) $mhs['prodi_id'])
            ->orderBy('matakuliah.semester', 'ASC')
            ->orderBy('matakuliah.kode', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($daftar as $index => $baris) {
            $peserta = $krsModel->jumlahPeserta((int) $baris['id'], $tahunAjaran);

            $daftar[$index]['peserta']     = $peserta;
            $daftar[$index]['sudah_ambil'] = isset($sudahDiambil[(int) $baris['id']]);
            $daftar[$index]['penuh']       = $peserta >= (int) $baris['kuota'];
        }

        return view('mahasiswa/krs', [
            'title'       => 'Kartu Rencana Studi',
            'mhs'         => $mhs,
            'tahunAjaran' => $tahunAjaran,
            'maxSks'      => $akademik->maxSks,
            'sksSekarang' => $krsModel->totalSks((int) $mhs['id'], $tahunAjaran),
            'daftar'      => $daftar,
            'diambil'     => $krsModel->barisMahasiswa((int) $mhs['id'], $tahunAjaran),
        ]);
    }

    public function simpan()
    {
        $mhs = $this->mahasiswaSaya();

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'))
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $akademik    = config('Akademik');
        $tahunAjaran = (string) $akademik->tahunAjaranAktif;
        $dipilih     = $this->request->getPost('matakuliah');

        if (! is_array($dipilih) || $dipilih === []) {
            return redirect()->back()->with('error', 'Pilih minimal satu mata kuliah terlebih dahulu.');
        }

        $krsModel    = new KrsModel();
        $matkulModel = new MatakuliahModel();
        $galat       = [];
        $sksBaru     = 0;
        $akanDisimpan = [];

        foreach ($dipilih as $matakuliahId) {
            $matakuliahId = (int) $matakuliahId;
            $matkul       = $matkulModel->find($matakuliahId);

            if ($matkul === null) {
                $galat[] = 'Ada mata kuliah yang tidak ditemukan.';
                continue;
            }

            if ((int) $matkul['prodi_id'] !== (int) $mhs['prodi_id']) {
                $galat[] = 'Mata kuliah ' . $matkul['nama'] . ' bukan milik program studi Anda.';
                continue;
            }

            if ($krsModel->sudahDiambil((int) $mhs['id'], $matakuliahId, $tahunAjaran)) {
                $galat[] = 'Mata kuliah ' . $matkul['nama'] . ' sudah diambil pada tahun ajaran ini.';
                continue;
            }

            $peserta = $krsModel->jumlahPeserta($matakuliahId, $tahunAjaran);
            if ($peserta >= (int) $matkul['kuota']) {
                $galat[] = 'Kuota mata kuliah ' . $matkul['nama'] . ' sudah penuh (' . $peserta . '/' . $matkul['kuota'] . ').';
                continue;
            }

            $sksBaru += (int) $matkul['sks'];
            $akanDisimpan[] = $matakuliahId;
        }

        $sksSekarang = $krsModel->totalSks((int) $mhs['id'], $tahunAjaran);

        if ($sksSekarang + $sksBaru > (int) $akademik->maxSks) {
            $galat[] = 'Total SKS menjadi ' . ($sksSekarang + $sksBaru)
                . ' SKS, melebihi batas maksimal ' . $akademik->maxSks . ' SKS.';
        }

        if ($galat !== []) {
            return redirect()->back()->withInput()->with('error', implode(' ', $galat));
        }

        $db = db_connect();
        $db->transStart();

        foreach ($akanDisimpan as $matakuliahId) {
            $krsModel->insert([
                'mahasiswa_id'  => (int) $mhs['id'],
                'matakuliah_id' => $matakuliahId,
                'tahun_ajaran'  => $tahunAjaran,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'KRS gagal disimpan. Silakan coba lagi.');
        }

        $total = $krsModel->totalSks((int) $mhs['id'], $tahunAjaran);

        return redirect()->to(site_url('mahasiswa/krs'))
            ->with('sukses', count($akanDisimpan) . ' mata kuliah berhasil ditambahkan. Total SKS tahun ini: ' . $total . ' SKS.');
    }

    public function cetak()
    {
        $mhs = $this->mahasiswaSaya();

        if ($mhs === null) {
            return redirect()->to(site_url('mahasiswa/dashboard'));
        }

        $tahunAjaran = (string) config('Akademik')->tahunAjaranAktif;

        return view('mahasiswa/cetak_krs', [
            'title'       => 'Cetak KRS',
            'mhs'         => $mhs,
            'tahunAjaran' => $tahunAjaran,
            'baris'       => (new KrsModel())->barisMahasiswa((int) $mhs['id'], $tahunAjaran),
            'totalSks'    => (new KrsModel())->totalSks((int) $mhs['id'], $tahunAjaran),
        ]);
    }

    private function mahasiswaSaya(): ?array
    {
        return (new MahasiswaModel())->milikUser((int) session()->get('user_id'));
    }
}
