<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &middot; SIAKAD</title>
    <link rel="stylesheet" href="<?= base_url('assets/sbadmin/css/sb-admin-2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sbadmin/vendor/fontawesome-free/css/all.min.css') ?>">
</head>
<body class="bg-gradient-primary">

<div class="container">

    <div class="row justify-content-center">

        <div class="col-xl-5 col-lg-6 col-md-8">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center">
                            <div class="h3 text-gray-800 font-weight-bold mb-1">SIAKAD</div>
                            <p class="text-muted small mb-4">
                                Sistem Informasi Akademik &middot; admin, dosen, dan mahasiswa
                            </p>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger small"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <?php $errors = session('errors') ?? []; ?>

                        <?php if ($errors !== []): ?>
                            <div class="alert alert-danger small">
                                <ul class="mb-0 pl-3">
                                    <?php foreach ($errors as $pesan): ?>
                                        <li><?= esc($pesan) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form class="user" action="<?= site_url('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="form-group">
                                <input type="text" name="username" class="form-control form-control-user"
                                       placeholder="Username (admin / NIP / NIM)" value="<?= esc(old('username')) ?>" required autofocus>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user"
                                       placeholder="Password" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Masuk
                            </button>
                        </form>

                        <hr>

                        <div class="small text-muted">
                            <div class="font-weight-bold text-gray-700 mb-1">Akun demo</div>
                            Admin: <code>admin</code> / <code>admin123</code><br>
                            Dosen: <code>dosen1</code> / <code>dosen123</code><br>
                            Mahasiswa: <code>2025001</code> / <code>mhs123</code>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script src="<?= base_url('assets/sbadmin/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/js/sb-admin-2.min.js') ?>"></script>
</body>
</html>
