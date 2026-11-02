<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $produk !== null;
$action = $edit ? site_url('admin/produk/' . $produk['id']) : site_url('admin/produk');
?>

<div class="row">
    <div class="col-lg-9">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $edit ? 'Edit produk' : 'Tambah produk' ?></h6>
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

                <form action="<?= $action ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="nama">Nama produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   value="<?= esc(old('nama', $produk['nama'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="kategori_id">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                <option value="">-- pilih kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= esc($k['id']) ?>"
                                        <?= (int) old('kategori_id', $produk['kategori_id'] ?? 0) === (int) $k['id'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand"
                                   value="<?= esc(old('brand', $produk['brand'] ?? '')) ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="ukuran">Ukuran</label>
                            <input type="text" class="form-control" id="ukuran" name="ukuran"
                                   value="<?= esc(old('ukuran', $produk['ukuran'] ?? '')) ?>" placeholder="M / L / 42">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="kondisi">Kondisi <span class="text-danger">*</span></label>
                            <select class="form-control" id="kondisi" name="kondisi">
                                <?php foreach (['like_new' => 'Like new', 'bagus' => 'Bagus', 'ada_minus' => 'Ada minus'] as $nilai => $label): ?>
                                    <option value="<?= esc($nilai) ?>"
                                        <?= old('kondisi', $produk['kondisi'] ?? 'bagus') === $nilai ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="harga">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga" name="harga" min="1" step="1000"
                                   value="<?= esc(old('harga', $produk['harga'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="stok">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok" name="stok" min="0"
                                   value="<?= esc(old('stok', $produk['stok'] ?? 1)) ?>" required>
                            <small class="form-text text-muted">Barang thrift umumnya stok 1.</small>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="aktif">Status</label>
                            <select class="form-control" id="aktif" name="aktif">
                                <option value="1" <?= (string) old('aktif', $produk['aktif'] ?? 1) === '1' ? 'selected' : '' ?>>
                                    Aktif (tampil di katalog)
                                </option>
                                <option value="0" <?= (string) old('aktif', $produk['aktif'] ?? 1) === '0' ? 'selected' : '' ?>>
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Tuliskan kondisi barang, minus (jika ada), dan ukuran detail."><?= esc(old('deskripsi', $produk['deskripsi'] ?? '')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto produk</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih gambar (maks 2 MB)</label>
                        </div>
                        <?php if (isset($errors['foto'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['foto']) ?></div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <img id="pratinjauFoto"
                                 src="<?= $edit && $produk['foto'] !== null
                                     ? esc(base_url('uploads/produk/' . $produk['foto']))
                                     : esc(base_url('assets/img/placeholder-produk.svg')) ?>"
                                 alt="Pratinjau foto produk"
                                 class="img-thumbnail" style="max-width: 240px; max-height: 240px; object-fit: cover;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan
                    </button>
                    <a href="<?= site_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Pratinjau foto sebelum upload (FileReader).
    var inputFoto = document.getElementById('foto');
    if (inputFoto) {
        inputFoto.addEventListener('change', function (event) {
            var berkas = event.target.files[0];
            if (!berkas) {
                return;
            }

            var label = inputFoto.parentElement.querySelector('.custom-file-label');
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
