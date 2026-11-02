<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<?php
$foto = $produk['foto'] !== null && $produk['foto'] !== ''
    ? base_url('uploads/produk/' . $produk['foto'])
    : base_url('assets/img/placeholder-produk.svg');
?>

<section class="py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-thrift">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('katalog') ?>">Katalog</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($produk['nama']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="foto-detail">
                    <img src="<?= esc($foto) ?>" alt="Foto <?= esc($produk['nama']) ?>">
                </div>
                <p class="small text-muted mt-2 mb-0">
                    Foto diambil apa adanya. Barang preloved bisa memiliki tanda pemakaian ringan
                    sesuai keterangan kondisi.
                </p>
            </div>

            <div class="col-lg-6">
                <span class="badge badge-kondisi mb-2"><?= esc(label_kondisi((string) $produk['kondisi'])) ?></span>
                <h1 class="h3 fw-bold mb-2"><?= esc($produk['nama']) ?></h1>
                <p class="text-muted mb-3">
                    <?= esc($produk['brand'] ?? '-') ?>
                    <?php if (! empty($produk['ukuran'])): ?>
                        &middot; Ukuran <?= esc($produk['ukuran']) ?>
                    <?php endif; ?>
                    <?php if (! empty($produk['nama_kategori'])): ?>
                        &middot; <?= esc($produk['nama_kategori']) ?>
                    <?php endif; ?>
                </p>

                <p class="h3 harga-thrift mb-3"><?= format_rupiah((int) $produk['harga']) ?></p>

                <p class="mb-3">
                    <?php if ((int) $produk['stok'] > 0): ?>
                        <span class="badge bg-success">Tersedia <?= esc($produk['stok']) ?> unit</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Stok habis</span>
                    <?php endif; ?>
                </p>

                <?php if ((int) $produk['stok'] > 0): ?>
                    <form action="<?= site_url('keranjang') ?>" method="post" class="row g-2 align-items-end mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="produk_id" value="<?= esc($produk['id']) ?>">

                        <div class="col-4 col-md-3">
                            <label for="qty" class="form-label small text-muted mb-1">Jumlah</label>
                            <input type="number" class="form-control" id="qty" name="qty" value="1"
                                   min="1" max="<?= esc($produk['stok']) ?>">
                        </div>
                        <div class="col-8 col-md-6">
                            <button type="submit" class="btn btn-thrift w-100">
                                Tambah ke keranjang
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 mb-3" disabled>Stok habis</button>
                <?php endif; ?>

                <?php if ($produk['deskripsi'] !== null && $produk['deskripsi'] !== ''): ?>
                    <div class="card border-0 bg-white shadow-sm mb-3">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-2">Deskripsi</h2>
                            <p class="mb-0 small text-muted"><?= nl2br(esc($produk['deskripsi'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="ringkasan-pesanan">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ongkir</span>
                            <span><?= format_rupiah(ongkir_untuk((int) $produk['harga'])) ?></span>
                        </div>
                        <div class="text-muted">
                            Gratis ongkir untuk belanja mulai Rp 200.000.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($serupa !== []): ?>
            <div class="mt-5">
                <h2 class="h5 fw-bold mb-3">Barang lain di kategori ini</h2>
                <div class="row g-4">
                    <?php foreach ($serupa as $s): ?>
                        <?= view('partials/kartu_produk', ['produk' => $s]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
