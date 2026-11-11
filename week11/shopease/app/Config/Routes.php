<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Peta routing ShopEase.
 *
 * Prinsip yang dipakai:
 * 1. Semua route diberi nama (opsi `as`) supaya view memakai url_to()/route_to()
 *    dan tidak ada URL yang ditulis manual di template.
 * 2. Aksi yang mengubah data hanya boleh POST (hapus, ubah status, upload,
 *    approve/reject) sehingga tidak bisa dipicu lewat tautan atau prefetch.
 * 3. Otorisasi diletakkan di lapisan route memakai filter `auth:role`,
 *    bukan di dalam view.
 *
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------------------
// Halaman publik
// ---------------------------------------------------------------------------
$routes->get('/', 'HomeController::index', ['as' => 'home']);
$routes->get('about', 'HomeController::about', ['as' => 'about']);
$routes->get('contact', 'HomeController::contact', ['as' => 'contact']);

$routes->get('catalog', 'ProductController::catalog', ['as' => 'catalog']);
$routes->get('product/(:num)', 'ProductController::detail/$1', ['as' => 'product.detail']);

// ---------------------------------------------------------------------------
// Autentikasi (hanya untuk tamu)
// ---------------------------------------------------------------------------
$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin', ['as' => 'login.attempt']);
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister', ['as' => 'register.attempt']);
});

$routes->post('logout', 'AuthController::logout', ['as' => 'logout', 'filter' => 'auth']);

// ---------------------------------------------------------------------------
// Area customer (wajib login sebagai customer)
// ---------------------------------------------------------------------------
$routes->group('', ['filter' => 'auth:customer'], static function (RouteCollection $routes): void {
    // Keranjang
    $routes->get('cart', 'CartController::index', ['as' => 'cart.index']);
    $routes->post('cart/add', 'CartController::add', ['as' => 'cart.add']);
    $routes->post('cart/update', 'CartController::update', ['as' => 'cart.update']);
    $routes->post('cart/remove/(:num)', 'CartController::remove/$1', ['as' => 'cart.remove']);

    // Checkout
    $routes->get('checkout', 'CheckoutController::index', ['as' => 'checkout.index']);
    $routes->post('checkout/address', 'CheckoutController::storeAddress', ['as' => 'checkout.address']);
    $routes->post('checkout/process', 'CheckoutController::process', ['as' => 'checkout.process']);

    // Pesanan milik sendiri
    $routes->get('orders', 'OrderController::index', ['as' => 'orders.index']);
    $routes->get('orders/(:num)', 'OrderController::detail/$1', ['as' => 'orders.detail']);
    $routes->post('orders/(:num)/payment', 'OrderController::uploadProof/$1', ['as' => 'orders.payment']);
});

// ---------------------------------------------------------------------------
// Area admin & operator
// ---------------------------------------------------------------------------
$routes->group('admin', ['filter' => 'auth:admin,operator'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'Admin\DashboardController::index', ['as' => 'admin.dashboard']);

    // Pesanan: operator ikut memproses, tetapi tidak mengelola katalog.
    $routes->get('orders', 'Admin\OrderController::index', ['as' => 'admin.orders.index']);
    $routes->get('orders/(:num)', 'Admin\OrderController::detail/$1', ['as' => 'admin.orders.detail']);
    $routes->post('orders/(:num)/status', 'Admin\OrderController::updateStatus/$1', ['as' => 'admin.orders.status']);

    // Pengiriman: operator mengisi kurir & nomor resi.
    $routes->post('shipments/(:num)', 'Admin\ShipmentController::store/$1', ['as' => 'admin.shipments.store']);
});

$routes->group('admin', ['filter' => 'auth:admin'], static function (RouteCollection $routes): void {
    // Kategori (pola resource, tanpa metode GET untuk aksi yang mengubah data)
    $routes->get('categories', 'Admin\CategoryController::index', ['as' => 'admin.categories.index']);
    $routes->get('categories/create', 'Admin\CategoryController::create', ['as' => 'admin.categories.create']);
    $routes->post('categories', 'Admin\CategoryController::store', ['as' => 'admin.categories.store']);
    $routes->get('categories/(:num)/edit', 'Admin\CategoryController::edit/$1', ['as' => 'admin.categories.edit']);
    $routes->post('categories/(:num)', 'Admin\CategoryController::update/$1', ['as' => 'admin.categories.update']);
    $routes->post('categories/(:num)/delete', 'Admin\CategoryController::delete/$1', ['as' => 'admin.categories.delete']);

    // Produk
    $routes->get('products', 'Admin\ProductController::index', ['as' => 'admin.products.index']);
    $routes->get('products/create', 'Admin\ProductController::create', ['as' => 'admin.products.create']);
    $routes->post('products', 'Admin\ProductController::store', ['as' => 'admin.products.store']);
    $routes->get('products/(:num)/edit', 'Admin\ProductController::edit/$1', ['as' => 'admin.products.edit']);
    $routes->post('products/(:num)', 'Admin\ProductController::update/$1', ['as' => 'admin.products.update']);
    $routes->post('products/(:num)/delete', 'Admin\ProductController::delete/$1', ['as' => 'admin.products.delete']);

    // Verifikasi pembayaran
    $routes->get('payments', 'Admin\PaymentController::index', ['as' => 'admin.payments.index']);
    $routes->post('payments/(:num)/approve', 'Admin\PaymentController::approve/$1', ['as' => 'admin.payments.approve']);
    $routes->post('payments/(:num)/reject', 'Admin\PaymentController::reject/$1', ['as' => 'admin.payments.reject']);

    // Laporan
    $routes->get('reports', 'Admin\ReportController::index', ['as' => 'admin.reports.index']);
});
