<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0">Riwayat pesanan</h1>
        </div>
        <a href="<?= site_url('catalog') ?>" class="btn btn-outline-brand btn-sm">Belanja lagi</a>
    </div>

    <?php if ($orders === []) : ?>
        <div class="se-panel se-empty">
            <p class="fw-semibold mb-1">Belum ada pesanan</p>
            <p class="mb-3">Pesanan yang Anda buat akan muncul di halaman ini.</p>
            <a href="<?= site_url('catalog') ?>" class="btn btn-brand btn-sm">Mulai belanja</a>
        </div>
    <?php else : ?>
        <div class="se-panel">
            <div class="table-responsive">
                <table class="table table-se align-middle">
                    <thead>
                        <tr>
                            <th>Nomor pesanan</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th>Status pesanan</th>
                            <th>Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($order['order_number']) ?></td>
                                <td class="text-soft"><?= esc(date('d M Y H:i', strtotime($order['order_date']))) ?></td>
                                <td class="text-end fw-semibold"><?= esc(rupiah($order['total'])) ?></td>
                                <td><?= order_status_badge($order['order_status']) ?></td>
                                <td><?= payment_status_badge($order['payment_status'] ?? 'unpaid') ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-brand" href="<?= site_url('orders/' . (int) $order['id']) ?>">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            <?= $pager->links('default', 'shopease') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
