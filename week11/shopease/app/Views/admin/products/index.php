<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <form class="d-flex gap-2" method="get" action="<?= site_url('admin/products') ?>">
        <label for="q" class="visually-hidden">Cari produk</label>
        <input type="search" class="form-control" id="q" name="q" value="<?= esc($keyword) ?>" placeholder="Cari nama produk...">
        <button class="btn btn-outline-secondary" type="submit">Cari</button>
    </form>
    <a href="<?= site_url('admin/products/create') ?>" class="btn btn-brand btn-sm">+ Tambah produk</a>
</div>

<div class="se-panel">
    <div class="table-responsive">
        <table class="table table-se align-middle mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products === []) : ?>
                    <tr><td colspan="6" class="text-soft">Tidak ada produk yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= esc(product_image($product['image'])) ?>" class="se-thumb-sm"
                                     alt="Foto <?= esc($product['name']) ?>">
                                <div>
                                    <div class="fw-semibold"><?= esc($product['name']) ?></div>
                                    <div class="small text-soft"><?= esc($product['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-soft"><?= esc($product['category_name'] ?? '-') ?></td>
                        <td class="text-end"><?= esc(rupiah($product['price'])) ?></td>
                        <td class="text-center"><?= esc((string) (int) $product['stock']) ?></td>
                        <td class="text-center">
                            <?php if ($product['status'] === 'active') : ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else : ?>
                                <span class="badge text-bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/products/' . (int) $product['id'] . '/edit') ?>">Ubah</a>
                                <form method="post" action="<?= site_url('admin/products/' . (int) $product['id'] . '/delete') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus produk ini? Produk yang pernah dipesan hanya akan dinonaktifkan.')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    <?= $pager->links('default', 'shopease') ?>
</div>
<?= $this->endSection() ?>
