<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="display-4 fw-bold text-warning">404</div>
                    <h1 class="h4 fw-bold mt-2">Halaman tidak ditemukan</h1>
                    <p class="text-muted mb-4">
                        <?= esc($message ?? 'Halaman yang Anda cari tidak ada atau sudah dipindahkan.') ?>
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">Beranda</a>
                        <a href="<?= site_url('kamar') ?>" class="btn btn-success">Lihat daftar kamar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
