<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar &middot; ThriftIn</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>
<body>
<div class="halaman-auth">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card kartu-auth">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="brand-badge mx-auto mb-2" style="width:44px;height:44px;font-size:22px;">T</div>
                            <h1 class="h4 fw-bold mb-1">Daftar akun pembeli</h1>
                            <p class="text-muted small mb-0">Belanja lebih cepat, riwayat pesanan tersimpan.</p>
                        </div>

                        <?php $errors = session('errors') ?? []; ?>

                        <?php if ($errors !== []): ?>
                            <div class="alert alert-danger small">
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($errors as $pesan): ?>
                                        <li><?= esc($pesan) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('daftar') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                           value="<?= esc(old('nama')) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                           value="<?= esc(old('username')) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= esc(old('email')) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                                           value="<?= esc(old('no_hp')) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat pengiriman</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required><?= esc(old('alamat')) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           minlength="6" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ulangi" class="form-label">Ulangi password</label>
                                    <input type="password" class="form-control" id="ulangi" name="ulangi"
                                           minlength="6" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-thrift w-100">Daftar</button>
                        </form>

                        <p class="small text-center mt-3 mb-0">
                            Sudah punya akun?
                            <a href="<?= site_url('login') ?>" class="text-decoration-none fw-semibold">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
