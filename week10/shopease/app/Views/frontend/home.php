<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <section class="se-hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1 class="mb-3">Belanja kebutuhan apapun,<br>tersedia di ShopEase</h1>
                <p class="mb-4 text-white-50" style="max-width: 46ch;">
                    Elektronik, pakaian, aksesoris, dan perlengkapan rumah.
                    Pilih produknya, masukkan keranjang, lalu checkout.
                </p>
                <a href="<?= site_url('catalog') ?>" class="btn btn-light px-4">Mulai belanja</a>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
            <h2 class="h4 mb-0">Kategori</h2>
            <a href="<?= site_url('catalog') ?>" class="small fw-semibold">Lihat semua &rarr;</a>
        </div>

        <div class="row g-3">
            <?php foreach ($categories as $category) : ?>
                <div class="col-6 col-lg-3">
                    <a class="se-card d-block p-4 text-reset" href="<?= site_url('catalog?category=' . (int) $category['id']) ?>">
                        <div class="se-price mb-1"><?= esc($category['name']) ?></div>
                        <div class="small text-soft"><?= esc((string) (int) $category['product_count']) ?> produk</div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
            <h2 class="h4 mb-0">Produk terbaru</h2>
            <a href="<?= site_url('catalog?sort=price_asc') ?>" class="small fw-semibold">Termurah &rarr;</a>
        </div>

        <div class="row g-4">
            <?php foreach ($latest as $product) : ?>
                <div class="col-6 col-lg-3">
                    <?= view('partials/product_card', ['product' => $product]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($bestSellers !== []) : ?>
        <section>
            <h2 class="h4 mb-3">Produk terlaris</h2>

            <div class="se-panel">
                <div class="table-responsive">
                    <table class="table table-se align-middle">
                        <thead>
                            <tr>
                                <th style="width:70px;">#</th>
                                <th>Produk</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bestSellers as $index => $item) : ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc((string) ($index + 1)) ?></td>
                                    <td>
                                        <a class="text-reset fw-semibold" href="<?= site_url('product/' . (int) $item['id']) ?>">
                                            <?= esc($item['name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-end"><?= esc(rupiah($item['price'])) ?></td>
                                    <td class="text-end"><?= esc((string) (int) $item['sold']) ?> unit</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
