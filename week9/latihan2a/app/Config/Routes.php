<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ------------------------------------------------------------------
// Publik & autentikasi
// ------------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// ------------------------------------------------------------------
// Panel admin
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Program studi
    $routes->get('prodi', 'Prodi::index');
    $routes->get('prodi/create', 'Prodi::create');
    $routes->post('prodi', 'Prodi::store');
    $routes->get('prodi/(:num)/edit', 'Prodi::edit/$1');
    $routes->post('prodi/(:num)', 'Prodi::update/$1');
    $routes->post('prodi/(:num)/delete', 'Prodi::delete/$1');

    // Dosen
    $routes->get('dosen', 'Dosen::index');
    $routes->get('dosen/create', 'Dosen::create');
    $routes->post('dosen', 'Dosen::store');
    $routes->get('dosen/(:num)/edit', 'Dosen::edit/$1');
    $routes->post('dosen/(:num)', 'Dosen::update/$1');
    $routes->post('dosen/(:num)/delete', 'Dosen::delete/$1');

    // Mahasiswa
    $routes->get('mahasiswa', 'Mahasiswa::index');
    $routes->get('mahasiswa/create', 'Mahasiswa::create');
    $routes->post('mahasiswa', 'Mahasiswa::store');
    $routes->get('mahasiswa/(:num)/edit', 'Mahasiswa::edit/$1');
    $routes->post('mahasiswa/(:num)', 'Mahasiswa::update/$1');
    $routes->post('mahasiswa/(:num)/delete', 'Mahasiswa::delete/$1');

    // Mata kuliah
    $routes->get('matakuliah', 'Matakuliah::index');
    $routes->get('matakuliah/create', 'Matakuliah::create');
    $routes->post('matakuliah', 'Matakuliah::store');
    $routes->get('matakuliah/(:num)/edit', 'Matakuliah::edit/$1');
    $routes->post('matakuliah/(:num)', 'Matakuliah::update/$1');
    $routes->post('matakuliah/(:num)/delete', 'Matakuliah::delete/$1');
});

// ------------------------------------------------------------------
// Panel dosen
// ------------------------------------------------------------------
$routes->group('dosen', ['filter' => ['auth', 'role:dosen'], 'namespace' => 'App\Controllers\Dosen'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('kelas', 'Nilai::index');
    $routes->get('nilai/(:num)', 'Nilai::form/$1');
    $routes->post('nilai/(:num)', 'Nilai::simpan/$1');
    $routes->get('perwalian', 'Perwalian::index');
});

// ------------------------------------------------------------------
// Panel mahasiswa
// ------------------------------------------------------------------
$routes->group('mahasiswa', ['filter' => ['auth', 'role:mahasiswa'], 'namespace' => 'App\Controllers\Mahasiswa'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profil', 'Dashboard::profil');
    $routes->get('krs', 'Krs::index');
    $routes->post('krs', 'Krs::simpan');
    $routes->get('krs/cetak', 'Krs::cetak');
    $routes->get('khs', 'Khs::index');
    $routes->get('khs/cetak', 'Khs::cetak');
});
