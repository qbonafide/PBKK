<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $isEdit = $category !== null; ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="se-panel">
            <div class="se-panel-header"><?= $isEdit ? 'Ubah kategori' : 'Kategori baru' ?></div>
            <div class="se-panel-body">
                <form method="post" action="<?= esc($action, 'attr') ?>" class="row g-3">
                    <?= csrf_field() ?>

                    <div class="col-12">
                        <label for="name" class="form-label">Nama kategori</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= esc(old('name', $category['name'] ?? '')) ?>">
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= esc(old('description', $category['description'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" <?= (string) old('is_active', (string) ($category['is_active'] ?? '1')) === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= (string) old('is_active', (string) ($category['is_active'] ?? '1')) === '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                        <div class="form-text">Kategori nonaktif tidak tampil di beranda customer.</div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-brand"><?= $isEdit ? 'Simpan perubahan' : 'Simpan kategori' ?></button>
                        <a href="<?= site_url('admin/categories') ?>" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="se-panel h-100">
            <div class="se-panel-header">Catatan</div>
            <div class="se-panel-body small text-soft">
                <p>Slug kategori dibuat otomatis dari nama, sehingga tidak perlu diisi manual.</p>
                <p class="mb-0">Penghapusan kategori akan ditolak selama masih ada produk yang memakainya.
                    Pindahkan atau hapus produk terlebih dahulu.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
