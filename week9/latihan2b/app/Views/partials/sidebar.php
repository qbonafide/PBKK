<?php
$menuAktif = service('request')->getPath();

$menu = [
    ['url' => 'admin/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'admin/kategori', 'label' => 'Kategori', 'ikon' => 'fa-tags'],
    ['url' => 'admin/produk', 'label' => 'Produk', 'ikon' => 'fa-tshirt'],
    ['url' => 'admin/pesanan', 'label' => 'Pesanan', 'ikon' => 'fa-shopping-bag'],
];
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('/') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-tshirt"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ThriftIn</div>
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
