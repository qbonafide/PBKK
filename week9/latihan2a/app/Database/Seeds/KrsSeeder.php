<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Config\Akademik;

/**
 * Data KRS dan nilai.
 *
 * Skenario:
 *  - Mahasiswa Informatika mengambil mata kuliah semester 1 pada 2025/2026 Ganjil
 *    dan sudah dinilai (agar KHS tidak kosong).
 *  - 2025001 (akun demo mhs123) juga mengambil mata kuliah semester 2 pada
 *    2025/2026 Genap dengan nilai, lalu semester 3 pada 2026/2027 Ganjil yang
 *    belum dinilai (status KRS aktif).
 *  - Mahasiswa SI dan TI mendapat satu mata kuliah yang relevan.
 *
 * Nilai huruf dan bobot dihitung dengan skala yang sama dengan Config\Akademik
 * supaya data seed konsisten dengan hasil input dosen.
 */
class KrsSeeder extends Seeder
{
    public function run()
    {
        $akademik = config('Akademik');

        $mahasiswa = [];
        foreach ($this->db->table('mahasiswa')->get()->getResultArray() as $row) {
            $mahasiswa[$row['nim']] = (int) $row['id'];
        }

        $matkul = [];
        foreach ($this->db->table('matakuliah')->get()->getResultArray() as $row) {
            $matkul[$row['kode']] = (int) $row['id'];
        }

        /** Konversi nilai angka memakai skala di Config\Akademik. */
        $konversi = static function (?int $angka) use ($akademik): array {
            if ($angka === null) {
                return ['nilai_angka' => null, 'nilai_huruf' => null, 'bobot' => null];
            }

            foreach ($akademik->skalaNilai as $skala) {
                if ($angka >= $skala['min']) {
                    return [
                        'nilai_angka' => $angka,
                        'nilai_huruf' => $skala['huruf'],
                        'bobot'       => $skala['bobot'],
                    ];
                }
            }

            return ['nilai_angka' => $angka, 'nilai_huruf' => 'E', 'bobot' => 0.0];
        };

        $baris = [];

        // 1) Semua mahasiswa IF: mata kuliah semester 1 pada 2025/2026 Ganjil.
        $nilaiSmt1 = [88, 80, 92];
        for ($i = 1; $i <= 12; $i++) {
            $nim  = '2025' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $kode = ['IF101', 'IF102', 'IF103'];

            foreach ($kode as $index => $k) {
                // Variasi nilai antar mahasiswa supaya IPK tidak seragam.
                $nilai = max(40, $nilaiSmt1[$index] - ($i * 2));

                $baris[] = array_merge([
                    'mahasiswa_id'  => $mahasiswa[$nim],
                    'matakuliah_id' => $matkul[$k],
                    'tahun_ajaran'  => '2025/2026 Ganjil',
                ], $konversi($nilai));
            }
        }

        // 2) Mahasiswa IF yang lebih senior: semester 2 pada 2025/2026 Genap.
        $nilaiSmt2 = [90, 82, 95];
        foreach (['IF201', 'IF202', 'IF203'] as $index => $k) {
            $baris[] = array_merge([
                'mahasiswa_id'  => $mahasiswa['2025001'],
                'matakuliah_id' => $matkul[$k],
                'tahun_ajaran'  => '2025/2026 Genap',
            ], $konversi($nilaiSmt2[$index]));
        }

        // 3) KRS aktif 2026/2027 Ganjil (semester 3, belum dinilai).
        foreach (['IF301', 'IF302', 'IF303'] as $k) {
            $baris[] = array_merge([
                'mahasiswa_id'  => $mahasiswa['2025001'],
                'matakuliah_id' => $matkul[$k],
                'tahun_ajaran'  => '2026/2027 Ganjil',
            ], $konversi(null));
        }

        // Sebagian mahasiswa IF lain juga mengisi KRS aktif (2 mata kuliah).
        for ($i = 2; $i <= 6; $i++) {
            $nim = '2025' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            foreach (['IF301', 'IF302'] as $k) {
                $baris[] = array_merge([
                    'mahasiswa_id'  => $mahasiswa[$nim],
                    'matakuliah_id' => $matkul[$k],
                    'tahun_ajaran'  => '2026/2027 Ganjil',
                ], $konversi(null));
            }
        }

        // 4) Mahasiswa Sistem Informasi.
        foreach (['2025013', '2025014', '2025015', '2025016', '2025017'] as $index => $nim) {
            $baris[] = array_merge([
                'mahasiswa_id'  => $mahasiswa[$nim],
                'matakuliah_id' => $matkul['SI201'],
                'tahun_ajaran'  => '2025/2026 Genap',
            ], $konversi(84 - ($index * 3)));

            $baris[] = array_merge([
                'mahasiswa_id'  => $mahasiswa[$nim],
                'matakuliah_id' => $matkul['SI301'],
                'tahun_ajaran'  => '2026/2027 Ganjil',
            ], $konversi(null));
        }

        // 5) Mahasiswa Teknologi Informasi.
        foreach (['2025018', '2025019', '2025020'] as $index => $nim) {
            $baris[] = array_merge([
                'mahasiswa_id'  => $mahasiswa[$nim],
                'matakuliah_id' => $matkul['TI201'],
                'tahun_ajaran'  => '2025/2026 Genap',
            ], $konversi(79 - ($index * 4)));
        }

        $this->db->table('krs')->truncate();
        $this->db->table('krs')->insertBatch($baris);
    }
}
