<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MatakuliahModel;

/**
 * Input nilai mahasiswa oleh dosen pengampu.
 *
 * Kepemilikan kelas diperiksa di setiap request (bukan hanya menyembunyikan
 * tautan): jika mata kuliah bukan milik dosen yang login, request dihentikan
 * dengan halaman 403.
 *
 * Tahun ajaran yang dinilai bisa dipilih dari daftar tahun ajaran yang memang
 * punya peserta di kelas tersebut; default-nya yang terbaru. Ini perlu karena
 * satu kelas bisa punya peserta pada beberapa periode.
 */
class Nilai extends BaseController
{
    public function index()
    {
        $dosen = $this->dosenSaya();

        if ($dosen === null) {
            return redirect()->to(site_url('dosen/dashboard'))
                ->with('error', 'Akun Anda belum terhubung dengan data dosen. Hubungi admin.');
        }

        $krsModel = new KrsModel();
        $kelas    = (new MatakuliahModel())->milikDosen((int) $dosen['id']);

        foreach ($kelas as $index => $baris) {
            $daftarTa = $krsModel->tahunAjaranKelas((int) $baris['id']);
            $kelas[$index]['tahun_ajaran'] = $daftarTa;
            $kelas[$index]['peserta']      = $daftarTa === [] ? 0 : (int) $daftarTa[0]['jumlah'];
        }

        return view('dosen/kelas', [
            'title' => 'Kelas Saya',
            'kelas' => $kelas,
        ]);
    }

    public function form($id = null)
    {
        [$dosen, $matkul] = $this->kelasMilikSaya((int) $id);

        if ($dosen === null && $matkul === null) {
            return redirect()->to(site_url('login'));
        }

        if ($dosen === null) {
            return $this->tolakAkses();
        }

        $krsModel = new KrsModel();
        $daftarTa = $krsModel->tahunAjaranKelas((int) $matkul['id']);
        $tahunAjaran = $this->pilihTahunAjaran($this->request->getGet('ta'), $daftarTa);

        return view('dosen/nilai_form', [
            'title'       => 'Input Nilai ' . $matkul['nama'],
            'matkul'      => $matkul,
            'tahunAjaran' => $tahunAjaran,
            'daftarTa'    => $daftarTa,
            'peserta'     => $krsModel->barisKelas((int) $matkul['id'], $tahunAjaran),
        ]);
    }

    public function simpan($id = null)
    {
        [$dosen, $matkul] = $this->kelasMilikSaya((int) $id);

        if ($dosen === null && $matkul === null) {
            return redirect()->to(site_url('login'));
        }

        if ($dosen === null) {
            return $this->tolakAkses();
        }

        $krsModel    = new KrsModel();
        $daftarTa    = $krsModel->tahunAjaranKelas((int) $matkul['id']);
        $tahunAjaran = $this->pilihTahunAjaran((string) $this->request->getPost('tahun_ajaran'), $daftarTa);
        $nilaiMasuk  = $this->request->getPost('nilai');

        if (! is_array($nilaiMasuk)) {
            return redirect()->back()->with('error', 'Tidak ada nilai yang dikirim.');
        }

        // Hanya baris KRS milik kelas & tahun ajaran ini yang boleh diubah.
        $peserta = [];
        foreach ($krsModel->barisKelas((int) $matkul['id'], $tahunAjaran) as $baris) {
            $peserta[(int) $baris['id']] = $baris;
        }

        $diubah = 0;
        $gagal  = [];

        foreach ($nilaiMasuk as $krsId => $nilai) {
            $krsId = (int) $krsId;
            $nilai = trim((string) $nilai);

            if (! isset($peserta[$krsId])) {
                $gagal[] = 'Ada baris KRS yang bukan milik kelas ini.';
                continue;
            }

            if ($nilai === '') {
                // Nilai dikosongkan -> nilai lama dihapus.
                $krsModel->update($krsId, [
                    'nilai_angka' => null,
                    'nilai_huruf' => null,
                    'bobot'       => null,
                ]);
                continue;
            }

            if (! is_numeric($nilai) || (int) $nilai < 0 || (int) $nilai > 100) {
                $gagal[] = 'Nilai ' . $peserta[$krsId]['nim'] . ' harus angka 0-100.';
                continue;
            }

            $konversi = $krsModel->konversiNilai((int) $nilai);

            $krsModel->update($krsId, [
                'nilai_angka' => (int) $nilai,
                'nilai_huruf' => $konversi['huruf'],
                'bobot'       => $konversi['bobot'],
            ]);

            $diubah++;
        }

        if ($gagal !== []) {
            return redirect()->back()->withInput()->with('error', implode(' ', $gagal));
        }

        return redirect()->to(site_url('dosen/nilai/' . $matkul['id'] . '?ta=' . urlencode($tahunAjaran)))
            ->with('sukses', $diubah . ' nilai mahasiswa berhasil disimpan dan dikonversi otomatis.');
    }

    private function dosenSaya(): ?array
    {
        return (new DosenModel())->milikUser((int) session()->get('user_id'));
    }

    /**
     * Menentukan tahun ajaran yang dipakai: pilihan dosen jika ada di daftar,
     * kalau tidak pakai yang terbaru, dan terakhir jatuh ke tahun ajaran aktif.
     */
    private function pilihTahunAjaran(?string $diminta, array $daftar): string
    {
        $tersedia = array_column($daftar, 'tahun_ajaran');

        if ($diminta !== null && $diminta !== '' && in_array($diminta, $tersedia, true)) {
            return $diminta;
        }

        if ($tersedia !== []) {
            return (string) $tersedia[0];
        }

        return (string) config('Akademik')->tahunAjaranAktif;
    }

    /**
     * Mengambil dosen yang login dan mata kuliah yang diminta.
     * Elemen pertama null berarti bukan pemilik kelas (atau data dosen tidak ada).
     *
     * @return array{0: array|null, 1: array|null}
     */
    private function kelasMilikSaya(int $matakuliahId): array
    {
        $dosen = $this->dosenSaya();

        if ($dosen === null) {
            return [null, null];
        }

        $matkul = (new MatakuliahModel())->find($matakuliahId);

        if ($matkul === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata kuliah tidak ditemukan.');
        }

        if ((int) $matkul['dosen_id'] !== (int) $dosen['id']) {
            return [null, $matkul];
        }

        return [$dosen, $matkul];
    }

    private function tolakAkses()
    {
        return $this->response
            ->setStatusCode(403)
            ->setBody(view('errors/html/error_403', ['role' => session()->get('role') ?? 'tamu']));
    }
}
