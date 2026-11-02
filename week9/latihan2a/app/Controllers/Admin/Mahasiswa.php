<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\UserModel;

/**
 * CRUD mahasiswa. Seperti pada dosen, akun login dibuat otomatis
 * (username = NIM, password awal = NIM) di dalam satu transaksi database.
 */
class Mahasiswa extends BaseController
{
    public function index()
    {
        $model   = new MahasiswaModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        // Join dan filter dipasang langsung pada model supaya ikut dipakai
        // saat paginate() menghitung baris (Query Builder terpisah tidak terpakai).
        $model->select('mahasiswa.*, prodi.nama AS nama_prodi, prodi.kode AS kode_prodi,
                        dosen.nama AS nama_wali, users.username')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id', 'left')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->join('users', 'users.id = mahasiswa.user_id', 'left');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('mahasiswa.nama', $keyword)
                ->orLike('mahasiswa.nim', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/mahasiswa/index', [
            'title'   => 'Kelola Mahasiswa',
            'mhs'     => $model->orderBy('mahasiswa.nim', 'ASC')->paginate(10),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/mahasiswa/form', [
            'title'    => 'Tambah Mahasiswa',
            'mhs'      => null,
            'prodi'    => (new ProdiModel())->orderBy('nama', 'ASC')->findAll(),
            'dosen'    => (new DosenModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nim       = (string) $this->request->getPost('nim');
        $user      = new UserModel();
        $mahasiswa = new MahasiswaModel();

        if ($user->where('username', $nim)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['nim' => 'NIM sudah dipakai sebagai username akun lain.']);
        }

        if ($mahasiswa->where('nim', $nim)->first() !== null) {
            return redirect()->back()->withInput()->with('errors', ['nim' => 'NIM sudah terdaftar.']);
        }

        $dosenWali = (string) $this->request->getPost('dosen_wali_id');

        $db = db_connect();
        $db->transStart();

        $user->insert([
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $nim,
            'password' => password_hash($nim, PASSWORD_DEFAULT),
            'role'     => 'mahasiswa',
        ]);

        $mahasiswa->insert([
            'user_id'       => (int) $user->getInsertID(),
            'prodi_id'      => (int) $this->request->getPost('prodi_id'),
            'dosen_wali_id' => $dosenWali === '' ? null : (int) $dosenWali,
            'nim'           => $nim,
            'nama'          => (string) $this->request->getPost('nama'),
            'angkatan'      => (int) $this->request->getPost('angkatan'),
            'alamat'        => (string) $this->request->getPost('alamat'),
            'no_hp'         => (string) $this->request->getPost('no_hp'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['umum' => 'Gagal menyimpan mahasiswa. Perubahan dibatalkan.']);
        }

        return redirect()->to(site_url('admin/mahasiswa'))
            ->with('sukses', 'Mahasiswa ditambahkan. Akun login memakai username NIM dengan password awal sama dengan NIM.');
    }

    public function edit($id = null)
    {
        $mhs = (new MahasiswaModel())->find((int) $id);

        if ($mhs === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mahasiswa tidak ditemukan.');
        }

        return view('admin/mahasiswa/form', [
            'title' => 'Edit Mahasiswa',
            'mhs'   => $mhs,
            'prodi' => (new ProdiModel())->orderBy('nama', 'ASC')->findAll(),
            'dosen' => (new DosenModel())->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $model = new MahasiswaModel();
        $mhs   = $model->find((int) $id);

        if ($mhs === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mahasiswa tidak ditemukan.');
        }

        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nim     = (string) $this->request->getPost('nim');
        $bentrok = $model->where('nim', $nim)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()->with('errors', ['nim' => 'NIM sudah dipakai mahasiswa lain.']);
        }

        $dosenWali = (string) $this->request->getPost('dosen_wali_id');

        $db = db_connect();
        $db->transStart();

        $model->update((int) $id, [
            'prodi_id'      => (int) $this->request->getPost('prodi_id'),
            'dosen_wali_id' => $dosenWali === '' ? null : (int) $dosenWali,
            'nim'           => $nim,
            'nama'          => (string) $this->request->getPost('nama'),
            'angkatan'      => (int) $this->request->getPost('angkatan'),
            'alamat'        => (string) $this->request->getPost('alamat'),
            'no_hp'         => (string) $this->request->getPost('no_hp'),
        ]);

        (new UserModel())->update((int) $mhs['user_id'], [
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $nim,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['umum' => 'Gagal memperbarui mahasiswa. Perubahan dibatalkan.']);
        }

        return redirect()->to(site_url('admin/mahasiswa'))->with('sukses', 'Data mahasiswa berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model = new MahasiswaModel();
        $mhs   = $model->find((int) $id);

        if ($mhs === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mahasiswa tidak ditemukan.');
        }

        $db = db_connect();

        if ($db->table('krs')->where('mahasiswa_id', (int) $id)->countAllResults() > 0) {
            return redirect()->to(site_url('admin/mahasiswa'))
                ->with('error', 'Mahasiswa masih punya data KRS. Hapus KRS-nya dulu.');
        }

        $db->transStart();
        (new UserModel())->delete((int) $mhs['user_id']);
        $db->transComplete();

        return redirect()->to(site_url('admin/mahasiswa'))->with('sukses', 'Mahasiswa beserta akunnya berhasil dihapus.');
    }

    private function aturan(): array
    {
        return [
            'prodi_id' => 'required|integer',
            'nim'      => 'required|max_length[20]',
            'nama'     => 'required|min_length[3]|max_length[100]',
            'angkatan' => 'required|integer|greater_than[1999]|less_than[2101]',
            'alamat'   => 'permit_empty|max_length[255]',
            'no_hp'    => 'permit_empty|max_length[20]',
        ];
    }

    private function pesan(): array
    {
        return [
            'prodi_id' => ['required' => 'Program studi wajib dipilih.'],
            'nim'      => ['required' => 'NIM wajib diisi.'],
            'nama'     => [
                'required'   => 'Nama mahasiswa wajib diisi.',
                'min_length' => 'Nama mahasiswa minimal 3 karakter.',
            ],
            'angkatan' => [
                'required'     => 'Angkatan wajib diisi.',
                'integer'      => 'Angkatan harus berupa angka tahun.',
                'greater_than' => 'Angkatan tidak valid.',
                'less_than'    => 'Angkatan tidak valid.',
            ],
        ];
    }
}
