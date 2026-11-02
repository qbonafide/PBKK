<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Autentikasi SIAKAD: satu halaman login untuk admin, dosen, dan mahasiswa.
 * Setelah login, session ID diregenerasi lalu pengguna diarahkan ke dashboard
 * sesuai rolenya.
 */
class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return $this->arahkanSesuaiRole();
        }

        return view('auth/login', ['title' => 'Login SIAKAD']);
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
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah. Silakan coba lagi.');
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

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'))->with('sukses', 'Anda sudah keluar dari akun.');
    }

    private function arahkanSesuaiRole()
    {
        return match (session()->get('role')) {
            'admin'     => redirect()->to(site_url('admin/dashboard')),
            'dosen'     => redirect()->to(site_url('dosen/dashboard')),
            'mahasiswa' => redirect()->to(site_url('mahasiswa/dashboard')),
            default     => redirect()->to(site_url('login')),
        };
    }
}
