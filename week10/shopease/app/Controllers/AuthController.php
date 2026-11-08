<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Registrasi, login, dan logout.
 */
class AuthController extends BaseController
{
    /**
     * Formulir login.
     */
    public function login(): string
    {
        return view('auth/login', ['title' => 'Masuk']);
    }

    /**
     * Proses login: pesan gagal sengaja generik agar tidak membocorkan
     * apakah sebuah email terdaftar atau tidak (PRD bagian 13).
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->findActiveByEmail($email);

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau kata sandi tidak cocok.');
        }

        // Cegah session fixation.
        $this->session->regenerate();
        $this->session->set([
            'user_id'   => (int) $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        $target = in_array($user['role'], ['admin', 'operator'], true) ? 'admin/dashboard' : '';

        return redirect()->to(site_url($target))
            ->with('success', 'Selamat datang, ' . $user['name'] . '.');
    }

    /**
     * Formulir registrasi customer.
     */
    public function register(): string
    {
        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    /**
     * Proses registrasi: email unik, password minimal 8 karakter,
     * password disimpan sebagai hash.
     */
    public function attemptRegister()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'phone'            => 'permit_empty|max_length[20]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email tersebut sudah terdaftar. Silakan masuk.',
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi kata sandi tidak sama.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->insert([
            'name'          => (string) $this->request->getPost('name'),
            'email'         => (string) $this->request->getPost('email'),
            'phone'         => (string) ($this->request->getPost('phone') ?? ''),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'customer',
            'is_active'     => 1,
        ]);

        return redirect()->to(site_url('login'))
            ->with('success', 'Akun berhasil dibuat. Silakan masuk dengan email dan kata sandi Anda.');
    }

    /**
     * Logout: hapus session lalu kembali ke beranda.
     */
    public function logout()
    {
        $this->session->destroy();

        return redirect()->to(site_url('/'))->with('info', 'Anda telah keluar dari akun.');
    }
}
