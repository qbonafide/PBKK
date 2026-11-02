<?php
/**
 * Partial kartu kamar, dipakai di beranda dan halaman daftar kamar
 * supaya markup kartu hanya ditulis satu kali.
 *
 * @var array $kamar
 */
$labelStatus = [
    'kosong'    => ['text' => 'Kosong', 'kelas' => 'badge-kosong'],
    'terisi'    => ['text' => 'Terisi', 'kelas' => 'badge-terisi'],
    'perbaikan' => ['text' => 'Perbaikan', 'kelas' => 'badge-perbaikan'],
];
$status = $labelStatus[$kamar['status']] ?? ['text' => $kamar['status'], 'kelas' => 'bg-secondary'];
$foto   = $kamar['foto'] !== null && $kamar['foto'] !== ''
    ? base_url('uploads/kamar/' . $kamar['foto'])
    : base_url('assets/img/placeholder-kamar.svg');
$fasilitas = array_slice(array_map('trim', explode(',', (string) $kamar['fasilitas'])), 0, 3);
?>
<div class="col-md-4">
    <div class="card kartu-kamar">
        <img src="<?= esc($foto) ?>" class="card-img-top" alt="Foto kamar <?= esc($kamar['nomor_kamar']) ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title mb-0">Kamar <?= esc($kamar['nomor_kamar']) ?></h5>
                <span class="badge <?= esc($status['kelas']) ?>"><?= esc($status['text']) ?></span>
            </div>
            <p class="text-muted small mb-2">
                Tipe <?= esc(ucfirst($kamar['tipe'])) ?> &middot; <?= esc($kamar['luas'] ?? '-') ?> m
            </p>
            <p class="harga mb-2">Rp <?= number_format((int) $kamar['harga_bulanan'], 0, ',', '.') ?>
                <span class="text-muted fw-normal small">/ bulan</span>
            </p>
            <ul class="list-unstyled small text-muted mb-3">
                <?php foreach ($fasilitas as $f): ?>
                    <li>&bull; <?= esc($f) ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="<?= site_url('kamar/' . $kamar['slug']) ?>" class="btn btn-outline-success btn-sm w-100">
                Lihat detail
            </a>
        </div>
    </div>
</div>
