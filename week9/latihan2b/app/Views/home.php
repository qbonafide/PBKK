<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<section class="hero-thrift">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark mb-3">Preloved pilihan &middot; stok terbatas</span>
                <h1 class="display-5 fw-bold mb-3">Barang bagus, harga masuk akal, tidak ada yang sama.</h1>
                <p class="lead mb-4">
                    ThriftIn menjual pakaian dan aksesoris preloved satu-satunya. Setiap barang
                    dicuci, diperiksa kondisinya, dan difoto apa adanya supaya Anda tahu persis
                    apa yang akan diterima.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= site_url('katalog') ?>" class="btn btn-warning fw-semibold px-4 text-dark">Mulai belanja</a>
                    <a href="<?= site_url('katalog?kondisi=like_new') ?>" class="btn btn-outline-light px-4">Lihat yang like new</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hero-stat">
                            <div class="fs-3 fw-bold"><?= esc(count($produk)) ?>+</div>
                            <div class="small">Barang siap kirim</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat">
                            <div class="fs-3 fw-bold"><?= esc(count($kategori)) ?></div>
                            <div class="small">Kategori</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="hero-stat">
                            <div class="small mb-1">Pengiriman</div>
                            <div class="fw-semibold">Flat Rp 20.000 &middot; gratis mulai Rp 200.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Baru masuk</h2>
                <p class="text-muted mb-0">Barang terbaru yang baru selesai difoto dan dicuci.</p>
            </div>
            <a href="<?= site_url('katalog') ?>" class="btn btn-sm btn-outline-thrift">Semua produk</a>
        </div>

        <div class="row g-4">
            <?php if ($produk === []): ?>
                <div class="col-12">
                    <div class="alert alert-warning mb-0">Belum ada produk aktif. Silakan cek kembali nanti.</div>
                </div>
            <?php endif; ?>

            <?php foreach ($produk as $p): ?>
                <?= view('partials/kartu_produk', ['produk' => $p]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="h4 fw-bold mb-4">Belanja per kategori</h2>
        <div class="row g-3">
            <?php foreach ($kategori as $k): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= site_url('katalog?kategori=' . $k['slug']) ?>"
                       class="d-block text-decoration-none text-center p-3 border rounded-3 h-100 bg-light">
                        <div class="fw-semibold text-dark small"><?= esc($k['nama']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Satu barang, satu pembeli</h3>
                <p class="text-muted mb-0">
                    Stok barang preloved umumnya hanya satu. Begitu ada yang checkout, stok
                    langsung ditahan untuk Anda.
                </p>
            </div>
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Bayar tanpa ribet</h3>
                <p class="text-muted mb-0">
                    Transfer, unggah bukti, lalu admin memverifikasi. Status pesanan bisa
                    dipantau kapan saja.
                </p>
            </div>
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Stok selalu jujur</h3>
                <p class="text-muted mb-0">
                    Pesanan yang tidak dibayar dalam 24 jam otomatis dibatalkan dan stoknya
                    dikembalikan untuk pembeli lain.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
