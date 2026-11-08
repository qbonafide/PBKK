<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Beranda') ?> &middot; ShopEase</title>
    <link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <?= $this->include('partials/navbar') ?>

    <main class="flex-grow-1">
        <?= $this->include('partials/flash') ?>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
