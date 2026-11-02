<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdiModel;

/**
 * CRUD program studi. Penghapusan ditolak jika prodi masih dipakai
 * mahasiswa atau mata kuliah, supaya data tidak menggantung.
 */
class Prodi extends BaseController
{
    public function index()
    {
        $model   = new ProdiModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        if ($keyword !== '') {
            $model->groupStart()->like('nama', $keyword)->orLike('kode', $keyword)->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/prodi/index', [
            'title'   => 'Kelola Program Studi',
            'prodi'   => $model->orderBy('kode', 'ASC')->paginate(8),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/prodi/form', ['title' => 'Tambah Program Studi', 'prodi' => null]);
    }

    public function store()
    {
        if (! $this->validate(
            ['kode' => 'required|max_length[10]', 'nama' => 'required|min_length[3]|max_length[100]'],
            [
                'kode' => ['required' => 'Kode prodi wajib diisi.'],
                'nama' => [
                    'required'   => 'Nama prodi wajib diisi.',
                    'min_length' => 'Nama prodi minimal 3 karakter.',
                ],
            ]
        )) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new ProdiModel();
        $kode  = strtoupper((string) $this->request->getPost('kode'));

        if ($model->where('kode', $kode)->first() !== null) {
            return redirect()->back()->withInput()->with('errors', ['kode' => 'Kode prodi sudah dipakai.']);
        }

        $model->insert(['kode' => $kode, 'nama' => (string) $this->request->getPost('nama')]);

        return redirect()->to(site_url('admin/prodi'))->with('sukses', 'Program studi berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $prodi = (new ProdiModel())->find((int) $id);

        if ($prodi === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Program studi tidak ditemukan.');
        }

        return view('admin/prodi/form', ['title' => 'Edit Program Studi', 'prodi' => $prodi]);
    }

    public function update($id = null)
    {
        $model = new ProdiModel();
        $prodi = $model->find((int) $id);

        if ($prodi === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Program studi tidak ditemukan.');
        }

        if (! $this->validate(
            ['kode' => 'required|max_length[10]', 'nama' => 'required|min_length[3]|max_length[100]'],
            [
                'kode' => ['required' => 'Kode prodi wajib diisi.'],
                'nama' => ['required' => 'Nama prodi wajib diisi.', 'min_length' => 'Nama prodi minimal 3 karakter.'],
            ]
        )) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kode    = strtoupper((string) $this->request->getPost('kode'));
        $bentrok = $model->where('kode', $kode)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()->with('errors', ['kode' => 'Kode prodi sudah dipakai prodi lain.']);
        }

        $model->update((int) $id, ['kode' => $kode, 'nama' => (string) $this->request->getPost('nama')]);

        return redirect()->to(site_url('admin/prodi'))->with('sukses', 'Program studi berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model = new ProdiModel();
        $prodi = $model->find((int) $id);

        if ($prodi === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Program studi tidak ditemukan.');
        }

        $db = db_connect();
        $dipakaiMahasiswa = $db->table('mahasiswa')->where('prodi_id', (int) $id)->countAllResults() > 0;
        $dipakaiMatkul    = $db->table('matakuliah')->where('prodi_id', (int) $id)->countAllResults() > 0;

        if ($dipakaiMahasiswa || $dipakaiMatkul) {
            return redirect()->to(site_url('admin/prodi'))
                ->with('error', 'Prodi masih dipakai data mahasiswa atau mata kuliah.');
        }

        $model->delete((int) $id);

        return redirect()->to(site_url('admin/prodi'))->with('sukses', 'Program studi berhasil dihapus.');
    }
}
