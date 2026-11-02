<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $prodi !== null;
$action = $edit ? site_url('admin/prodi/' . $prodi['id']) : site_url('admin/prodi');
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $edit ? 'Edit program studi' : 'Tambah program studi' ?></h6>
            </div>
            <div class="card-body">
                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3 small">
                            <?php foreach ($errors as $pesan): ?>
                                <li><?= esc($pesan) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= $action ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="kode">Kode prodi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode" name="kode" maxlength="10"
                               value="<?= esc(old('kode', $prodi['kode'] ?? '')) ?>" required>
                        <small class="form-text text-muted">Contoh: IF, SI, TI.</small>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama program studi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= esc(old('nama', $prodi['nama'] ?? '')) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan
                    </button>
                    <a href="<?= site_url('admin/prodi') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
