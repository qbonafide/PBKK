<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php if ($penghuni === null): ?>
    <div class="alert alert-warning">
        Akun Anda belum terhubung dengan data penghuni. Silakan hubungi admin kos untuk melengkapi data.
    </div>
<?php else: ?>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kamar saya</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">
                    Kamar <?= esc($kamar['nomor_kamar'] ?? '-') ?>
                </div>
                <div class="small text-muted mt-1">
                    Tipe <?= esc(ucfirst($kamar['tipe'] ?? '-')) ?> &middot; <?= esc($kamar['luas'] ?? '-') ?> m
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Harga sewa</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">
                    Rp <?= number_format((int) ($kamar['harga_bulanan'] ?? 0), 0, ',', '.') ?>
                </div>
                <div class="small text-muted mt-1">per bulan, sudah termasuk listrik &amp; air</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tagihan belum dibayar</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">
                    Rp <?= number_format($totalBelum, 0, ',', '.') ?>
                </div>
                <div class="small text-muted mt-1">Total tagihan berstatus belum bayar/ditolak</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tagihan terakhir</h6>
        <a href="<?= site_url('penghuni/tagihan') ?>" class="btn btn-sm btn-primary">Lihat semua tagihan</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tagihan === []): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada tagihan.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($tagihan as $t): ?>
                        <tr>
                            <td><?= esc($t['bulan']) ?>/<?= esc($t['tahun']) ?></td>
                            <td>Rp <?= number_format((int) $t['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge badge-<?= $t['status'] === 'lunas' ? 'success' : ($t['status'] === 'menunggu_verifikasi' ? 'warning' : ($t['status'] === 'ditolak' ? 'danger' : 'secondary')) ?>">
                                    <?= esc(ucfirst(str_replace('_', ' ', $t['status']))) ?>
                                </span>
                            </td>
                            <td><?= esc($t['tanggal_bayar'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data penghuni</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Nama</div>
                <div class="font-weight-bold"><?= esc($penghuni['nama']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Nomor HP</div>
                <div class="font-weight-bold"><?= esc($penghuni['no_hp'] ?? '-') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Tanggal masuk</div>
                <div class="font-weight-bold"><?= esc($penghuni['tanggal_masuk'] ?? '-') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Status</div>
                <div class="font-weight-bold"><?= esc(ucfirst($penghuni['status'])) ?></div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>
