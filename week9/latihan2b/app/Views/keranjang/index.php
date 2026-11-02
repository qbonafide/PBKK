<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<section class="py-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Keranjang Belanja</h1>
        <p class="text-muted">Harga dan stok di halaman ini selalu dibaca ulang dari database.</p>

        <?php if ($keranjang['items'] === []): ?>
            <div class="alert alert-info">
                Keranjang masih kosong.
                <a href="<?= site_url('katalog') ?>" class="alert-link">Cari barang dulu</a>.
            </div>
        <?php else: ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form action="<?= site_url('keranjang/update') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="table-responsive">
                                    <table class="table align-middle mb-3">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th style="width: 120px;">Jumlah</th>
                                                <th>Subtotal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($keranjang['items'] as $item): ?>
                                                <?php $foto = $item['foto'] !== null && $item['foto'] !== ''
                                                    ? base_url('uploads/produk/' . $item['foto'])
                                                    : base_url('assets/img/placeholder-produk.svg'); ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="<?= esc($foto) ?>" alt="<?= esc($item['nama']) ?>"
                                                                 style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                                                            <div>
                                                                <a class="text-decoration-none text-dark fw-semibold"
                                                                   href="<?= site_url('produk/' . $item['slug']) ?>">
                                                                    <?= esc($item['nama']) ?>
                                                                </a>
                                                                <div class="small text-muted">Stok: <?= esc($item['stok']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= format_rupiah($item['harga']) ?></td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                               name="qty[<?= esc($item['produk_id']) ?>]"
                                                               value="<?= esc($item['qty']) ?>" min="0"
                                                               max="<?= esc($item['stok']) ?>">
                                                    </td>
                                                    <td class="fw-semibold"><?= format_rupiah($item['subtotal']) ?></td>
                                                    <td>
                                                        <button type="submit" form="hapus-<?= esc($item['produk_id']) ?>"
                                                                class="btn btn-sm btn-outline-danger">Hapus</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <button type="submit" class="btn btn-outline-thrift">
                                    Perbarui keranjang
                                </button>
                                <a href="<?= site_url('katalog') ?>" class="btn btn-link">Lanjut belanja</a>
                            </form>

                            <?php foreach ($keranjang['items'] as $item): ?>
                                <form id="hapus-<?= esc($item['produk_id']) ?>" action="<?= site_url('keranjang/hapus') ?>"
                                      method="post" class="d-none">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= esc($item['produk_id']) ?>">
                                </form>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-3">Ringkasan</h2>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal (<?= esc($keranjang['jumlah']) ?> barang)</span>
                                <span><?= format_rupiah($keranjang['subtotal']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ongkir</span>
                                <span>
                                    <?= $keranjang['ongkir'] === 0
                                        ? '<span class="text-success fw-semibold">Gratis</span>'
                                        : format_rupiah($keranjang['ongkir']) ?>
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold">Total</span>
                                <span class="h5 harga-thrift mb-0"><?= format_rupiah($keranjang['total']) ?></span>
                            </div>

                            <?php if ($keranjang['subtotal'] < 200000): ?>
                                <p class="small text-muted">
                                    Tambah <?= format_rupiah(200000 - $keranjang['subtotal']) ?> lagi untuk gratis ongkir.
                                </p>
                            <?php endif; ?>

                            <?php if (session()->get('user_id')): ?>
                                <a href="<?= site_url('checkout') ?>" class="btn btn-thrift w-100">Lanjut ke checkout</a>
                            <?php else: ?>
                                <a href="<?= site_url('checkout/masuk') ?>" class="btn btn-thrift w-100">
                                    Masuk untuk checkout
                                </a>
                                <p class="small text-muted mt-2 mb-0">
                                    Belum punya akun? Anda akan diarahkan ke halaman login,
                                    lalu kembali ke checkout.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
