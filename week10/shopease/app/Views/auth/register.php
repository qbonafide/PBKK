<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container se-auth-wrap">
    <div class="se-auth-card">
        <div class="se-panel">
            <div class="se-panel-body p-4 p-lg-5">
                <h1 class="h3 mb-2">Daftar akun</h1>
                <p class="text-soft small mb-4">
                    Buat akun untuk mulai berbelanja.
                </p>

                <form method="post" action="<?= site_url('register') ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= esc(old('name')) ?>" placeholder="Nama sesuai identitas" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= esc(old('email')) ?>" placeholder="nama@contoh.test" required>
                        <div class="form-text">Email dipakai untuk masuk dan harus belum terdaftar.</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor telepon <span class="text-soft fw-normal">(opsional)</span></label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="<?= esc(old('phone')) ?>" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label for="password" class="form-label">Kata sandi</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="password_confirm" class="form-label">Ulangi kata sandi</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                   placeholder="Ulangi kata sandi" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2">Buat akun</button>
                </form>

                <p class="small text-soft text-center mt-4 mb-0">
                    Sudah punya akun? <a href="<?= site_url('login') ?>" class="fw-semibold">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
