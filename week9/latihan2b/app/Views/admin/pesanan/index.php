<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$kelasStatus = [
    'menunggu_pembayaran' => 'warning',
    'menunggu_verifikasi' => 'info',
    'diproses'            => 'primary',
    'dikirim'             => 'primary',
    'selesai'             => 'success',
    'dibatalkan'          => 'secondary',
];
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar pesanan</h6>
        <a href="<?= site_url('admin/produk') ?>" class="btn btn-sm btn-outline-primary">Kelola produk</a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/pesanan') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari invoice atau nama pembeli" value="<?= esc($keyword) ?>">
            <select name="status" class="form-control form-control-sm mr-2 mb-2">
                <option value="">Semua status</option>
                <?php foreach ($label as $nilai => $teks): ?>
                    <option value="<?= esc($nilai) ?>" <?= $status === $nilai ? 'selected' : '' ?>>
                        <?= esc($teks) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Filter</button>
            <?php if ($keyword !== '' || $status !== ''): ?>
                <a href="<?= site_url('admin/pesanan') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Pembeli</th>
                        <th>Penerima</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pesanan === []): ?>
                        <tr><td colspan="8" class="text-center text-muted">Tidak ada pesanan yang cocok.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($pesanan as $p): ?>
                        <tr>
                            <td class="font-weight-bold"><?= esc($p['invoice']) ?></td>
                            <td><?= esc($p['nama_pembeli'] ?? '-') ?></td>
                            <td><?= esc($p['nama_penerima']) ?></td>
                            <td><?= format_rupiah((int) $p['total']) ?></td>
                            <td>
                                <span class="badge badge-<?= esc($kelasStatus[$p['status']] ?? 'secondary') ?>">
                                    <?= esc($label[$p['status']] ?? $p['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (! empty($p['bukti_bayar'])): ?>
                                    <a href="<?= site_url('berkas/bukti/' . $p['bukti_bayar']) ?>" target="_blank"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= esc($p['created_at']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/pesanan/' . $p['id']) ?>" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>
