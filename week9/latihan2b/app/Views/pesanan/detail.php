<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$urutan = ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'dikirim', 'selesai'];
$posisi = array_search($pesanan['status'], $urutan, true);
$terbatal = $pesanan['status'] === 'dibatalkan';
?>

<section class="py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-thrift">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('pesanan') ?>">Pesanan saya</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($pesanan['invoice']) ?></li>
            </ol>
        </nav>

        <?php if ($errors !== []): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    <?php foreach ($errors as $pesan): ?>
                        <li><?= esc($pesan) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h4 fw-bold mb-1"><?= esc($pesanan['invoice']) ?></h1>
                                <p class="text-muted small mb-0">Dibuat <?= esc($pesanan['created_at']) ?></p>
                            </div>
                            <span class="badge fs-6 <?= $terbatal ? 'bg-secondary' : 'bg-primary' ?>">
                                <?= esc($label[$pesanan['status']] ?? $pesanan['status']) ?>
                            </span>
                        </div>

                        <?php if (! $terbatal): ?>
                            <div class="stepper-status">
                                <?php foreach ($urutan as $i => $status): ?>
                                    <span class="<?= $posisi !== false && $i <= $posisi ? 'aktif' : '' ?>">
                                        <?= esc($label[$status]) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($pesanan['status'] === 'menunggu_pembayaran'): ?>
                            <div class="alert alert-warning small mb-0">
                                Selesaikan pembayaran sebelum <strong><?= esc($pesanan['expired_at']) ?></strong>.
                                Setelah waktu itu pesanan dibatalkan otomatis.
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($pesanan['no_resi'])): ?>
                            <div class="alert alert-info small mb-0 mt-3">
                                Nomor resi: <strong><?= esc($pesanan['no_resi']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h2 class="h6 fw-bold mb-0">Barang pesanan</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga saat pesan</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pesanan['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <?= esc($item['nama_produk']) ?>
                                                <?php if ($item['produk_id'] === null): ?>
                                                    <span class="badge bg-secondary ms-1">produk dihapus</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= format_rupiah((int) $item['harga']) ?></td>
                                            <td><?= esc($item['qty']) ?></td>
                                            <td><?= format_rupiah((int) $item['subtotal']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h6 fw-bold mb-0">Alamat pengiriman</h2>
                    </div>
                    <div class="card-body small">
                        <div class="fw-semibold"><?= esc($pesanan['nama_penerima']) ?></div>
                        <div class="text-muted"><?= esc($pesanan['no_hp']) ?></div>
                        <div class="mt-1"><?= nl2br(esc($pesanan['alamat_kirim'])) ?></div>
                        <?php if (! empty($pesanan['catatan'])): ?>
                            <div class="mt-2"><span class="text-muted">Catatan:</span> <?= esc($pesanan['catatan']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Ringkasan pembayaran</h2>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span><?= format_rupiah((int) $pesanan['subtotal']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Ongkir</span>
                            <span>
                                <?= (int) $pesanan['ongkir'] === 0
                                    ? '<span class="text-success fw-semibold">Gratis</span>'
                                    : format_rupiah((int) $pesanan['ongkir']) ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total</span>
                            <span class="h5 harga-thrift mb-0"><?= format_rupiah((int) $pesanan['total']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Bukti transfer</h2>

                        <?php if (! empty($pesanan['bukti_bayar'])): ?>
                            <a href="<?= site_url('berkas/bukti/' . $pesanan['bukti_bayar']) ?>" target="_blank"
                               class="btn btn-outline-thrift btn-sm w-100 mb-2">Lihat bukti yang diunggah</a>
                        <?php endif; ?>

                        <?php if ($pesanan['status'] === 'menunggu_pembayaran'): ?>
                            <form action="<?= site_url('pesanan/' . $pesanan['id'] . '/bukti') ?>" method="post"
                                  enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <label for="bukti_bayar" class="form-label small">
                                    Unggah bukti transfer (JPG/PNG/WEBP, maks 2 MB)
                                </label>
                                <input type="file" class="form-control form-control-sm mb-2"
                                       id="bukti_bayar" name="bukti_bayar" accept="image/*" required>
                                <button type="submit" class="btn btn-thrift btn-sm w-100">Kirim bukti</button>
                            </form>
                        <?php else: ?>
                            <p class="small text-muted mb-0">
                                Bukti transfer hanya bisa diunggah saat status masih menunggu pembayaran.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (in_array('dibatalkan', $berikutnya, true)): ?>
                    <form action="<?= site_url('pesanan/' . $pesanan['id'] . '/batal') ?>" method="post"
                          onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">Batalkan pesanan</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
