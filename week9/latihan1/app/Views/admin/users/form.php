<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $user !== null;
$action = $edit ? site_url('admin/users/' . $user['id']) : site_url('admin/users');
?>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?= $edit ? 'Edit pengguna' : 'Tambah pengguna baru' ?>
                </h6>
            </div>
            <div class="card-body">

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger">
                        <strong>Periksa kembali isian berikut:</strong>
                        <ul class="mb-0 mt-1 small">
                            <?php foreach ($errors as $pesan): ?>
                                <li><?= esc($pesan) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= $action ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="nama">Nama lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= esc(old('nama', $user['nama'] ?? '')) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role" name="role">
                                <?php foreach (['admin' => 'Admin', 'penghuni' => 'Penghuni'] as $nilai => $label): ?>
                                    <option value="<?= esc($nilai) ?>"
                                        <?= old('role', $user['role'] ?? 'penghuni') === $nilai ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= esc(old('email', $user['email'] ?? '')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Password <?= $edit ? '' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               <?= $edit ? '' : 'required' ?> autocomplete="new-password">
                        <small class="form-text text-muted">
                            <?= $edit
                                ? 'Kosongkan jika tidak ingin mengubah password.'
                                : 'Minimal 6 karakter. Password disimpan dalam bentuk hash.' ?>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> <?= $edit ? 'Simpan perubahan' : 'Simpan pengguna' ?>
                    </button>
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
