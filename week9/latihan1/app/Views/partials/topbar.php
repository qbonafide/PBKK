<?php
$nama = session()->get('nama') ?? 'Pengguna';
$role = session()->get('role') ?? '-';
?>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= esc($nama) ?> <span class="text-muted">(<?= esc($role) ?>)</span>
                </span>
                <img class="img-profile rounded-circle"
                     src="<?= base_url('assets/sbadmin/img/undraw_profile.svg') ?>" alt="Foto profil">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <?php if ($role === 'admin'): ?>
                    <a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a class="dropdown-item" href="<?= site_url('penghuni/dashboard') ?>">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i> Dashboard
                    </a>
                <?php endif; ?>
                <a class="dropdown-item" href="<?= site_url('/') ?>">
                    <i class="fas fa-globe fa-sm fa-fw mr-2 text-gray-400"></i> Halaman publik
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= site_url('logout') ?>">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Keluar
                </a>
            </div>
        </li>
    </ul>
</nav>
