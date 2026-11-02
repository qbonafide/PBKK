<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Controller autentikasi: login, proses login, dan logout.
 * Setelah login berhasil, session ID diregenerasi untuk mencegah session
 * fixation, lalu data user disimpan di session.
 */
class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return $this->arahkanSesuaiRole();
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function attempt()
    {
        $aturan = [
            'username' => 'required',
            'password' => 'required',
        ];

        $pesan = [
            'username' => ['required' => 'Username wajib diisi.'],
            'password' => ['required' => 'Password wajib diisi.'],
        ];

        if (! $this->validate($aturan, $pesan)) {
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

        // Regenerasi session ID setelah login (mencegah session fixation).
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

        return redirect()->to(site_url('/'))->with('sukses', 'Anda sudah keluar dari akun.');
    }

    private function arahkanSesuaiRole()
    {
        if (session()->get('role') === 'admin') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return redirect()->to(site_url('penghuni/dashboard'));
    }
}
