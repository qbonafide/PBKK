<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $penghuni !== null;
$action = $edit ? site_url('admin/penghuni/' . $penghuni['id']) : site_url('admin/penghuni');
?>

<div class="row">
    <div class="col-lg-9">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?= $edit ? 'Edit data penghuni' : 'Tambah penghuni baru' ?>
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
                        <div class="form-group col-md-6">
                            <label for="user_id">Akun login <span class="text-danger">*</span></label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- pilih akun penghuni --</option>
                                <?php foreach ($akun as $a): ?>
                                    <option value="<?= esc($a['id']) ?>"
                                        <?= (int) old('user_id', $penghuni['user_id'] ?? 0) === (int) $a['id'] ? 'selected' : '' ?>>
                                        <?= esc($a['nama']) ?> (@<?= esc($a['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">
                                Akun baru dibuat dari menu <a href="<?= site_url('admin/users/create') ?>">Pengguna</a>.
                            </small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="kamar_id">Kamar <span class="text-danger">*</span></label>
                            <select class="form-control" id="kamar_id" name="kamar_id" required>
                                <option value="">-- pilih kamar --</option>
                                <?php foreach ($kamar as $k): ?>
                                    <option value="<?= esc($k['id']) ?>"
                                        <?= (int) old('kamar_id', $penghuni['kamar_id'] ?? 0) === (int) $k['id'] ? 'selected' : '' ?>>
                                        Kamar <?= esc($k['nomor_kamar']) ?> (<?= esc(ucfirst($k['tipe'])) ?>) -
                                        Rp <?= number_format((int) $k['harga_bulanan'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama">Nama lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                                   id="nama" name="nama" value="<?= esc(old('nama', $penghuni['nama'] ?? '')) ?>" required>
                            <?php if (isset($errors['nama'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['nama']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="no_hp">Nomor HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp"
                                   value="<?= esc(old('no_hp', $penghuni['no_hp'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat_asal">Alamat asal</label>
                        <textarea class="form-control" id="alamat_asal" name="alamat_asal" rows="2"><?= esc(old('alamat_asal', $penghuni['alamat_asal'] ?? '')) ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="pekerjaan">Pekerjaan</label>
                            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan"
                                   value="<?= esc(old('pekerjaan', $penghuni['pekerjaan'] ?? '')) ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tanggal_masuk">Tanggal masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                                   value="<?= esc(old('tanggal_masuk', $penghuni['tanggal_masuk'] ?? date('Y-m-d'))) ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tanggal_keluar">Tanggal keluar</label>
                            <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar"
                                   value="<?= esc(old('tanggal_keluar', $penghuni['tanggal_keluar'] ?? '')) ?>">
                            <small class="form-text text-muted">Isi jika penghuni sudah keluar.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status">
                            <?php foreach (['aktif' => 'Aktif', 'keluar' => 'Keluar'] as $nilai => $label): ?>
                                <option value="<?= esc($nilai) ?>"
                                    <?= old('status', $penghuni['status'] ?? 'aktif') === $nilai ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Status aktif otomatis membuat kamar menjadi <em>terisi</em>; status keluar mengembalikan kamar menjadi <em>kosong</em>.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="foto_ktp">Foto KTP</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto_ktp" name="foto_ktp" accept="image/*">
                            <label class="custom-file-label" for="foto_ktp">Pilih gambar KTP (maks 2 MB)</label>
                        </div>
                        <?php if (isset($errors['foto_ktp'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['foto_ktp']) ?></div>
                        <?php endif; ?>
                        <small class="form-text text-muted">
                            Foto KTP disimpan di folder privat dan hanya bisa dibuka admin atau pemilik akun.
                        </small>

                        <div class="mt-3">
                            <img id="pratinjauKtp"
                                 src="<?= $edit && $penghuni['foto_ktp'] !== null ? site_url('berkas/ktp/' . $penghuni['foto_ktp']) : '' ?>"
                                 alt="Pratinjau KTP"
                                 class="img-thumbnail <?= ! $edit || $penghuni['foto_ktp'] === null ? 'd-none' : '' ?>"
                                 style="max-width: 260px; max-height: 170px; object-fit: cover;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> <?= $edit ? 'Simpan perubahan' : 'Simpan penghuni' ?>
                    </button>
                    <a href="<?= site_url('admin/penghuni') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    var inputKtp = document.getElementById('foto_ktp');
    if (inputKtp) {
        inputKtp.addEventListener('change', function (event) {
            var berkas = event.target.files[0];
            if (!berkas) {
                return;
            }

            var label = inputKtp.parentElement.querySelector('.custom-file-label');
            if (label) {
                label.textContent = berkas.name;
            }

            var pembaca = new FileReader();
            pembaca.onload = function (e) {
                var gambar = document.getElementById('pratinjauKtp');
                gambar.src = e.target.result;
                gambar.classList.remove('d-none');
            };
            pembaca.readAsDataURL(berkas);
        });
    }
</script>
<?= $this->endSection() ?>
