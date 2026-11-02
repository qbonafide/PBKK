<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<section class="py-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Checkout</h1>
        <p class="text-muted">Lengkapi data pengiriman. Stok akan ditahan setelah pesanan dibuat.</p>

        <?php if ($errors !== []): ?>
            <div class="alert alert-danger">
                <strong>Periksa kembali isian berikut:</strong>
                <ul class="mb-0 mt-1 small">
                    <?php foreach ($errors as $pesan): ?>
                        <li><?= esc($pesan) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Data pengiriman</h2>

                        <form action="<?= site_url('checkout') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="nama_penerima" class="form-label">Nama penerima</label>
                                <input type="text" class="form-control" id="nama_penerima" name="nama_penerima"
                                       value="<?= esc(old('nama_penerima', $user['nama'] ?? '')) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                       value="<?= esc(old('no_hp', $user['no_hp'] ?? '')) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="alamat_kirim" class="form-label">Alamat pengiriman</label>
                                <textarea class="form-control" id="alamat_kirim" name="alamat_kirim" rows="3"
                                          required><?= esc(old('alamat_kirim', $user['alamat'] ?? '')) ?></textarea>
                                <div class="form-text">Sertakan kota dan kode pos agar pengiriman lancar.</div>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="2"
                                          placeholder="Contoh: titip di pos keamanan"><?= esc(old('catatan')) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-thrift px-4">
                                Buat pesanan
                            </button>
                            <a href="<?= site_url('keranjang') ?>" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Pesanan Anda</h2>

                        <ul class="list-unstyled mb-3">
                            <?php foreach ($keranjang['items'] as $item): ?>
                                <li class="d-flex justify-content-between small mb-2">
                                    <span>
                                        <?= esc($item['nama']) ?>
                                        <span class="text-muted">&times; <?= esc($item['qty']) ?></span>
                                    </span>
                                    <span><?= format_rupiah($item['subtotal']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <hr>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Subtotal</span>
                            <span><?= format_rupiah($keranjang['subtotal']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Ongkir</span>
                            <span>
                                <?= $keranjang['ongkir'] === 0
                                    ? '<span class="text-success fw-semibold">Gratis</span>'
                                    : format_rupiah($keranjang['ongkir']) ?>
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total bayar</span>
                            <span class="h5 harga-thrift mb-0"><?= format_rupiah($keranjang['total']) ?></span>
                        </div>

                        <p class="small text-muted mt-3 mb-0">
                            Setelah pesanan dibuat, unggah bukti transfer dalam 24 jam.
                            Lewat dari itu pesanan dibatalkan otomatis dan stok dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
