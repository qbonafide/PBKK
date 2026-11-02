<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\UserModel;

/**
 * CRUD dosen. Akun login dosen dibuat otomatis di transaksi yang sama:
 * username = NIP, password awal = NIP (di-hash). Jika salah satu insert gagal,
 * semuanya dibatalkan sehingga tidak ada akun tanpa profil atau sebaliknya.
 */
class Dosen extends BaseController
{
    public function index()
    {
        $model   = new DosenModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        $model->select('dosen.*, users.username')
            ->join('users', 'users.id = dosen.user_id', 'left');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('dosen.nama', $keyword)
                ->orLike('dosen.nip', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/dosen/index', [
            'title'   => 'Kelola Dosen',
            'dosen'   => $model->orderBy('dosen.nama', 'ASC')->paginate(8),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/dosen/form', ['title' => 'Tambah Dosen', 'dosen' => null]);
    }

    public function store()
    {
        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nip   = (string) $this->request->getPost('nip');
        $user  = new UserModel();
        $dosen = new DosenModel();

        if ($user->where('username', $nip)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['nip' => 'NIP sudah dipakai sebagai username akun lain.']);
        }

        if ($dosen->where('nip', $nip)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['nip' => 'NIP sudah terdaftar.']);
        }

        $db = db_connect();
        $db->transStart();

        $user->insert([
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $nip,
            'password' => password_hash($nip, PASSWORD_DEFAULT),
            'role'     => 'dosen',
        ]);

        $dosen->insert([
            'user_id' => (int) $user->getInsertID(),
            'nip'     => $nip,
            'nama'    => (string) $this->request->getPost('nama'),
            'email'   => (string) $this->request->getPost('email'),
            'no_hp'   => (string) $this->request->getPost('no_hp'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['umum' => 'Gagal menyimpan dosen. Perubahan dibatalkan.']);
        }

        return redirect()->to(site_url('admin/dosen'))
            ->with('sukses', 'Dosen ditambahkan. Akun login memakai username NIP dengan password awal sama dengan NIP.');
    }

    public function edit($id = null)
    {
        $dosen = (new DosenModel())->find((int) $id);

        if ($dosen === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Dosen tidak ditemukan.');
        }

        return view('admin/dosen/form', ['title' => 'Edit Dosen', 'dosen' => $dosen]);
    }

    public function update($id = null)
    {
        $model = new DosenModel();
        $dosen = $model->find((int) $id);

        if ($dosen === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Dosen tidak ditemukan.');
        }

        if (! $this->validate($this->aturan(), $this->pesan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nip     = (string) $this->request->getPost('nip');
        $bentrok = $model->where('nip', $nip)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()->with('errors', ['nip' => 'NIP sudah dipakai dosen lain.']);
        }

        $db = db_connect();
        $db->transStart();

        $model->update((int) $id, [
            'nip'   => $nip,
            'nama'  => (string) $this->request->getPost('nama'),
            'email' => (string) $this->request->getPost('email'),
            'no_hp' => (string) $this->request->getPost('no_hp'),
        ]);

        // Username mengikuti NIP supaya konsisten dengan aturan pembuatan akun.
        (new UserModel())->update((int) $dosen['user_id'], [
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $nip,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['umum' => 'Gagal memperbarui dosen. Perubahan dibatalkan.']);
        }

        return redirect()->to(site_url('admin/dosen'))->with('sukses', 'Data dosen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model = new DosenModel();
        $dosen = $model->find((int) $id);

        if ($dosen === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Dosen tidak ditemukan.');
        }

        $db = db_connect();

        if ($db->table('matakuliah')->where('dosen_id', (int) $id)->countAllResults() > 0) {
            return redirect()->to(site_url('admin/dosen'))
                ->with('error', 'Dosen masih mengampu mata kuliah. Pindahkan mata kuliahnya dulu.');
        }

        $db->transStart();
        // Menghapus akun ikut menghapus profil dosen (foreign key ON DELETE CASCADE).
        (new UserModel())->delete((int) $dosen['user_id']);
        $db->transComplete();

        return redirect()->to(site_url('admin/dosen'))->with('sukses', 'Dosen beserta akunnya berhasil dihapus.');
    }

    private function aturan(): array
    {
        return [
            'nip'   => 'required|max_length[30]',
            'nama'  => 'required|min_length[3]|max_length[100]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'no_hp' => 'permit_empty|max_length[20]',
        ];
    }

    private function pesan(): array
    {
        return [
            'nip'   => ['required' => 'NIP wajib diisi.'],
            'nama'  => [
                'required'   => 'Nama dosen wajib diisi.',
                'min_length' => 'Nama dosen minimal 3 karakter.',
            ],
            'email' => ['valid_email' => 'Format email tidak valid.'],
        ];
    }
}
