<?= $this->extend('layouts/shop') ?>

<?= $this->section('content') ?>

<?php
$labelStatus = [
    'menunggu_pembayaran' => ['teks' => 'Menunggu pembayaran', 'kelas' => 'bg-warning text-dark'],
    'menunggu_verifikasi' => ['teks' => 'Menunggu verifikasi', 'kelas' => 'bg-info text-dark'],
    'diproses'            => ['teks' => 'Diproses', 'kelas' => 'bg-primary'],
    'dikirim'             => ['teks' => 'Dikirim', 'kelas' => 'bg-primary'],
    'selesai'             => ['teks' => 'Selesai', 'kelas' => 'bg-success'],
    'dibatalkan'          => ['teks' => 'Dibatalkan', 'kelas' => 'bg-secondary'],
];
?>

<section class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Pesanan Saya</h1>
                <p class="text-muted mb-0">Semua pesanan yang pernah Anda buat.</p>
            </div>
            <a href="<?= site_url('katalog') ?>" class="btn btn-sm btn-outline-thrift">Belanja lagi</a>
        </div>

        <?php if ($pesanan === []): ?>
            <div class="alert alert-info">
                Belum ada pesanan.
                <a href="<?= site_url('katalog') ?>" class="alert-link">Lihat katalog</a>.
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Batas bayar</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pesanan as $p): ?>
                                    <?php $info = $labelStatus[$p['status']] ?? ['teks' => $p['status'], 'kelas' => 'bg-secondary']; ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($p['invoice']) ?></td>
                                        <td class="small text-muted"><?= esc($p['created_at']) ?></td>
                                        <td><?= format_rupiah((int) $p['total']) ?></td>
                                        <td><span class="badge <?= esc($info['kelas']) ?>"><?= esc($info['teks']) ?></span></td>
                                        <td class="small text-muted">
                                            <?= $p['status'] === 'menunggu_pembayaran' ? esc($p['expired_at']) : '-' ?>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('pesanan/' . $p['id']) ?>" class="btn btn-sm btn-outline-thrift">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
