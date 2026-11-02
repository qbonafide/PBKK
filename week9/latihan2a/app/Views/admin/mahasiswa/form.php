<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $mhs !== null;
$action = $edit ? site_url('admin/mahasiswa/' . $mhs['id']) : site_url('admin/mahasiswa');
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $edit ? 'Edit mahasiswa' : 'Tambah mahasiswa' ?></h6>
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
                            <label for="nim">NIM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nim" name="nim"
                                   value="<?= esc(old('nim', $mhs['nim'] ?? '')) ?>" required>
                            <small class="form-text text-muted">
                                NIM dipakai sebagai username akun dan password awal.
                            </small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nama">Nama mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   value="<?= esc(old('nama', $mhs['nama'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prodi_id">Program studi <span class="text-danger">*</span></label>
                            <select class="form-control" id="prodi_id" name="prodi_id" required>
                                <option value="">-- pilih prodi --</option>
                                <?php foreach ($prodi as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"
                                        <?= (int) old('prodi_id', $mhs['prodi_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['kode']) ?> - <?= esc($p['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dosen_wali_id">Dosen wali</label>
                            <select class="form-control" id="dosen_wali_id" name="dosen_wali_id">
                                <option value="">-- belum ditentukan --</option>
                                <?php foreach ($dosen as $d): ?>
                                    <option value="<?= esc($d['id']) ?>"
                                        <?= (int) old('dosen_wali_id', $mhs['dosen_wali_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>>
                                        <?= esc($d['nama']) ?> (<?= esc($d['nip']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="angkatan">Angkatan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="angkatan" name="angkatan"
                                   min="2000" max="2100"
                                   value="<?= esc(old('angkatan', $mhs['angkatan'] ?? date('Y'))) ?>" required>
                        </div>

                        <div class="form-group col-md-8">
                            <label for="no_hp">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp"
                                   value="<?= esc(old('no_hp', $mhs['no_hp'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= esc(old('alamat', $mhs['alamat'] ?? '')) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan
                    </button>
                    <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
