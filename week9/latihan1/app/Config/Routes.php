<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ------------------------------------------------------------------
// Halaman publik (tanpa login)
// ------------------------------------------------------------------
$routes->get('/', 'Pages::index');
$routes->get('tentang', 'Pages::about');
$routes->get('kontak', 'Pages::contact');
$routes->post('kontak', 'Pages::sendContact');
$routes->get('kamar', 'Kamar::index');
$routes->get('kamar/(:segment)', 'Kamar::detail/$1');

// ------------------------------------------------------------------
// Autentikasi
// ------------------------------------------------------------------
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// ------------------------------------------------------------------
// Berkas privat (KTP & bukti transfer) -> dicek hak akses di controller
// ------------------------------------------------------------------
$routes->group('berkas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('ktp/(:segment)', 'Berkas::ktp/$1');
    $routes->get('bukti/(:segment)', 'Berkas::bukti/$1');
});

// ------------------------------------------------------------------
// Panel admin (wajib login + role admin)
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('kamar', 'Kamar::index');
    $routes->get('kamar/create', 'Kamar::create');
    $routes->post('kamar', 'Kamar::store');
    $routes->get('kamar/(:num)/edit', 'Kamar::edit/$1');
    $routes->post('kamar/(:num)', 'Kamar::update/$1');
    $routes->post('kamar/(:num)/delete', 'Kamar::delete/$1');

    $routes->get('penghuni', 'Penghuni::index');
    $routes->get('penghuni/create', 'Penghuni::create');
    $routes->post('penghuni', 'Penghuni::store');
    $routes->get('penghuni/(:num)/edit', 'Penghuni::edit/$1');
    $routes->post('penghuni/(:num)', 'Penghuni::update/$1');
    $routes->post('penghuni/(:num)/delete', 'Penghuni::delete/$1');

    $routes->get('pembayaran', 'Pembayaran::index');
    $routes->get('pembayaran/create', 'Pembayaran::create');
    $routes->post('pembayaran', 'Pembayaran::store');
    $routes->get('pembayaran/(:num)/edit', 'Pembayaran::edit/$1');
    $routes->post('pembayaran/(:num)', 'Pembayaran::update/$1');
    $routes->post('pembayaran/(:num)/delete', 'Pembayaran::delete/$1');
    $routes->post('pembayaran/(:num)/verifikasi', 'Pembayaran::verifikasi/$1');
    $routes->post('pembayaran/(:num)/tolak', 'Pembayaran::tolak/$1');

    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::create');
    $routes->post('users', 'Users::store');
    $routes->get('users/(:num)/edit', 'Users::edit/$1');
    $routes->post('users/(:num)', 'Users::update/$1');
    $routes->post('users/(:num)/delete', 'Users::delete/$1');
});

// ------------------------------------------------------------------
// Panel penghuni (wajib login + role penghuni)
// ------------------------------------------------------------------
$routes->group('penghuni', ['filter' => ['auth', 'role:penghuni'], 'namespace' => 'App\Controllers\Penghuni'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('tagihan', 'Tagihan::index');
    $routes->post('tagihan/(:num)/bayar', 'Tagihan::uploadBukti/$1');
});
