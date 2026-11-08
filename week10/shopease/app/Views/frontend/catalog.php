<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0">Katalog</h1>
            <p class="text-soft mb-0 mt-1">
                <?= esc((string) (int) ($total ?? 0)) ?> produk tersedia
            </p>
        </div>
    </div>

    <form class="se-panel p-3 p-lg-4 mb-4" method="get" action="<?= site_url('catalog') ?>">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label for="q" class="form-label">Cari nama produk</label>
                <input type="search" class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>"
                       placeholder="mis. headphone, kaos, lampu">
            </div>
            <div class="col-lg-3 col-md-6">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Semua kategori</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (int) $filters['category'] === (int) $category['id'] ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="sort" class="form-label">Urutkan</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="">Terbaru</option>
                    <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Harga terendah</option>
                    <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Harga tertinggi</option>
                    <option value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'selected' : '' ?>>Nama A-Z</option>
                </select>
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-brand flex-grow-1">Terapkan</button>
                <a href="<?= site_url('catalog') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <?php if ($products === []) : ?>
        <div class="se-panel se-empty">
            <p class="fw-semibold mb-1">Produk tidak ditemukan</p>
            <p class="mb-3">Coba kata kunci lain atau hapus filter kategori.</p>
            <a href="<?= site_url('catalog') ?>" class="btn btn-outline-brand btn-sm">Lihat semua produk</a>
        </div>
    <?php else : ?>
        <div class="row g-4">
            <?php foreach ($products as $product) : ?>
                <div class="col-6 col-lg-3">
                    <?= view('partials/product_card', ['product' => $product]) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-center">
            <?= $pager->links('default', 'shopease') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
