<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-soft mb-0">Kategori yang masih memiliki produk tidak dapat dihapus.</p>
    <a href="<?= site_url('admin/categories/create') ?>" class="btn btn-brand btn-sm">+ Tambah kategori</a>
</div>

<div class="se-panel">
    <div class="table-responsive">
        <table class="table table-se align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories === []) : ?>
                    <tr><td colspan="6" class="text-soft">Belum ada kategori.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $index => $category) : ?>
                    <tr>
                        <td class="text-soft"><?= esc((string) ($index + 1)) ?></td>
                        <td>
                            <div class="fw-semibold"><?= esc($category['name']) ?></div>
                            <div class="small text-soft"><?= esc(mb_strimwidth((string) ($category['description'] ?? ''), 0, 70, '...')) ?></div>
                        </td>
                        <td class="text-soft small"><?= esc($category['slug']) ?></td>
                        <td class="text-center"><?= esc((string) (int) $category['product_count']) ?></td>
                        <td class="text-center">
                            <?php if ((int) $category['is_active'] === 1) : ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else : ?>
                                <span class="badge text-bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/categories/' . (int) $category['id'] . '/edit') ?>">Ubah</a>
                                <form method="post" action="<?= site_url('admin/categories/' . (int) $category['id'] . '/delete') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus kategori ini?')">Hapus</button>
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
