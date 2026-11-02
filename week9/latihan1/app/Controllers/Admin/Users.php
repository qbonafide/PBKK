<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenghuniModel;
use App\Models\UserModel;

/**
 * CRUD akun pengguna (khusus admin).
 * Password selalu di-hash; saat edit, kolom password boleh dikosongkan
 * artinya password lama dipertahankan.
 */
class Users extends BaseController
{
    public function index()
    {
        $model   = new UserModel();
        $keyword = (string) ($this->request->getGet('q') ?? '');

        if ($keyword !== '') {
            $model->groupStart()
                ->like('nama', $keyword)
                ->orLike('username', $keyword)
                ->groupEnd();
        }

        service('pager')->only(['q']);

        return view('admin/users/index', [
            'title'   => 'Kelola Pengguna',
            'users'   => $model->orderBy('role', 'ASC')->orderBy('nama', 'ASC')->paginate(8),
            'pager'   => $model->pager,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/users/form', [
            'title' => 'Tambah Pengguna',
            'user'  => null,
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->aturanValidasi(), $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model    = new UserModel();
        $username = (string) $this->request->getPost('username');

        if ($model->where('username', $username)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['username' => 'Username sudah dipakai.']);
        }

        $tersimpan = $model->insert([
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $username,
            'email'    => (string) $this->request->getPost('email'),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => (string) $this->request->getPost('role'),
        ]);

        if ($tersimpan === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(site_url('admin/users'))
            ->with('sukses', 'Pengguna ' . $username . ' berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $user = (new UserModel())->find((int) $id);

        if ($user === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.');
        }

        return view('admin/users/form', [
            'title' => 'Edit Pengguna ' . $user['username'],
            'user'  => $user,
        ]);
    }

    public function update($id = null)
    {
        $model = new UserModel();
        $user  = $model->find((int) $id);

        if ($user === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.');
        }

        $aturan             = $this->aturanValidasi();
        $aturan['password'] = 'permit_empty|min_length[6]';

        if (! $this->validate($aturan, $this->pesanValidasi())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost('username');
        $bentrok  = $model->where('username', $username)->where('id !=', (int) $id)->first();

        if ($bentrok !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['username' => 'Username sudah dipakai pengguna lain.']);
        }

        $roleBaru = (string) $this->request->getPost('role');

        // Admin terakhir tidak boleh diturunkan rolenya, supaya tidak terkunci.
        if ($user['role'] === 'admin' && $roleBaru !== 'admin' && $this->jumlahAdmin() <= 1) {
            return redirect()->back()->withInput()
                ->with('errors', ['role' => 'Minimal harus ada satu admin aktif.']);
        }

        $data = [
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $username,
            'email'    => (string) $this->request->getPost('email'),
            'role'     => $roleBaru,
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($model->update((int) $id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // Jika akun sendiri diubah, perbarui data di session.
        if ((int) session()->get('user_id') === (int) $id) {
            session()->set(['nama' => $data['nama'], 'role' => $data['role']]);
        }

        return redirect()->to(site_url('admin/users'))
            ->with('sukses', 'Pengguna ' . $username . ' berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $model = new UserModel();
        $user  = $model->find((int) $id);

        if ($user === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.');
        }

        if ((int) session()->get('user_id') === (int) $id) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if ($user['role'] === 'admin' && $this->jumlahAdmin() <= 1) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', 'Minimal harus ada satu admin aktif.');
        }

        $penghuni = (new PenghuniModel())->where('user_id', (int) $id)->first();
        if ($penghuni !== null) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', 'Akun ini masih terhubung dengan data penghuni. Hapus data penghuninya dulu.');
        }

        $model->delete((int) $id);

        return redirect()->to(site_url('admin/users'))
            ->with('sukses', 'Pengguna ' . $user['username'] . ' berhasil dihapus.');
    }

    private function jumlahAdmin(): int
    {
        return (new UserModel())->where('role', 'admin')->countAllResults();
    }

    private function aturanValidasi(): array
    {
        return [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => 'required|alpha_numeric|min_length[3]|max_length[50]',
            'email'    => 'permit_empty|valid_email|max_length[100]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,penghuni]',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nama'     => ['required' => 'Nama wajib diisi.', 'min_length' => 'Nama minimal 3 karakter.'],
            'username' => [
                'required'      => 'Username wajib diisi.',
                'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
                'min_length'    => 'Username minimal 3 karakter.',
            ],
            'email'    => ['valid_email' => 'Format email tidak valid.'],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'role' => ['in_list' => 'Role harus admin atau penghuni.'],
        ];
    }
}
