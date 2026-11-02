<?php
/**
 * Partial kartu produk, dipakai di beranda, katalog, dan halaman detail.
 *
 * @var array $produk
 */
$foto = $produk['foto'] !== null && $produk['foto'] !== ''
    ? base_url('uploads/produk/' . $produk['foto'])
    : base_url('assets/img/placeholder-produk.svg');
?>
<div class="col-sm-6 col-lg-4">
    <div class="card kartu-produk">
        <a href="<?= site_url('produk/' . $produk['slug']) ?>">
            <img src="<?= esc($foto) ?>" alt="Foto <?= esc($produk['nama']) ?>">
        </a>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="badge badge-kondisi"><?= esc(label_kondisi((string) $produk['kondisi'])) ?></span>
                <?php if ((int) $produk['stok'] < 1): ?>
                    <span class="badge bg-secondary">Habis</span>
                <?php else: ?>
                    <span class="badge bg-success">Stok <?= esc($produk['stok']) ?></span>
                <?php endif; ?>
            </div>

            <h5 class="card-title h6 fw-bold mb-1">
                <a class="text-decoration-none text-dark" href="<?= site_url('produk/' . $produk['slug']) ?>">
                    <?= esc($produk['nama']) ?>
                </a>
            </h5>

            <p class="text-muted small mb-2">
                <?= esc($produk['brand'] ?? '-') ?>
                <?php if (! empty($produk['ukuran'])): ?>
                    &middot; Ukuran <?= esc($produk['ukuran']) ?>
                <?php endif; ?>
                <?php if (! empty($produk['nama_kategori'])): ?>
                    &middot; <?= esc($produk['nama_kategori']) ?>
                <?php endif; ?>
            </p>

            <div class="d-flex justify-content-between align-items-center">
                <span class="harga-thrift"><?= format_rupiah((int) $produk['harga']) ?></span>
                <a href="<?= site_url('produk/' . $produk['slug']) ?>" class="btn btn-sm btn-outline-thrift">
                    Lihat
                </a>
            </div>
        </div>
    </div>
</div>
