<?php
/**
 * Sidebar SIAKAD. Menu berbeda untuk tiap role:
 * admin mengelola master data, dosen mengelola kelas & nilai,
 * mahasiswa mengisi KRS dan melihat KHS.
 */
$role      = session()->get('role');
$menuAktif = service('request')->getPath();

$menuAdmin = [
    ['url' => 'admin/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'admin/prodi', 'label' => 'Program Studi', 'ikon' => 'fa-university'],
    ['url' => 'admin/dosen', 'label' => 'Dosen', 'ikon' => 'fa-chalkboard-teacher'],
    ['url' => 'admin/mahasiswa', 'label' => 'Mahasiswa', 'ikon' => 'fa-user-graduate'],
    ['url' => 'admin/matakuliah', 'label' => 'Mata Kuliah', 'ikon' => 'fa-book'],
];

$menuDosen = [
    ['url' => 'dosen/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'dosen/kelas', 'label' => 'Kelas Saya', 'ikon' => 'fa-chalkboard'],
    ['url' => 'dosen/perwalian', 'label' => 'Mahasiswa Perwalian', 'ikon' => 'fa-user-friends'],
];

$menuMahasiswa = [
    ['url' => 'mahasiswa/dashboard', 'label' => 'Dashboard', 'ikon' => 'fa-tachometer-alt'],
    ['url' => 'mahasiswa/krs', 'label' => 'Isi KRS', 'ikon' => 'fa-clipboard-list'],
    ['url' => 'mahasiswa/khs', 'label' => 'KHS & IPK', 'ikon' => 'fa-scroll'],
    ['url' => 'mahasiswa/profil', 'label' => 'Profil', 'ikon' => 'fa-id-card'],
];

$menu = match ($role) {
    'admin'     => $menuAdmin,
    'dosen'     => $menuDosen,
    'mahasiswa' => $menuMahasiswa,
    default     => [],
};
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('/') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SIAKAD</div>
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
