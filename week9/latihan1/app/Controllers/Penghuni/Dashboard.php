<?php

namespace App\Controllers\Penghuni;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PembayaranModel;
use App\Models\PenghuniModel;

/**
 * Dashboard penghuni: ringkasan kamar sendiri dan status tagihan terakhir.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $penghuniModel = new PenghuniModel();
        $penghuni      = $penghuniModel->milikUser((int) session()->get('user_id'));

        if ($penghuni === null) {
            return view('penghuni/dashboard', [
                'title'      => 'Dashboard Penghuni',
                'penghuni'   => null,
                'kamar'      => null,
                'tagihan'    => [],
                'totalBelum' => 0,
            ]);
        }

        $kamar   = (new KamarModel())->find((int) $penghuni['kamar_id']);
        $tagihan = (new PembayaranModel())->milikPenghuni((int) $penghuni['id']);

        $totalBelum = 0;
        foreach ($tagihan as $t) {
            if (in_array($t['status'], ['belum_bayar', 'ditolak'], true)) {
                $totalBelum += (int) $t['jumlah'];
            }
        }

        return view('penghuni/dashboard', [
            'title'      => 'Dashboard Penghuni',
            'penghuni'   => $penghuni,
            'kamar'      => $kamar,
            'tagihan'    => array_slice($tagihan, 0, 4),
            'totalBelum' => $totalBelum,
        ]);
    }
}
