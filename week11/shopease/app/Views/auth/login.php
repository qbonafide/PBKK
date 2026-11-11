<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container se-auth-wrap">
    <div class="se-auth-card">
        <div class="se-panel">
            <div class="se-panel-body p-4 p-lg-5">
                <h1 class="h3 mb-2">Masuk</h1>
                <p class="text-soft small mb-4">
                    Gunakan email dan kata sandi akun Anda.
                </p>

                <form method="post" action="<?= site_url('login') ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= esc(old('email')) ?>" placeholder="nama@contoh.test" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Kata sandi</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Masukkan kata sandi" required>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2">Masuk</button>
                </form>

                <p class="small text-soft text-center mt-4 mb-0">
                    Belum punya akun? <a href="<?= site_url('register') ?>" class="fw-semibold">Daftar</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
