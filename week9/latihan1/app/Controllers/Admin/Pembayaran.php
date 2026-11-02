<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenghuniModel;

/**
 * Pengelolaan tagihan & pembayaran (khusus admin).
 * Admin dapat mencatat tagihan baru, memverifikasi bukti transfer,
 * menolak bukti, atau mengubah status secara manual.
 */
class Pembayaran extends BaseController
{
    private const FOLDER_BUKTI = WRITEPATH . 'uploads/bukti/';

    public function index()
    {
        $model   = new PembayaranModel();
        $status  = (string) ($this->request->getGet('status') ?? '');
        $keyword = (string) ($this->request->getGet('q') ?? '');

        $model->select('pembayaran.*, penghuni.nama AS nama_penghuni, kamar.nomor_kamar')
            ->join('penghuni', 'penghuni.id = pembayaran.penghuni_id', 'left')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left');

        if ($status !== '') {
            $model->where('pembayaran.status', $status);
        }

        if ($keyword !== '') {
            $model->like('penghuni.nama', $keyword);
        }

        service('pager')->only(['status', 'q']);

        return view('admin/pembayaran/index', [
            'title'   => 'Kelola Pembayaran',
            'bayar'   => $model->orderBy('pembayaran.tahun', 'DESC')
                ->orderBy('pembayaran.bulan', 'DESC')
                ->paginate(10),
            'pager'   => $model->pager,
            'status'  => $status,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/pembayaran/form', [
            'title'    => 'Tambah Tagihan',
            'bayar'    => null,
            'penghuni' => $this->penghuniAktif(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->aturanValidasi(), $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model      = new PembayaranModel();
        $penghuniId = (int) $this->request->getPost('penghuni_id');
        $bulan      = (int) $this->request->getPost('bulan');
        $tahun      = (int) $this->request->getPost('tahun');

        if ($model->where('penghuni_id', $penghuniId)->where('bulan', $bulan)->where('tahun', $tahun)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['bulan' => 'Tagihan untuk periode ini sudah ada.']);
        }

        $status = (string) $this->request->getPost('status');

        $tersimpan = $model->insert([
            'penghuni_id'   => $penghuniId,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'jumlah'        => (int) $this->request->getPost('jumlah'),
            'status'        => $status,
            'tanggal_bayar' => in_array($status, ['lunas', 'menunggu_verifikasi'], true)
                ? date('Y-m-d')
                : null,
            'catatan'       => (string) $this->request->getPost('catatan'),
        ]);

        if ($tersimpan === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(site_url('admin/pembayaran'))->with('sukses', 'Tagihan berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $bayar = (new PembayaranModel())->find((int) $id);

        if ($bayar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pembayaran tidak ditemukan.');
        }

        return view('admin/pembayaran/form', [
            'title'    => 'Edit Tagihan',
            'bayar'    => $bayar,
            'penghuni' => $this->penghuniAktif((int) $bayar['penghuni_id']),
        ]);
    }

    public function update($id = null)
    {
        $model = new PembayaranModel();
        $bayar = $model->find((int) $id);

        if ($bayar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pembayaran tidak ditemukan.');
        }

        if (! $this->validate($this->aturanValidasi(), $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penghuniId = (int) $this->request->getPost('penghuni_id');
        $bulan      = (int) $this->request->getPost('bulan');
        $tahun      = (int) $this->request->getPost('tahun');

        $bentrok = $model->where('penghuni_id', $penghuniId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('id !=', (int) $id)
            ->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['bulan' => 'Tagihan untuk periode ini sudah ada.']);
        }

        $status = (string) $this->request->getPost('status');

        $tersimpan = $model->update((int) $id, [
            'penghuni_id'   => $penghuniId,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'jumlah'        => (int) $this->request->getPost('jumlah'),
            'status'        => $status,
            'tanggal_bayar' => in_array($status, ['lunas', 'menunggu_verifikasi'], true)
                ? ($bayar['tanggal_bayar'] ?? date('Y-m-d'))
                : null,
            'catatan'       => (string) $this->request->getPost('catatan'),
        ]);

        if ($tersimpan === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(site_url('admin/pembayaran'))->with('sukses', 'Tagihan berhasil diperbarui.');
    }

    public function verifikasi($id = null)
    {
        return $this->ubahStatus((int) $id, 'lunas', 'Pembayaran diverifikasi oleh admin.');
    }

    public function tolak($id = null)
    {
        return $this->ubahStatus((int) $id, 'ditolak', (string) ($this->request->getPost('catatan') ?: 'Bukti transfer ditolak admin.'));
    }

    public function delete($id = null)
    {
        $model = new PembayaranModel();
        $bayar = $model->find((int) $id);

        if ($bayar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pembayaran tidak ditemukan.');
        }

        $model->delete((int) $id);
        hapus_berkas($bayar['bukti_transfer'] === null ? null : self::FOLDER_BUKTI . $bayar['bukti_transfer']);

        return redirect()->to(site_url('admin/pembayaran'))->with('sukses', 'Tagihan berhasil dihapus.');
    }

    private function ubahStatus(int $id, string $status, string $catatan)
    {
        $model = new PembayaranModel();
        $bayar = $model->find($id);

        if ($bayar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pembayaran tidak ditemukan.');
        }

        $data = [
            'status'  => $status,
            'catatan' => $catatan,
        ];

        if ($status === 'lunas') {
            $data['tanggal_bayar'] = date('Y-m-d');
        }

        $model->update($id, $data);

        $pesan = $status === 'lunas' ? 'Pembayaran ditandai lunas.' : 'Pembayaran ditolak.';

        return redirect()->to(site_url('admin/pembayaran'))->with('sukses', $pesan);
    }

    private function penghuniAktif(?int $idTambahan = null): array
    {
        $db = db_connect();

        $builder = $db->table('penghuni')
            ->select('penghuni.id, penghuni.nama, kamar.nomor_kamar, kamar.harga_bulanan')
            ->join('kamar', 'kamar.id = penghuni.kamar_id', 'left')
            ->where('penghuni.status', 'aktif');

        if ($idTambahan !== null) {
            $builder->orWhere('penghuni.id', $idTambahan);
        }

        return $builder->orderBy('penghuni.nama', 'ASC')->get()->getResultArray();
    }

    private function aturanValidasi(): array
    {
        return [
            'penghuni_id' => 'required|integer',
            'bulan'       => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
            'tahun'       => 'required|integer|greater_than[2000]',
            'jumlah'      => 'required|integer|greater_than[0]',
            'status'      => 'required|in_list[belum_bayar,menunggu_verifikasi,lunas,ditolak]',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'penghuni_id' => ['required' => 'Penghuni wajib dipilih.'],
            'bulan'       => [
                'required'              => 'Bulan wajib dipilih.',
                'greater_than_equal_to' => 'Bulan harus antara 1 sampai 12.',
                'less_than_equal_to'    => 'Bulan harus antara 1 sampai 12.',
            ],
            'tahun'  => ['required' => 'Tahun wajib diisi.', 'integer' => 'Tahun harus berupa angka.'],
            'jumlah' => [
                'required'     => 'Jumlah tagihan wajib diisi.',
                'integer'      => 'Jumlah harus berupa angka.',
                'greater_than' => 'Jumlah harus lebih besar dari nol.',
            ],
            'status' => ['in_list' => 'Status pembayaran tidak dikenal.'],
        ];
    }
}
