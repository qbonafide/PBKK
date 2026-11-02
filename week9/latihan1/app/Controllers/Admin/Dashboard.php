<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PembayaranModel;
use App\Models\PenghuniModel;

/**
 * Dashboard admin: empat kartu statistik, grafik pemasukan per bulan,
 * dan tabel pembayaran terbaru.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $kamarModel      = new KamarModel();
        $penghuniModel   = new PenghuniModel();
        $pembayaranModel = new PembayaranModel();
        $db              = db_connect();

        $bulan = (int) date('n');
        $tahun = (int) date('Y');

        $pemasukanBulanIni = $db->table('pembayaran')
            ->selectSum('jumlah')
            ->where('status', 'lunas')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->getRowArray();

        $tunggakanBulanIni = $db->table('pembayaran')
            ->selectSum('jumlah')
            ->whereIn('status', ['belum_bayar', 'ditolak'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->getRowArray();

        $pembayaranTerbaru = $db->table('pembayaran')
            ->select('pembayaran.*, penghuni.nama AS nama_penghuni, kamar.nomor_kamar')
            ->join('penghuni', 'penghuni.id = pembayaran.penghuni_id', 'left')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left')
            ->orderBy('pembayaran.updated_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [
            'title'            => 'Dashboard Admin',
            'bulanNama'        => $this->namaBulan($bulan),
            'tahun'            => $tahun,
            'totalKamar'       => $kamarModel->countAllResults(),
            'kamarTerisi'      => $kamarModel->where('status', 'terisi')->countAllResults(),
            'kamarKosong'      => $kamarModel->where('status', 'kosong')->countAllResults(),
            'kamarPerbaikan'   => $kamarModel->where('status', 'perbaikan')->countAllResults(),
            'penghuniAktif'    => $penghuniModel->where('status', 'aktif')->countAllResults(),
            'pemasukanBulanIni' => (int) ($pemasukanBulanIni['jumlah'] ?? 0),
            'tunggakanBulanIni' => (int) ($tunggakanBulanIni['jumlah'] ?? 0),
            'grafik'           => $pembayaranModel->totalLunasPerBulan($tahun),
            'pembayaranTerbaru' => $pembayaranTerbaru,
        ];

        return view('admin/dashboard', $data);
    }

    private function namaBulan(int $bulan): string
    {
        $daftar = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $daftar[$bulan] ?? '-';
    }
}
