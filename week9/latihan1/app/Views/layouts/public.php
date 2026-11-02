<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'KosKu') ?> &middot; KosKu</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-kosku sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('/') ?>">
            <span class="brand-dot">K</span> KosKu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUtama"
                aria-controls="navUtama" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navUtama">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('kamar*') ? 'active' : '' ?>" href="<?= site_url('kamar') ?>">Daftar Kamar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('tentang') ? 'active' : '' ?>" href="<?= site_url('tentang') ?>">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('kontak') ? 'active' : '' ?>" href="<?= site_url('kontak') ?>">Kontak</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                <?php if (session()->get('user_id')): ?>
                    <?php $tujuan = session()->get('role') === 'admin' ? 'admin/dashboard' : 'penghuni/dashboard'; ?>
                    <a class="btn btn-light btn-sm px-3" href="<?= site_url($tujuan) ?>">Dashboard</a>
                    <a class="btn btn-outline-light btn-sm px-3" href="<?= site_url('logout') ?>">Keluar</a>
                <?php else: ?>
                    <a class="btn btn-light btn-sm px-3 fw-semibold" href="<?= site_url('login') ?>">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if (session()->getFlashdata('sukses')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('sukses')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
<?php endif; ?>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer class="footer-kosku mt-5">
    <div class="container py-4">
        <div class="row gy-3">
            <div class="col-md-5">
                <h5 class="fw-bold mb-2">KosKu</h5>
                <p class="mb-0 small">
                    Kos putri &amp; putra dengan kamar bersih, harga transparan, dan pengelolaan
                    pembayaran yang rapi. Dikelola langsung oleh pemilik.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-semibold mb-2">Navigasi</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a href="<?= site_url('kamar') ?>">Daftar Kamar</a></li>
                    <li><a href="<?= site_url('tentang') ?>">Tentang</a></li>
                    <li><a href="<?= site_url('kontak') ?>">Kontak</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2">Kontak</h6>
                <p class="small mb-0">
                    Jl. Kaliurang KM 5, Sleman, Yogyakarta<br>
                    WhatsApp: 0812-3456-7890<br>
                    Email: halo@kosku.test
                </p>
            </div>
        </div>
        <hr class="border-light opacity-25 my-3">
        <p class="small mb-0 text-center">&copy; <?= date('Y') ?> KosKu &middot; Tugas PBKK Framework CodeIgniter</p>
    </div>
</footer>

<script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
