<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $kamar !== null;
$action = $edit ? site_url('admin/kamar/' . $kamar['id']) : site_url('admin/kamar');
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?= $edit ? 'Edit kamar' : 'Tambah kamar baru' ?>
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

                <form action="<?= $action ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="nomor_kamar">Nomor kamar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['nomor_kamar']) ? 'is-invalid' : '' ?>"
                                   id="nomor_kamar" name="nomor_kamar"
                                   value="<?= esc(old('nomor_kamar', $kamar['nomor_kamar'] ?? '')) ?>" required>
                            <?php if (isset($errors['nomor_kamar'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['nomor_kamar']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tipe">Tipe <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipe" name="tipe">
                                <?php foreach (['standar' => 'Standar', 'ac' => 'AC', 'premium' => 'Premium'] as $nilai => $label): ?>
                                    <option value="<?= esc($nilai) ?>"
                                        <?= old('tipe', $kamar['tipe'] ?? 'standar') === $nilai ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status">
                                <?php foreach (['kosong' => 'Kosong', 'terisi' => 'Terisi', 'perbaikan' => 'Perbaikan'] as $nilai => $label): ?>
                                    <option value="<?= esc($nilai) ?>"
                                        <?= old('status', $kamar['status'] ?? 'kosong') === $nilai ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="harga_bulanan">Harga bulanan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="1000"
                                   class="form-control <?= isset($errors['harga_bulanan']) ? 'is-invalid' : '' ?>"
                                   id="harga_bulanan" name="harga_bulanan"
                                   value="<?= esc(old('harga_bulanan', $kamar['harga_bulanan'] ?? '')) ?>" required>
                            <?php if (isset($errors['harga_bulanan'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['harga_bulanan']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="luas">Luas (contoh: 3x4)</label>
                            <input type="text" class="form-control" id="luas" name="luas"
                                   value="<?= esc(old('luas', $kamar['luas'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fasilitas">Fasilitas</label>
                        <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3"
                                  placeholder="Pisahkan dengan koma, contoh: AC, kasur, lemari, kamar mandi dalam"><?= esc(old('fasilitas', $kamar['fasilitas'] ?? '')) ?></textarea>
                        <small class="form-text text-muted">Pisahkan tiap fasilitas dengan koma.</small>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto kamar</label>
                        <div class="custom-file <?= isset($errors['foto']) ? 'is-invalid' : '' ?>">
                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih gambar (JPG/PNG/WEBP, maks 2 MB)</label>
                        </div>
                        <?php if (isset($errors['foto'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['foto']) ?></div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <img id="pratinjauFoto"
                                 src="<?= $edit && $kamar['foto'] !== null ? esc(base_url('uploads/kamar/' . $kamar['foto'])) : esc(base_url('assets/img/placeholder-kamar.svg')) ?>"
                                 alt="Pratinjau foto kamar"
                                 class="img-thumbnail" style="max-width: 260px; max-height: 180px; object-fit: cover;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> <?= $edit ? 'Simpan perubahan' : 'Simpan kamar' ?>
                    </button>
                    <a href="<?= site_url('admin/kamar') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Pratinjau foto sebelum form dikirim (FileReader).
    var inputFoto = document.getElementById('foto');
    if (inputFoto) {
        inputFoto.addEventListener('change', function (event) {
            var berkas = event.target.files[0];
            if (!berkas) {
                return;
            }

            var label = document.querySelector('.custom-file-label');
            if (label) {
                label.textContent = berkas.name;
            }

            var pembaca = new FileReader();
            pembaca.onload = function (e) {
                document.getElementById('pratinjauFoto').src = e.target.result;
            };
            pembaca.readAsDataURL(berkas);
        });
    }
</script>
<?= $this->endSection() ?>
