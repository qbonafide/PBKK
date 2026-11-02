<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="display-4 fw-bold text-danger">403</div>
                    <h1 class="h4 fw-bold mt-2">Akses ditolak</h1>
                    <p class="text-muted mb-3">
                        Halaman ini hanya bisa dibuka oleh pengguna dengan hak akses tertentu.
                    </p>

                    <div class="alert alert-warning text-start small mb-4">
                        Anda masuk sebagai <strong><?= esc($role ?? 'tamu') ?></strong>.
                        Silakan login dengan akun yang sesuai atau kembali ke dashboard Anda.
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">Beranda</a>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-success">Dashboard Admin</a>
                        <?php elseif (session()->get('role') === 'penghuni'): ?>
                            <a href="<?= site_url('penghuni/dashboard') ?>" class="btn btn-success">Dashboard Penghuni</a>
                        <?php else: ?>
                            <a href="<?= site_url('login') ?>" class="btn btn-success">Masuk</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
