<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;

/**
 * CRUD kategori. Kategori yang masih dipakai produk tidak boleh dihapus.
 */
class Kategori extends BaseController
{
    public function index()
    {
        return view('admin/kategori/index', [
            'title'    => 'Kelola Kategori',
            'kategori' => (new KategoriModel())->denganJumlahProduk(),
        ]);
    }

    public function create()
    {
        return view('admin/kategori/form', ['title' => 'Tambah Kategori', 'kategori' => null]);
    }

    public function store()
    {
        if (! $this->validate(
            ['nama' => 'required|min_length[3]|max_length[50]'],
            ['nama' => [
                'required'   => 'Nama kategori wajib diisi.',
                'min_length' => 'Nama kategori minimal 3 karakter.',
            ]]
        )) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new KategoriModel();
        $nama  = (string) $this->request->getPost('nama');

        $model->insert([
            'nama' => $nama,
            'slug' => $model->buatSlug($nama),
        ]);

        return redirect()->to(site_url('admin/kategori'))->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $kategori = (new KategoriModel())->find((int) $id);

        if ($kategori === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan.');
        }

        return view('admin/kategori/form', ['title' => 'Edit Kategori', 'kategori' => $kategori]);
    }

    public function update($id = null)
    {
        $model    = new KategoriModel();
        $kategori = $model->find((int) $id);

        if ($kategori === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan.');
        }

        if (! $this->validate(
            ['nama' => 'required|min_length[3]|max_length[50]'],
            ['nama' => [
                'required'   => 'Nama kategori wajib diisi.',
                'min_length' => 'Nama kategori minimal 3 karakter.',
            ]]
        )) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = (string) $this->request->getPost('nama');

        $model->update((int) $id, [
            'nama' => $nama,
            'slug' => $model->buatSlug($nama, (int) $id),
        ]);

        return redirect()->to(site_url('admin/kategori'))->with('sukses', 'Kategori berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model    = new KategoriModel();
        $kategori = $model->find((int) $id);

        if ($kategori === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan.');
        }

        $dipakai = (new ProdukModel())->where('kategori_id', (int) $id)->countAllResults();

        if ($dipakai > 0) {
            return redirect()->to(site_url('admin/kategori'))
                ->with('error', 'Kategori masih dipakai ' . $dipakai . ' produk.');
        }

        $model->delete((int) $id);

        return redirect()->to(site_url('admin/kategori'))->with('sukses', 'Kategori berhasil dihapus.');
    }
}
