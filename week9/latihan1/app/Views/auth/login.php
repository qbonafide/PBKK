<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &middot; KosKu</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>">
</head>
<body>
<div class="halaman-login py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card kartu-login">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="brand-dot mx-auto mb-2" style="width:42px;height:42px;font-size:22px;">K</div>
                            <h1 class="h4 fw-bold mb-1">Masuk ke KosKu</h1>
                            <p class="text-muted small mb-0">Admin dan penghuni memakai halaman login yang sama.</p>
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
                                <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                       id="username" name="username" value="<?= esc(old('username')) ?>" autofocus required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       id="password" name="password" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Masuk</button>
                        </form>

                        <div class="mt-4 pt-3 border-top small text-muted">
                            <strong>Akun demo:</strong><br>
                            Admin: <code>admin</code> / <code>admin123</code><br>
                            Penghuni: <code>penghuni1</code> / <code>penghuni123</code>
                        </div>

                        <div class="text-center mt-3">
                            <a href="<?= site_url('/') ?>" class="small text-decoration-none">&larr; Kembali ke beranda</a>
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
