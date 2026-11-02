<?php
/**
 * Sidebar SB Admin 2. Menu ditampilkan sesuai role yang sedang login:
 * admin melihat semua menu pengelolaan, penghuni hanya dashboard & tagihan.
 */
$role     = session()->get('role');
$menuAktif = service('request')->getPath();

$menuAdmin = [
    ['url' => 'admin/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'admin/kamar', 'label' => 'Kamar', 'ikon' => 'fa-door-open'],
    ['url' => 'admin/penghuni', 'label' => 'Penghuni', 'ikon' => 'fa-users'],
    ['url' => 'admin/pembayaran', 'label' => 'Pembayaran', 'ikon' => 'fa-money-bill-wave'],
    ['url' => 'admin/users', 'label' => 'Pengguna', 'ikon' => 'fa-user-cog'],
];

$menuPenghuni = [
    ['url' => 'penghuni/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'penghuni/tagihan', 'label' => 'Tagihan Saya', 'ikon' => 'fa-file-invoice-dollar'],
];

$menu = $role === 'admin' ? $menuAdmin : $menuPenghuni;
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('/') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-home"></i>
        </div>
        <div class="sidebar-brand-text mx-3">KosKu</div>
    </a>

    <hr class="sidebar-divider my-0">

    <?php foreach ($menu as $item): ?>
        <?php $aktif = str_starts_with($menuAktif, $item['url']); ?>
        <li class="nav-item <?= $aktif ? 'active' : '' ?>">
            <a class="nav-link" href="<?= site_url($item['url']) ?>">
                <i class="fas <?= esc($item['ikon']) ?>"></i>
                <span><?= esc($item['label']) ?></span>
            </a>
        </li>
    <?php endforeach; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
