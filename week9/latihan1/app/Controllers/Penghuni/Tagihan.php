<?php

namespace App\Controllers\Penghuni;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenghuniModel;

/**
 * Halaman tagihan untuk penghuni yang sedang login.
 * Penghuni hanya bisa melihat dan mengunggah bukti untuk tagihannya sendiri;
 * kepemilikan diperiksa di setiap request.
 */
class Tagihan extends BaseController
{
    private const FOLDER_BUKTI = WRITEPATH . 'uploads/bukti/';

    public function index()
    {
        $penghuni = (new PenghuniModel())->milikUser((int) session()->get('user_id'));

        if ($penghuni === null) {
            return redirect()->to(site_url('penghuni/dashboard'))
                ->with('error', 'Data penghuni belum terhubung dengan akun Anda. Hubungi admin.');
        }

        return view('penghuni/tagihan', [
            'title'    => 'Tagihan Saya',
            'penghuni' => $penghuni,
            'tagihan'  => (new PembayaranModel())->milikPenghuni((int) $penghuni['id']),
        ]);
    }

    public function uploadBukti($id = null)
    {
        $penghuni = (new PenghuniModel())->milikUser((int) session()->get('user_id'));

        if ($penghuni === null) {
            return redirect()->to(site_url('penghuni/dashboard'))
                ->with('error', 'Data penghuni tidak ditemukan.');
        }

        $model = new PembayaranModel();
        $bayar = $model->find((int) $id);

        // Cek kepemilikan: tagihan harus milik penghuni yang login.
        if ($bayar === null || (int) $bayar['penghuni_id'] !== (int) $penghuni['id']) {
            return redirect()->to(site_url('penghuni/tagihan'))
                ->with('error', 'Tagihan tidak ditemukan atau bukan milik Anda.');
        }

        if (! in_array($bayar['status'], ['belum_bayar', 'ditolak'], true)) {
            return redirect()->to(site_url('penghuni/tagihan'))
                ->with('error', 'Tagihan ini sudah dikirim dan sedang diproses.');
        }

        $aturan = [
            'bukti_transfer' => 'uploaded[bukti_transfer]|is_image[bukti_transfer]|mime_in[bukti_transfer,image/jpg,image/jpeg,image/png,image/webp]|max_size[bukti_transfer,2048]',
        ];

        $pesan = [
            'bukti_transfer' => [
                'uploaded' => 'Pilih berkas bukti transfer terlebih dahulu.',
                'is_image' => 'Bukti transfer harus berupa gambar.',
                'mime_in'  => 'Format bukti harus JPG, JPEG, PNG, atau WEBP.',
                'max_size' => 'Ukuran bukti maksimal 2 MB.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('bukti_transfer');
        $nama = simpan_upload($file, self::FOLDER_BUKTI);

        $model->update((int) $id, [
            'bukti_transfer' => $nama,
            'status'         => 'menunggu_verifikasi',
            'tanggal_bayar'  => date('Y-m-d'),
            'catatan'        => (string) $this->request->getPost('catatan'),
        ]);

        hapus_berkas($bayar['bukti_transfer'] === null ? null : self::FOLDER_BUKTI . $bayar['bukti_transfer']);

        return redirect()->to(site_url('penghuni/tagihan'))
            ->with('sukses', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }
}
