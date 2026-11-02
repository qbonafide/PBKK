<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ------------------------------------------------------------------
// Publik
// ------------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('katalog', 'Katalog::index');
$routes->get('produk/(:segment)', 'Katalog::detail/$1');

// Keranjang (session)
$routes->get('keranjang', 'Keranjang::index');
$routes->post('keranjang', 'Keranjang::tambah');
$routes->post('keranjang/update', 'Keranjang::update');
$routes->post('keranjang/hapus', 'Keranjang::hapus');

// ------------------------------------------------------------------
// Autentikasi
// ------------------------------------------------------------------
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('daftar', 'Auth::register');
$routes->post('daftar', 'Auth::simpanRegister');
$routes->get('logout', 'Auth::logout');

// ------------------------------------------------------------------
// Checkout & pesanan pembeli (wajib login)
// ------------------------------------------------------------------
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('checkout', 'Checkout::index');
    $routes->post('checkout', 'Checkout::proses');
    $routes->get('checkout/masuk', 'Checkout::simpanTujuan');

    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::detail/$1');
    $routes->post('pesanan/(:num)/bukti', 'Pesanan::uploadBukti/$1');
    $routes->post('pesanan/(:num)/batal', 'Pesanan::batal/$1');

    // Bukti transfer disimpan privat, diakses lewat controller ini.
    $routes->get('berkas/bukti/(:segment)', 'Berkas::bukti/$1');
});

// ------------------------------------------------------------------
// Panel admin
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('kategori', 'Kategori::index');
    $routes->get('kategori/create', 'Kategori::create');
    $routes->post('kategori', 'Kategori::store');
    $routes->get('kategori/(:num)/edit', 'Kategori::edit/$1');
    $routes->post('kategori/(:num)', 'Kategori::update/$1');
    $routes->post('kategori/(:num)/delete', 'Kategori::delete/$1');

    $routes->get('produk', 'Produk::index');
    $routes->get('produk/create', 'Produk::create');
    $routes->post('produk', 'Produk::store');
    $routes->get('produk/(:num)/edit', 'Produk::edit/$1');
    $routes->post('produk/(:num)', 'Produk::update/$1');
    $routes->post('produk/(:num)/delete', 'Produk::delete/$1');

    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::detail/$1');
    $routes->post('pesanan/(:num)/status', 'Pesanan::ubahStatus/$1');
});
