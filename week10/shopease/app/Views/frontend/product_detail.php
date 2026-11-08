<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $stock = (int) $product['stock']; ?>
<div class="container section-pad">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('catalog') ?>">Katalog</a></li>
            <li class="breadcrumb-item">
                <a href="<?= site_url('catalog?category=' . (int) $product['category_id']) ?>"><?= esc($product['category_name']) ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="se-panel overflow-hidden">
                <img src="<?= esc(product_image($product['image'])) ?>" class="w-100"
                     style="aspect-ratio: 4/3; object-fit: cover;" alt="Foto produk <?= esc($product['name']) ?>">
            </div>
        </div>

        <div class="col-lg-6">
            <span class="se-chip mb-3"><?= esc($product['category_name']) ?></span>
            <h1 class="h2 mb-2"><?= esc($product['name']) ?></h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="se-price fs-4"><?= esc(rupiah($product['price'])) ?></div>
                <?php if ($stock > 0) : ?>
                    <span class="badge text-bg-success">Stok <?= esc((string) $stock) ?></span>
                <?php else : ?>
                    <span class="badge text-bg-danger">Stok habis</span>
                <?php endif; ?>
            </div>

            <p class="text-soft"><?= esc($product['description'] ?? 'Belum ada deskripsi untuk produk ini.') ?></p>

            <div class="se-divider"></div>

            <?php if ($stock > 0) : ?>
                <?php if (current_user() === null) : ?>
                    <div class="alert alert-info mb-3">
                        Masuk terlebih dahulu untuk menambahkan produk ke keranjang.
                    </div>
                    <a href="<?= site_url('login') ?>" class="btn btn-brand px-4">Masuk untuk membeli</a>
                    <a href="<?= site_url('register') ?>" class="btn btn-outline-brand px-4">Daftar akun</a>
                <?php elseif (has_role('customer')) : ?>
                    <form method="post" action="<?= site_url('cart/add') ?>" class="row g-3 align-items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <div class="col-4 col-sm-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1"
                                   min="1" max="<?= esc((string) $stock) ?>" required>
                        </div>
                        <div class="col-8 col-sm-9">
                            <button type="submit" class="btn btn-brand w-100">Tambah ke keranjang</button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="alert alert-secondary mb-0">
                        Anda masuk sebagai <?= esc(current_user()['role']) ?>. Keranjang hanya tersedia untuk akun customer.
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-secondary" disabled>Stok habis</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($related !== []) : ?>
        <section class="mt-5">
            <div class="mb-3">
                <p class="se-muted-label mb-1">Kategori sama</p>
                <h2 class="h5 mb-0">Produk lain yang mungkin cocok</h2>
            </div>
            <div class="row g-4">
                <?php foreach ($related as $item) : ?>
                    <div class="col-6 col-lg-3">
                        <?= view('partials/product_card', ['product' => $item]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
