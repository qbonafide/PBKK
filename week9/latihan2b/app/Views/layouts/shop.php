<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'ThriftIn') ?> &middot; ThriftIn</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>
<body>

<?php
$role      = session()->get('role');
$keranjang = session()->get('keranjang');
$jumlahKeranjang = is_array($keranjang) ? array_sum($keranjang) : 0;
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-thrift sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('/') ?>">
            <span class="brand-badge">T</span> ThriftIn
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navToko"
                aria-controls="navToko" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navToko">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('katalog*') || url_is('produk*') ? 'active' : '' ?>" href="<?= site_url('katalog') ?>">Katalog</a>
                </li>
                <?php if ($role === 'pembeli'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('pesanan*') ? 'active' : '' ?>" href="<?= site_url('pesanan') ?>">Pesanan Saya</a>
                    </li>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Panel Admin</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-light btn-sm position-relative" href="<?= site_url('keranjang') ?>">
                    Keranjang
                    <?php if ($jumlahKeranjang > 0): ?>
                        <span class="badge rounded-pill bg-warning text-dark ms-1"><?= esc($jumlahKeranjang) ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($role !== null): ?>
                    <span class="text-white-50 small d-none d-lg-inline">Hai, <?= esc(session()->get('nama')) ?></span>
                    <a class="btn btn-light btn-sm" href="<?= site_url('logout') ?>">Keluar</a>
                <?php else: ?>
                    <a class="btn btn-light btn-sm" href="<?= site_url('login') ?>">Masuk</a>
                    <a class="btn btn-warning btn-sm text-dark fw-semibold" href="<?= site_url('daftar') ?>">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php foreach (['sukses' => 'success', 'error' => 'danger'] as $kunci => $kelas): ?>
    <?php if (session()->getFlashdata($kunci)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $kelas ?> alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata($kunci)) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer class="footer-thrift mt-5">
    <div class="container py-4">
        <div class="row gy-3">
            <div class="col-md-5">
                <h5 class="fw-bold mb-2">ThriftIn</h5>
                <p class="small mb-0">
                    Toko pakaian preloved pilihan. Setiap barang dicek kondisi dan dicuci
                    sebelum dikirim, jadi Anda tahu persis apa yang dibeli.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-semibold mb-2">Belanja</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a href="<?= site_url('katalog') ?>">Katalog</a></li>
                    <li><a href="<?= site_url('keranjang') ?>">Keranjang</a></li>
                    <li><a href="<?= site_url('pesanan') ?>">Pesanan saya</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2">Bantuan</h6>
                <p class="small mb-0">
                    Pengiriman flat Rp 20.000, gratis untuk belanja mulai Rp 200.000.<br>
                    Pesanan otomatis dibatalkan bila belum dibayar 24 jam.<br>
                    Email: halo@thriftin.test
                </p>
            </div>
        </div>
        <hr class="border-light opacity-25 my-3">
        <p class="small mb-0 text-center">&copy; <?= date('Y') ?> ThriftIn &middot; Tugas PBKK Framework CodeIgniter</p>
    </div>
</footer>

<script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
