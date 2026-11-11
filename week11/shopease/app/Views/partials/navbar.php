<?php
/**
 * Navbar publik.
 * Semua tautan memakai url_to()/site_url() agar tidak ada URL hard-code.
 */
$user = current_user();
$isAdminArea = has_role('admin', 'operator');
?>
<nav class="navbar navbar-expand-lg se-navbar sticky-top py-3">
    <div class="container">
        <a class="navbar-brand se-brand" href="<?= site_url('/') ?>">Shop<span>Ease</span></a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navUtama"
                aria-controls="navUtama" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navUtama">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link se-nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link se-nav-link <?= url_is('catalog*') ? 'active' : '' ?>" href="<?= site_url('catalog') ?>">Katalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link se-nav-link <?= url_is('about') ? 'active' : '' ?>" href="<?= site_url('about') ?>">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link se-nav-link <?= url_is('contact') ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Kontak</a>
                </li>
            </ul>

            <form class="d-flex se-search me-lg-3 mb-2 mb-lg-0" action="<?= site_url('catalog') ?>" method="get" role="search">
                <label for="navSearch" class="visually-hidden">Cari produk</label>
                <input type="search" class="form-control" id="navSearch" name="q" value="<?= esc(service('request')->getGet('q') ?? '') ?>"
                       placeholder="Cari produk...">
                <button class="btn btn-brand" type="submit">Cari</button>
            </form>

            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <?php if ($user === null) : ?>
                    <li class="nav-item">
                        <a class="nav-link se-nav-link" href="<?= site_url('login') ?>">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-brand btn-sm ms-lg-2" href="<?= site_url('register') ?>">Daftar</a>
                    </li>
                <?php else : ?>
                    <?php if ($isAdminArea) : ?>
                        <li class="nav-item">
                            <a class="nav-link se-nav-link" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link se-nav-link <?= url_is('cart*') ? 'active' : '' ?>" href="<?= site_url('cart') ?>">Keranjang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link se-nav-link <?= url_is('orders*') ? 'active' : '' ?>" href="<?= site_url('orders') ?>">Pesanan</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link se-nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= esc($user['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><span class="dropdown-item-text small text-soft">Masuk sebagai <?= esc($user['role']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= site_url('logout') ?>" method="post" class="px-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
