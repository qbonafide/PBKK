<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
$labelStatus = [
    'kosong'    => ['text' => 'Kosong', 'kelas' => 'badge-kosong'],
    'terisi'    => ['text' => 'Terisi', 'kelas' => 'badge-terisi'],
    'perbaikan' => ['text' => 'Perbaikan', 'kelas' => 'badge-perbaikan'],
];
$status    = $labelStatus[$kamar['status']] ?? ['text' => $kamar['status'], 'kelas' => 'bg-secondary'];
$foto      = $kamar['foto'] !== null && $kamar['foto'] !== ''
    ? base_url('uploads/kamar/' . $kamar['foto'])
    : base_url('assets/img/placeholder-kamar.svg');
$fasilitas = array_filter(array_map('trim', explode(',', (string) $kamar['fasilitas'])));
?>

<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('kamar') ?>">Daftar Kamar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kamar <?= esc($kamar['nomor_kamar']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="detail-foto">
                    <img src="<?= esc($foto) ?>" alt="Foto kamar <?= esc($kamar['nomor_kamar']) ?>">
                </div>
            </div>

            <div class="col-lg-5">
                <div class="d-flex justify-content-between align-items-start">
                    <h1 class="bagian-judul h2 mb-1">Kamar <?= esc($kamar['nomor_kamar']) ?></h1>
                    <span class="badge <?= esc($status['kelas']) ?>"><?= esc($status['text']) ?></span>
                </div>
                <p class="text-muted">Tipe <?= esc(ucfirst($kamar['tipe'])) ?> &middot; Luas <?= esc($kamar['luas'] ?? '-') ?> m</p>

                <p class="h3 harga mb-3">
                    Rp <?= number_format((int) $kamar['harga_bulanan'], 0, ',', '.') ?>
                    <span class="text-muted fw-normal fs-6">/ bulan</span>
                </p>

                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">Fasilitas kamar</h2>
                <ul class="list-group list-group-flush mb-4">
                    <?php foreach ($fasilitas as $f): ?>
                        <li class="list-group-item px-0">
                            <span class="text-success me-2">&#10003;</span><?= esc($f) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($kamar['status'] === 'kosong'): ?>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-success w-100 mb-2">Tanya &amp; jadwalkan survei</a>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 mb-2" disabled>Kamar sedang tidak tersedia</button>
                <?php endif; ?>
                <a href="<?= site_url('kamar') ?>" class="btn btn-outline-secondary w-100">Kembali ke daftar kamar</a>

                <div class="alert alert-light border mt-4 mb-0 small">
                    Harga sudah termasuk listrik, air, dan WiFi. Pembayaran dilakukan setiap bulan
                    melalui dashboard penghuni.
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
