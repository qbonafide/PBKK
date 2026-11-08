<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dashboard') ?> &middot; ShopEase Admin</title>
    <link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="se-admin-body">
    <div class="se-admin-shell">
        <?= $this->include('partials/admin_sidebar') ?>

        <div class="se-admin-main">
            <header class="se-admin-topbar">
                <div>
                    <div class="se-muted-label"><?= esc($section ?? 'Administrasi') ?></div>
                    <h1 class="h5 mb-0"><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= site_url('/') ?>" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex">Lihat toko</a>
                    <div class="text-end">
                        <div class="fw-semibold small"><?= esc(current_user()['name'] ?? '') ?></div>
                        <div class="text-soft small text-capitalize"><?= esc(current_user()['role'] ?? '') ?></div>
                    </div>
                </div>
            </header>

            <main class="se-admin-content">
                <?= $this->include('partials/flash') ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
