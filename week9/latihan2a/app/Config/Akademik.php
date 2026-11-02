<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi aturan akademik SIAKAD.
 * Nilai di sini dipakai bersama oleh controller KRS, controller nilai,
 * dan halaman KHS supaya batas SKS serta skala nilai hanya didefinisikan sekali.
 */
class Akademik extends BaseConfig
{
    /**
     * Batas maksimum SKS yang boleh diambil seorang mahasiswa dalam satu semester.
     */
    public int $maxSks = 24;

    /**
     * Tahun ajaran yang sedang berjalan. Dipakai sebagai default saat
     * mahasiswa mengisi KRS dan saat dosen menginput nilai.
     */
    public string $tahunAjaranAktif = '2026/2027 Ganjil';

    /**
     * Skala konversi nilai angka ke huruf beserta bobotnya.
     * Urutan penting: dicek dari atas ke bawah, ambil ambang pertama yang terpenuhi.
     *
     * @var list<array{min: int, huruf: string, bobot: float}>
     */
    public array $skalaNilai = [
        ['min' => 85, 'huruf' => 'A', 'bobot' => 4.0],
        ['min' => 75, 'huruf' => 'AB', 'bobot' => 3.5],
        ['min' => 65, 'huruf' => 'B', 'bobot' => 3.0],
        ['min' => 60, 'huruf' => 'BC', 'bobot' => 2.5],
        ['min' => 55, 'huruf' => 'C', 'bobot' => 2.0],
        ['min' => 40, 'huruf' => 'D', 'bobot' => 1.0],
        ['min' => 0, 'huruf' => 'E', 'bobot' => 0.0],
    ];

    /**
     * Daftar semester yang bisa dipilih saat membuat mata kuliah.
     *
     * @var list<int>
     */
    public array $semesterTersedia = [1, 2, 3, 4, 5, 6, 7, 8];
}
