<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $isEdit = $product !== null; ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="se-panel">
            <div class="se-panel-header"><?= $isEdit ? 'Ubah produk' : 'Produk baru' ?></div>
            <div class="se-panel-body">
                <form method="post" action="<?= esc($action, 'attr') ?>" enctype="multipart/form-data" class="row g-3">
                    <?= csrf_field() ?>

                    <div class="col-md-8">
                        <label for="name" class="form-label">Nama produk</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= esc(old('name', $product['name'] ?? '')) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= (int) $category['id'] ?>"
                                    <?= (string) old('category_id', (string) ($product['category_id'] ?? '')) === (string) $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="price" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="100" required
                               value="<?= esc(old('price', (string) ($product['price'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" required
                               value="<?= esc(old('stock', (string) ($product['stock'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= old('status', $product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= old('status', $product['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= esc(old('description', $product['description'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12">
                        <label for="image" class="form-label">Gambar produk</label>
                        <input type="file" class="form-control" id="image" name="image"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">JPG, PNG, atau WEBP. Maksimal 2 MB. Nama berkas dibuat acak oleh sistem.</div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-brand"><?= $isEdit ? 'Simpan perubahan' : 'Simpan produk' ?></button>
                        <a href="<?= site_url('admin/products') ?>" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="se-panel">
            <div class="se-panel-header">Pratinjau gambar</div>
            <div class="se-panel-body">
                <img src="<?= esc(product_image($product['image'] ?? null)) ?>" class="w-100 rounded-3"
                     style="aspect-ratio: 4/3; object-fit: cover;" alt="Pratinjau gambar produk">
                <?php if ($isEdit && ! empty($product['image'])) : ?>
                    <p class="small text-soft mt-2 mb-0">Berkas saat ini: <?= esc($product['image']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="se-panel mt-3">
            <div class="se-panel-body small text-soft">
                Produk yang sudah pernah dipesan tidak dihapus permanen. Tindakan hapus akan
                mengubah statusnya menjadi nonaktif agar riwayat transaksi tetap utuh.
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
