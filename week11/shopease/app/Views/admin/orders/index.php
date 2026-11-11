<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<form class="se-panel p-3 mb-3" method="get" action="<?= site_url('admin/orders') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label">Cari nomor / nama customer</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>">
        </div>
        <div class="col-md-4">
            <label for="status" class="form-label">Status pesanan</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua status</option>
                <?php foreach (\App\Models\OrderModel::statuses() as $status) : ?>
                    <option value="<?= esc($status, 'attr') ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>>
                        <?= esc(order_status_label($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-brand flex-grow-1" type="submit">Terapkan filter</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/orders') ?>">Reset</a>
        </div>
    </div>
</form>

<div class="se-panel">
    <div class="table-responsive">
        <table class="table table-se align-middle mb-0">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th class="text-end">Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders === []) : ?>
                    <tr><td colspan="7" class="text-soft">Belum ada pesanan yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($order['order_number']) ?></td>
                        <td class="text-soft"><?= esc($order['customer_name']) ?></td>
                        <td class="text-soft small"><?= esc(date('d M Y H:i', strtotime($order['order_date']))) ?></td>
                        <td class="text-end"><?= esc(rupiah($order['total'])) ?></td>
                        <td><?= order_status_badge($order['order_status']) ?></td>
                        <td><?= payment_status_badge($order['payment_status'] ?? 'unpaid') ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-brand" href="<?= site_url('admin/orders/' . (int) $order['id']) ?>">Kelola</a>
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
<?= $this->endSection() ?>
