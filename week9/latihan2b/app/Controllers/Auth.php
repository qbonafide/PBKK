<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Autentikasi pembeli & admin ThriftIn.
 *
 * Jika login dimulai dari halaman checkout, URL tujuan disimpan di session
 * (`intended`) lalu dipakai sekali setelah login berhasil.
 */
class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return $this->arahkanSesuaiRole();
        }

        return view('auth/login', ['title' => 'Masuk ke ThriftIn']);
    }

    public function attempt()
    {
        if (! $this->validate(
            ['username' => 'required', 'password' => 'required'],
            [
                'username' => ['required' => 'Username wajib diisi.'],
                'password' => ['required' => 'Password wajib diisi.'],
            ]
        )) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->cariBerdasarkanUsername($username);

        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        session()->regenerate(true);
        session()->set([
            'user_id'  => (int) $user['id'],
            'nama'     => $user['nama'],
            'role'     => $user['role'],
            'username' => $user['username'],
        ]);

        session()->setFlashdata('sukses', 'Selamat datang, ' . $user['nama'] . '.');

        return $this->arahkanSesuaiRole();
    }

    public function register()
    {
        if (session()->get('user_id')) {
            return $this->arahkanSesuaiRole();
        }

        return view('auth/register', ['title' => 'Daftar Akun ThriftIn']);
    }

    public function simpanRegister()
    {
        $aturan = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => 'required|alpha_numeric|min_length[3]|max_length[50]',
            'email'    => 'permit_empty|valid_email|max_length[100]',
            'no_hp'    => 'required|max_length[20]',
            'alamat'   => 'required|min_length[10]',
            'password' => 'required|min_length[6]',
            'ulangi'   => 'required|matches[password]',
        ];

        $pesan = [
            'nama'     => [
                'required'   => 'Nama wajib diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
            ],
            'username' => [
                'required'      => 'Username wajib diisi.',
                'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
                'min_length'    => 'Username minimal 3 karakter.',
            ],
            'email'    => ['valid_email' => 'Format email tidak valid.'],
            'no_hp'    => ['required' => 'Nomor HP wajib diisi.'],
            'alamat'   => [
                'required'   => 'Alamat wajib diisi.',
                'min_length' => 'Alamat minimal 10 karakter.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'ulangi'   => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak sama.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model    = new UserModel();
        $username = (string) $this->request->getPost('username');

        if ($model->where('username', $username)->first() !== null) {
            return redirect()->back()->withInput()
                ->with('errors', ['username' => 'Username sudah dipakai, silakan pilih yang lain.']);
        }

        $tersimpan = $model->insert([
            'nama'     => (string) $this->request->getPost('nama'),
            'username' => $username,
            'email'    => (string) $this->request->getPost('email'),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'pembeli',
            'no_hp'    => (string) $this->request->getPost('no_hp'),
            'alamat'   => (string) $this->request->getPost('alamat'),
        ]);

        if ($tersimpan === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        session()->regenerate(true);
        session()->set([
            'user_id'  => (int) $model->getInsertID(),
            'nama'     => (string) $this->request->getPost('nama'),
            'role'     => 'pembeli',
            'username' => $username,
        ]);

        $tujuan = session()->get('intended') ?: site_url('/');
        session()->remove('intended');

        return redirect()->to($tujuan)->with('sukses', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('/'))->with('sukses', 'Anda sudah keluar.');
    }

    private function arahkanSesuaiRole()
    {
        $tujuan = session()->get('intended');

        if ($tujuan !== null && $tujuan !== '') {
            session()->remove('intended');

            return redirect()->to($tujuan);
        }

        return session()->get('role') === 'admin'
            ? redirect()->to(site_url('admin/dashboard'))
            : redirect()->to(site_url('/'));
    }
}
