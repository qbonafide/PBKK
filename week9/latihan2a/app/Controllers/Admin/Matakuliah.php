<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\MatakuliahModel;
use App\Models\ProdiModel;

/**
 * CRUD mata kuliah (kelas). Mata kuliah yang sudah punya peserta KRS
 * tidak boleh dihapus agar data nilai mahasiswa tidak ikut hilang.
 */
class Matakuliah extends BaseController
{
    public function index()
    {
        $model   = new MatakuliahModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        $model->select('matakuliah.*, prodi.nama AS nama_prodi, dosen.nama AS nama_dosen')
            ->join('prodi', 'prodi.id = matakuliah.prodi_id', 'left')
            ->join('dosen', 'dosen.id = matakuliah.dosen_id', 'left');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('matakuliah.nama', $keyword)
                ->orLike('matakuliah.kode', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/matakuliah/index', [
            'title'   => 'Kelola Mata Kuliah',
            'matkul'  => $model->orderBy('matakuliah.semester', 'ASC')->orderBy('matakuliah.kode', 'ASC')->paginate(10),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/matakuliah/form', [
            'title'  => 'Tambah Mata Kuliah',
            'matkul' => null,
            'prodi'  => (new ProdiModel())->orderBy('nama', 'ASC')->findAll(),
            'dosen'  => (new DosenModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new MatakuliahModel();
        $kode  = strtoupper((string) $this->request->getPost('kode'));

        if ($model->where('kode', $kode)->first() !== null) {
            return redirect()->back()->withInput()->with('errors', ['kode' => 'Kode mata kuliah sudah dipakai.']);
        }

        $model->insert($this->dataDariForm($kode));

        return redirect()->to(site_url('admin/matakuliah'))->with('sukses', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $matkul = (new MatakuliahModel())->find((int) $id);

        if ($matkul === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata kuliah tidak ditemukan.');
        }

        return view('admin/matakuliah/form', [
            'title'  => 'Edit Mata Kuliah',
            'matkul' => $matkul,
            'prodi'  => (new ProdiModel())->orderBy('nama', 'ASC')->findAll(),
            'dosen'  => (new DosenModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $model  = new MatakuliahModel();
        $matkul = $model->find((int) $id);

        if ($matkul === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata kuliah tidak ditemukan.');
        }

        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kode    = strtoupper((string) $this->request->getPost('kode'));
        $bentrok = $model->where('kode', $kode)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()->with('errors', ['kode' => 'Kode mata kuliah sudah dipakai mata kuliah lain.']);
        }

        $model->update((int) $id, $this->dataDariForm($kode));

        return redirect()->to(site_url('admin/matakuliah'))->with('sukses', 'Mata kuliah berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model  = new MatakuliahModel();
        $matkul = $model->find((int) $id);

        if ($matkul === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata kuliah tidak ditemukan.');
        }

        $db = db_connect();

        if ($db->table('krs')->where('matakuliah_id', (int) $id)->countAllResults() > 0) {
            return redirect()->to(site_url('admin/matakuliah'))
                ->with('error', 'Mata kuliah sudah diambil mahasiswa sehingga tidak bisa dihapus.');
        }

        $model->delete((int) $id);

        return redirect()->to(site_url('admin/matakuliah'))->with('sukses', 'Mata kuliah berhasil dihapus.');
    }

    private function dataDariForm(string $kode): array
    {
        return [
            'prodi_id'    => (int) $this->request->getPost('prodi_id'),
            'dosen_id'    => (int) $this->request->getPost('dosen_id'),
            'kode'        => $kode,
            'nama'        => (string) $this->request->getPost('nama'),
            'sks'         => (int) $this->request->getPost('sks'),
            'semester'    => (int) $this->request->getPost('semester'),
            'kuota'       => (int) $this->request->getPost('kuota'),
            'hari'        => (string) $this->request->getPost('hari'),
            'jam_mulai'   => (string) $this->request->getPost('jam_mulai'),
            'jam_selesai' => (string) $this->request->getPost('jam_selesai'),
            'ruang'       => (string) $this->request->getPost('ruang'),
        ];
    }

    private function aturan(): array
    {
        return [
            'prodi_id' => 'required|integer',
            'dosen_id' => 'required|integer',
            'kode'     => 'required|max_length[20]',
            'nama'     => 'required|min_length[3]|max_length[100]',
            'sks'      => 'required|integer|greater_than[0]|less_than_equal_to[6]',
            'semester' => 'required|integer|greater_than[0]|less_than_equal_to[8]',
            'kuota'    => 'required|integer|greater_than[0]',
            'hari'     => 'permit_empty|max_length[15]',
            'ruang'    => 'permit_empty|max_length[30]',
        ];
    }

    private function pesan(): array
    {
        return [
            'prodi_id' => ['required' => 'Program studi wajib dipilih.'],
            'dosen_id' => ['required' => 'Dosen pengampu wajib dipilih.'],
            'kode'     => ['required' => 'Kode mata kuliah wajib diisi.'],
            'nama'     => [
                'required'   => 'Nama mata kuliah wajib diisi.',
                'min_length' => 'Nama mata kuliah minimal 3 karakter.',
            ],
            'sks'      => [
                'required'           => 'SKS wajib diisi.',
                'greater_than'       => 'SKS harus lebih dari nol.',
                'less_than_equal_to' => 'SKS maksimal 6.',
            ],
            'semester' => [
                'required'           => 'Semester wajib diisi.',
                'less_than_equal_to' => 'Semester maksimal 8.',
            ],
            'kuota'    => [
                'required'     => 'Kuota wajib diisi.',
                'greater_than' => 'Kuota harus lebih dari nol.',
            ],
        ];
    }
}
