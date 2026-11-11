<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<form class="se-panel p-3 mb-3" method="get" action="<?= site_url('admin/reports') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="start" class="form-label">Tanggal awal</label>
            <input type="date" class="form-control" id="start" name="start" value="<?= esc($filters['start'], 'attr') ?>">
        </div>
        <div class="col-md-3">
            <label for="end" class="form-label">Tanggal akhir</label>
            <input type="date" class="form-control" id="end" name="end" value="<?= esc($filters['end'], 'attr') ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status pesanan</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua status</option>
                <?php foreach ($statuses as $item) : ?>
                    <option value="<?= esc($item, 'attr') ?>" <?= $filters['status'] === $item ? 'selected' : '' ?>>
                        <?= esc(order_status_label($item)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-brand flex-grow-1">Tampilkan</button>
            <a href="<?= site_url('admin/reports') ?>" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Omzet periode</div>
            <div class="se-stat-value"><?= esc(rupiah($summary['omzet'])) ?></div>
            <div class="small text-soft mt-1">Hanya pesanan diproses/dikirim/selesai</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Jumlah transaksi sah</div>
            <div class="se-stat-value"><?= esc((string) $summary['jumlah']) ?></div>
            <div class="small text-soft mt-1">Status diproses, dikirim, dan selesai</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Periode</div>
            <div class="se-stat-value" style="font-size:1.1rem;">
                <?= esc(date('d M Y', strtotime($filters['start']))) ?> &ndash;
                <?= esc(date('d M Y', strtotime($filters['end']))) ?>
            </div>
            <div class="small text-soft mt-1">Filter status: <?= esc($filters['status'] === '' ? 'semua' : order_status_label($filters['status'])) ?></div>
        </div>
    </div>
</div>

<div class="se-panel">
    <div class="table-responsive">
        <table class="table table-se align-middle mb-0">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders === []) : ?>
                    <tr><td colspan="6" class="text-soft">Tidak ada transaksi pada periode ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td>
                            <a class="fw-semibold text-reset" href="<?= site_url('admin/orders/' . (int) $order['id']) ?>">
                                <?= esc($order['order_number']) ?>
                            </a>
                        </td>
                        <td class="small text-soft"><?= esc(date('d M Y', strtotime($order['order_date']))) ?></td>
                        <td class="text-soft"><?= esc($order['customer_name']) ?></td>
                        <td class="text-end"><?= esc(rupiah($order['subtotal'])) ?></td>
                        <td class="text-end fw-semibold"><?= esc(rupiah($order['total'])) ?></td>
                        <td><?= order_status_badge($order['order_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    <?= $pager->links('default', 'shopease') ?>
</div>
<?= $this->endSection() ?>
