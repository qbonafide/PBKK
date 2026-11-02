<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $matkul !== null;
$action = $edit ? site_url('admin/matakuliah/' . $matkul['id']) : site_url('admin/matakuliah');
?>

<div class="row">
    <div class="col-lg-9">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $edit ? 'Edit mata kuliah' : 'Tambah mata kuliah' ?></h6>
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
                        <div class="form-group col-md-4">
                            <label for="kode">Kode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode"
                                   value="<?= esc(old('kode', $matkul['kode'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group col-md-8">
                            <label for="nama">Nama mata kuliah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   value="<?= esc(old('nama', $matkul['nama'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prodi_id">Program studi <span class="text-danger">*</span></label>
                            <select class="form-control" id="prodi_id" name="prodi_id" required>
                                <option value="">-- pilih prodi --</option>
                                <?php foreach ($prodi as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"
                                        <?= (int) old('prodi_id', $matkul['prodi_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['kode']) ?> - <?= esc($p['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dosen_id">Dosen pengampu <span class="text-danger">*</span></label>
                            <select class="form-control" id="dosen_id" name="dosen_id" required>
                                <option value="">-- pilih dosen --</option>
                                <?php foreach ($dosen as $d): ?>
                                    <option value="<?= esc($d['id']) ?>"
                                        <?= (int) old('dosen_id', $matkul['dosen_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>>
                                        <?= esc($d['nama']) ?> (<?= esc($d['nip']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="sks">SKS <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="sks" name="sks" min="1" max="6"
                                   value="<?= esc(old('sks', $matkul['sks'] ?? 3)) ?>" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="semester">Semester <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="semester" name="semester" min="1" max="8"
                                   value="<?= esc(old('semester', $matkul['semester'] ?? 1)) ?>" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="kuota">Kuota <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kuota" name="kuota" min="1"
                                   value="<?= esc(old('kuota', $matkul['kuota'] ?? 30)) ?>" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="hari">Hari</label>
                            <select class="form-control" id="hari" name="hari">
                                <?php
                                $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $hariTerpilih = old('hari', $matkul['hari'] ?? 'Senin');
                                foreach ($daftarHari as $hari): ?>
                                    <option value="<?= esc($hari) ?>" <?= $hariTerpilih === $hari ? 'selected' : '' ?>>
                                        <?= esc($hari) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="jam_mulai">Jam mulai</label>
                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai"
                                   value="<?= esc(old('jam_mulai', $matkul['jam_mulai'] ?? '08:00')) ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="jam_selesai">Jam selesai</label>
                            <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                                   value="<?= esc(old('jam_selesai', $matkul['jam_selesai'] ?? '09:40')) ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="ruang">Ruang</label>
                            <input type="text" class="form-control" id="ruang" name="ruang"
                                   value="<?= esc(old('ruang', $matkul['ruang'] ?? '')) ?>"
                                   placeholder="Contoh: Lab RPL 1">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan
                    </button>
                    <a href="<?= site_url('admin/matakuliah') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
