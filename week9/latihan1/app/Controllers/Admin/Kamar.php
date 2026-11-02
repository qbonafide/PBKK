<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PenghuniModel;

/**
 * CRUD kamar (khusus admin). Semua aksi tulis memakai POST + CSRF.
 * Foto kamar disimpan di public/uploads/kamar sehingga boleh tampil di
 * halaman publik.
 */
class Kamar extends BaseController
{
    private const FOLDER_FOTO = FCPATH . 'uploads/kamar/';

    public function index()
    {
        $model   = new KamarModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('nomor_kamar', $keyword)
                ->orLike('fasilitas', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/kamar/index', [
            'title'   => 'Kelola Kamar',
            'kamar'   => $model->orderBy('nomor_kamar', 'ASC')->paginate(8),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/kamar/form', [
            'title' => 'Tambah Kamar',
            'kamar' => null,
        ]);
    }

    public function store()
    {
        $aturan = $this->aturanValidasi();
        $file   = $this->request->getFile('foto');

        if ($file !== null && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $aturan['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]';
        }

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new KamarModel();
        $nomor = (string) $this->request->getPost('nomor_kamar');

        if ($model->where('nomor_kamar', $nomor)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['nomor_kamar' => 'Nomor kamar sudah dipakai.']);
        }

        $foto = simpan_upload($file, self::FOLDER_FOTO);

        $tersimpan = $model->insert([
            'nomor_kamar'   => $nomor,
            'slug'          => $model->buatSlug($nomor),
            'tipe'          => (string) $this->request->getPost('tipe'),
            'harga_bulanan' => (int) $this->request->getPost('harga_bulanan'),
            'luas'          => (string) $this->request->getPost('luas'),
            'fasilitas'     => (string) $this->request->getPost('fasilitas'),
            'status'        => (string) $this->request->getPost('status'),
            'foto'          => $foto,
        ]);

        if ($tersimpan === false) {
            hapus_berkas($foto === null ? null : self::FOLDER_FOTO . $foto);

            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(site_url('admin/kamar'))->with('sukses', 'Kamar ' . $nomor . ' berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $kamar = (new KamarModel())->find((int) $id);

        if ($kamar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kamar tidak ditemukan.');
        }

        return view('admin/kamar/form', [
            'title' => 'Edit Kamar ' . $kamar['nomor_kamar'],
            'kamar' => $kamar,
        ]);
    }

    public function update($id = null)
    {
        $model = new KamarModel();
        $kamar = $model->find((int) $id);

        if ($kamar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kamar tidak ditemukan.');
        }

        $aturan = $this->aturanValidasi();
        $file   = $this->request->getFile('foto');

        if ($file !== null && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $aturan['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]';
        }

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nomor   = (string) $this->request->getPost('nomor_kamar');
        $bentrok = $model->where('nomor_kamar', $nomor)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['nomor_kamar' => 'Nomor kamar sudah dipakai kamar lain.']);
        }

        $fotoBaru = simpan_upload($file, self::FOLDER_FOTO);

        $data = [
            'nomor_kamar'   => $nomor,
            'slug'          => $model->buatSlug($nomor, (int) $id),
            'tipe'          => (string) $this->request->getPost('tipe'),
            'harga_bulanan' => (int) $this->request->getPost('harga_bulanan'),
            'luas'          => (string) $this->request->getPost('luas'),
            'fasilitas'     => (string) $this->request->getPost('fasilitas'),
            'status'        => (string) $this->request->getPost('status'),
        ];

        if ($fotoBaru !== null) {
            $data['foto'] = $fotoBaru;
        }

        if ($model->update((int) $id, $data) === false) {
            hapus_berkas($fotoBaru === null ? null : self::FOLDER_FOTO . $fotoBaru);

            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // Foto lama baru dihapus setelah update berhasil.
        if ($fotoBaru !== null) {
            hapus_berkas($kamar['foto'] === null ? null : self::FOLDER_FOTO . $kamar['foto']);
        }

        return redirect()->to(site_url('admin/kamar'))->with('sukses', 'Kamar ' . $nomor . ' berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model = new KamarModel();
        $kamar = $model->find((int) $id);

        if ($kamar === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kamar tidak ditemukan.');
        }

        $masihDihuni = (new PenghuniModel())
            ->where('kamar_id', (int) $id)
            ->where('status', 'aktif')
            ->first();

        if ($masihDihuni !== null) {
            return redirect()->to(site_url('admin/kamar'))
                ->with('error', 'Kamar masih dihuni. Pindahkan atau tandai penghuni keluar terlebih dahulu.');
        }

        $model->delete((int) $id);
        hapus_berkas($kamar['foto'] === null ? null : self::FOLDER_FOTO . $kamar['foto']);

        return redirect()->to(site_url('admin/kamar'))->with('sukses', 'Kamar ' . $kamar['nomor_kamar'] . ' berhasil dihapus.');
    }

    private function aturanValidasi(): array
    {
        return [
            'nomor_kamar'   => 'required|max_length[20]',
            'tipe'          => 'required|in_list[standar,ac,premium]',
            'harga_bulanan' => 'required|integer|greater_than[0]',
            'luas'          => 'permit_empty|max_length[20]',
            'status'        => 'required|in_list[kosong,terisi,perbaikan]',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nomor_kamar' => ['required' => 'Nomor kamar wajib diisi.'],
            'tipe'        => ['in_list' => 'Tipe kamar tidak dikenal.'],
            'harga_bulanan' => [
                'required'     => 'Harga bulanan wajib diisi.',
                'integer'      => 'Harga harus berupa angka.',
                'greater_than' => 'Harga harus lebih besar dari nol.',
            ],
            'status' => ['in_list' => 'Status kamar tidak dikenal.'],
            'foto'   => [
                'is_image' => 'Berkas harus berupa gambar.',
                'mime_in'  => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
                'max_size' => 'Ukuran gambar maksimal 2 MB.',
            ],
        ];
    }
}
