<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<section class="py-4 border-bottom bg-white">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Katalog</h1>
        <p class="text-muted mb-0">Cari barang sesuai kategori, ukuran, dan kondisi.</p>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <form method="get" action="<?= site_url('katalog') ?>" class="row g-2 align-items-end mb-4">
            <div class="col-md-4">
                <label for="q" class="form-label small text-muted mb-1">Kata kunci</label>
                <input type="text" class="form-control" id="q" name="q"
                       value="<?= esc($filter['keyword']) ?>" placeholder="Nama barang atau brand">
            </div>
            <div class="col-md-2">
                <label for="kategori" class="form-label small text-muted mb-1">Kategori</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= esc($k['slug']) ?>"
                            <?= $filter['kategori'] === $k['slug'] ? 'selected' : '' ?>>
                            <?= esc($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="ukuran" class="form-label small text-muted mb-1">Ukuran</label>
                <select class="form-select" id="ukuran" name="ukuran">
                    <option value="">Semua</option>
                    <?php foreach ($ukuran as $u): ?>
                        <option value="<?= esc($u) ?>" <?= $filter['ukuran'] === $u ? 'selected' : '' ?>>
                            <?= esc($u) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="kondisi" class="form-label small text-muted mb-1">Kondisi</label>
                <select class="form-select" id="kondisi" name="kondisi">
                    <option value="">Semua</option>
                    <?php foreach (['like_new', 'bagus', 'ada_minus'] as $kondisi): ?>
                        <option value="<?= esc($kondisi) ?>" <?= $filter['kondisi'] === $kondisi ? 'selected' : '' ?>>
                            <?= esc(label_kondisi($kondisi)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="urut" class="form-label small text-muted mb-1">Urutkan</label>
                <select class="form-select" id="urut" name="urut">
                    <option value="terbaru" <?= $filter['urut'] === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="termurah" <?= $filter['urut'] === 'termurah' ? 'selected' : '' ?>>Termurah</option>
                    <option value="termahal" <?= $filter['urut'] === 'termahal' ? 'selected' : '' ?>>Termahal</option>
                </select>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-thrift px-4">Terapkan filter</button>
                <a href="<?= site_url('katalog') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="row g-4">
            <?php if ($produk === []): ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Tidak ada produk yang cocok. Coba kata kunci atau filter lain.
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($produk as $p): ?>
                <?= view('partials/kartu_produk', ['produk' => $p]) ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
