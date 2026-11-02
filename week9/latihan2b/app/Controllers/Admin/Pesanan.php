<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

/**
 * Pengelolaan pesanan oleh admin: lihat bukti transfer, ubah status sesuai
 * ALUR, dan isi nomor resi. Status yang tidak ada di ALUR akan ditolak oleh
 * OrderModel::ubahStatus().
 */
class Pesanan extends BaseController
{
    public function index()
    {
        $model   = new OrderModel();
        $status  = (string) ($this->request->getGet('status') ?? '');
        $keyword = (string) ($this->request->getGet('q') ?? '');

        service('pager')->only(['status', 'q']);

        $daftar = $model->daftarAdmin($status, $keyword);

        return view('admin/pesanan/index', [
            'title'   => 'Kelola Pesanan',
            'pesanan' => $daftar->paginate(10),
            'pager'   => $model->pager,
            'status'  => $status,
            'keyword' => $keyword,
            'label'   => OrderModel::LABEL_STATUS,
        ]);
    }

    public function detail($id = null)
    {
        $model   = new OrderModel();
        $pesanan = $model->denganItem((int) $id);

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
        }

        return view('admin/pesanan/detail', [
            'title'      => 'Pesanan ' . $pesanan['invoice'],
            'pesanan'    => $pesanan,
            'berikutnya' => $model->statusBerikutnya((string) $pesanan['status']),
            'label'      => OrderModel::LABEL_STATUS,
        ]);
    }

    public function ubahStatus($id = null)
    {
        $model   = new OrderModel();
        $pesanan = $model->find((int) $id);

        if ($pesanan === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
        }

        $statusBaru = (string) $this->request->getPost('status');
        $noResi     = (string) $this->request->getPost('no_resi');

        if ($statusBaru === '') {
            return redirect()->to(site_url('admin/pesanan/' . $id))
                ->with('error', 'Pilih status tujuan terlebih dahulu.');
        }

        $hasil = $model->ubahStatus((int) $id, $statusBaru, $noResi);

        return redirect()->to(site_url('admin/pesanan/' . $id))
            ->with($hasil['ok'] ? 'sukses' : 'error', $hasil['pesan']);
    }
}
