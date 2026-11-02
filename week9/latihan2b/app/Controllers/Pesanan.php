<?php

namespace App\Controllers;

use App\Models\OrderModel;

/**
 * Halaman pesanan untuk pembeli: riwayat, detail, unggah bukti transfer,
 * dan pembatalan pesanan yang belum dibayar.
 *
 * Kepemilikan pesanan diperiksa di setiap request: pembeli tidak bisa membuka
 * pesanan milik pembeli lain (403).
 */
class Pesanan extends BaseController
{
    private const FOLDER_BUKTI = WRITEPATH . 'uploads/bukti/';

    public function index()
    {
        return view('pesanan/index', [
            'title'   => 'Pesanan Saya',
            'pesanan' => (new OrderModel())->daftarPembeli((int) session()->get('user_id')),
        ]);
    }

    public function detail($id = null)
    {
        $model   = new OrderModel();
        $pesanan = $model->denganItem((int) $id);

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
        }

        if ((int) $pesanan['user_id'] !== (int) session()->get('user_id')) {
            return $this->tolakAkses();
        }

        return view('pesanan/detail', [
            'title'    => 'Pesanan ' . $pesanan['invoice'],
            'pesanan'  => $pesanan,
            'berikutnya' => $model->statusBerikutnya((string) $pesanan['status']),
            'label'    => OrderModel::LABEL_STATUS,
        ]);
    }

    public function uploadBukti($id = null)
    {
        $model   = new OrderModel();
        $pesanan = $model->find((int) $id);

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
        }

        if ((int) $pesanan['user_id'] !== (int) session()->get('user_id')) {
            return $this->tolakAkses();
        }

        if ($pesanan['status'] !== 'menunggu_pembayaran') {
            return redirect()->to(site_url('pesanan/' . $id))
                ->with('error', 'Bukti transfer hanya bisa diunggah saat status menunggu pembayaran.');
        }

        $aturan = [
            'bukti_bayar' => 'uploaded[bukti_bayar]|is_image[bukti_bayar]|mime_in[bukti_bayar,image/jpg,image/jpeg,image/png,image/webp]|max_size[bukti_bayar,2048]',
        ];

        $pesan = [
            'bukti_bayar' => [
                'uploaded' => 'Pilih berkas bukti transfer terlebih dahulu.',
                'is_image' => 'Bukti transfer harus berupa gambar.',
                'mime_in'  => 'Format bukti harus JPG, JPEG, PNG, atau WEBP.',
                'max_size' => 'Ukuran bukti maksimal 2 MB.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $nama = simpan_upload($this->request->getFile('bukti_bayar'), self::FOLDER_BUKTI);

        $model->update((int) $id, ['bukti_bayar' => $nama]);

        $hasil = $model->ubahStatus((int) $id, 'menunggu_verifikasi');

        if (! $hasil['ok']) {
            return redirect()->to(site_url('pesanan/' . $id))->with('error', $hasil['pesan']);
        }

        hapus_berkas($pesanan['bukti_bayar'] === null ? null : self::FOLDER_BUKTI . $pesanan['bukti_bayar']);

        return redirect()->to(site_url('pesanan/' . $id))
            ->with('sukses', 'Bukti transfer terkirim. Status pesanan menjadi menunggu verifikasi.');
    }

    public function batal($id = null)
    {
        $model   = new OrderModel();
        $pesanan = $model->find((int) $id);

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
        }

        if ((int) $pesanan['user_id'] !== (int) session()->get('user_id')) {
            return $this->tolakAkses();
        }

        $hasil = $model->ubahStatus((int) $id, 'dibatalkan');

        return redirect()->to(site_url('pesanan/' . $id))
            ->with($hasil['ok'] ? 'sukses' : 'error', $hasil['pesan']);
    }

    private function tolakAkses()
    {
        return $this->response
            ->setStatusCode(403)
            ->setBody(view('errors/html/error_403', ['role' => session()->get('role') ?? 'tamu']));
    }
}
