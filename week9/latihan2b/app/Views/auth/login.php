<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk &middot; ThriftIn</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>
<body>
<div class="halaman-auth">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card kartu-auth">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="brand-badge mx-auto mb-2" style="width:44px;height:44px;font-size:22px;">T</div>
                            <h1 class="h4 fw-bold mb-1">Masuk ke ThriftIn</h1>
                            <p class="text-muted small mb-0">Pembeli dan admin memakai halaman login yang sama.</p>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger small"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('sukses')): ?>
                            <div class="alert alert-success small"><?= esc(session()->getFlashdata('sukses')) ?></div>
                        <?php endif; ?>

                        <?php $errors = session('errors') ?? []; ?>

                        <form action="<?= site_url('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= esc(old('username')) ?>" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-thrift w-100">Masuk</button>
                        </form>

                        <p class="small text-center mt-3 mb-0">
                            Belum punya akun?
                            <a href="<?= site_url('daftar') ?>" class="text-decoration-none fw-semibold">Daftar di sini</a>
                        </p>

                        <div class="mt-4 pt-3 border-top small text-muted">
                            <strong>Akun demo:</strong><br>
                            Admin: <code>admin</code> / <code>admin123</code><br>
                            Pembeli: <code>pembeli1</code> / <code>pembeli123</code>
                        </div>

                        <div class="text-center mt-3">
                            <a href="<?= site_url('/') ?>" class="small text-decoration-none">&larr; Kembali ke toko</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
