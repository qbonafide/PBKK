<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $dosen !== null;
$action = $edit ? site_url('admin/dosen/' . $dosen['id']) : site_url('admin/dosen');
?>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $edit ? 'Edit dosen' : 'Tambah dosen' ?></h6>
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

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nip">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nip" name="nip"
                                   value="<?= esc(old('nip', $dosen['nip'] ?? '')) ?>" required>
                            <small class="form-text text-muted">
                                NIP dipakai sebagai username akun dan password awal.
                            </small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nama">Nama dosen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   value="<?= esc(old('nama', $dosen['nama'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= esc(old('email', $dosen['email'] ?? '')) ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="no_hp">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp"
                                   value="<?= esc(old('no_hp', $dosen['no_hp'] ?? '')) ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan
                    </button>
                    <a href="<?= site_url('admin/dosen') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
