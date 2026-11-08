<?php
/**
 * Kartu produk yang dipakai ulang di homepage, katalog, dan hasil pencarian.
 * Variabel yang diharapkan: $product (array dari ProductModel / Query Builder).
 */
$stock = (int) ($product['stock'] ?? 0);
$isAvailable = $stock > 0;
?>
<div class="se-card">
    <a href="<?= site_url('product/' . (int) $product['id']) ?>">
        <img src="<?= esc(product_image($product['image'] ?? null)) ?>" class="se-thumb"
             alt="Foto produk <?= esc($product['name']) ?>" loading="lazy">
    </a>
    <div class="se-card-body d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <span class="se-chip"><?= esc($product['category_name'] ?? 'Tanpa kategori') ?></span>
            <?php if ($isAvailable) : ?>
                <span class="small text-soft">Stok <?= esc((string) $stock) ?></span>
            <?php else : ?>
                <span class="badge text-bg-danger">Habis</span>
            <?php endif; ?>
        </div>

        <h3 class="h6 mb-0">
            <a class="text-reset" href="<?= site_url('product/' . (int) $product['id']) ?>"><?= esc($product['name']) ?></a>
        </h3>

        <div class="se-price mt-auto"><?= esc(rupiah($product['price'])) ?></div>
    </div>
</div>
