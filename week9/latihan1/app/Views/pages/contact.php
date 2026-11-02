<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h1 class="bagian-judul h2 mb-3">Hubungi KosKu</h1>
                <p class="text-muted">
                    Ada pertanyaan soal kamar, harga, atau survei lokasi? Kirim pesan lewat form di
                    samping, atau hubungi kami langsung.
                </p>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="mb-2"><strong>Alamat</strong><br>Jl. Kaliurang KM 5, Sleman, Yogyakarta</p>
                        <p class="mb-2"><strong>WhatsApp</strong><br>0812-3456-7890</p>
                        <p class="mb-2"><strong>Email</strong><br>halo@kosku.test</p>
                        <p class="mb-0"><strong>Jam operasional</strong><br>Senin&ndash;Sabtu, 08.00&ndash;17.00 WIB</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Kirim pesan</h2>

                        <form action="<?= site_url('kontak') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama lengkap</label>
                                <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                                       id="nama" name="nama" value="<?= esc(old('nama')) ?>" required>
                                <?php if (isset($errors['nama'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['nama']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       id="email" name="email" value="<?= esc(old('email')) ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="pesan" class="form-label">Pesan</label>
                                <textarea class="form-control <?= isset($errors['pesan']) ? 'is-invalid' : '' ?>"
                                          id="pesan" name="pesan" rows="5" required><?= esc(old('pesan')) ?></textarea>
                                <?php if (isset($errors['pesan'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['pesan']) ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-success px-4">Kirim pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
