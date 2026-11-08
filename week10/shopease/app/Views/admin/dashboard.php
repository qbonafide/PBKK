<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Total penjualan</div>
            <div class="se-stat-value"><?= esc(rupiah($stats['sales'])) ?></div>
            <div class="small text-soft mt-1">Pesanan diproses, dikirim, dan selesai</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Total pesanan</div>
            <div class="se-stat-value"><?= esc((string) $stats['orders']) ?></div>
            <div class="small text-soft mt-1"><?= esc((string) $stats['pending_orders']) ?> belum selesai</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Customer</div>
            <div class="se-stat-value"><?= esc((string) $stats['customers']) ?></div>
            <div class="small text-soft mt-1">Akun dengan role customer</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Produk aktif</div>
            <div class="se-stat-value"><?= esc((string) $stats['active_products']) ?></div>
            <div class="small text-soft mt-1">dari <?= esc((string) $stats['products']) ?> produk terdaftar</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Pesanan pending</div>
            <div class="se-stat-value"><?= esc((string) $stats['pending_orders']) ?></div>
            <a class="small fw-semibold" href="<?= site_url('admin/orders?status=pending_payment') ?>">Lihat pesanan &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="se-stat-card">
            <div class="se-stat-label mb-1">Pembayaran menunggu</div>
            <div class="se-stat-value"><?= esc((string) $stats['waiting_payment']) ?></div>
            <a class="small fw-semibold" href="<?= site_url('admin/payments') ?>">Verifikasi sekarang &rarr;</a>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="se-stat-card">
            <div class="se-stat-label mb-2">Distribusi status pesanan</div>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($statusBreakdown as $status => $count) : ?>
                    <span class="se-chip">
                        <?= esc(order_status_label($status)) ?>: <strong><?= esc((string) $count) ?></strong>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="se-panel h-100">
            <div class="se-panel-header d-flex justify-content-between align-items-center">
                <span>Penjualan 14 hari terakhir</span>
                <span class="small text-soft">Total per hari (Rp)</span>
            </div>
            <div class="se-panel-body">
                <canvas id="grafikPenjualan" height="120" aria-label="Grafik penjualan harian" role="img"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="se-panel h-100">
            <div class="se-panel-header">Komposisi status</div>
            <div class="se-panel-body">
                <canvas id="grafikStatus" height="200" aria-label="Distribusi status pesanan" role="img"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-6">
        <div class="se-panel h-100">
            <div class="se-panel-header">Produk terlaris</div>
            <div class="table-responsive">
                <table class="table table-se align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bestSellers === []) : ?>
                            <tr><td colspan="2" class="text-soft">Belum ada penjualan.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($bestSellers as $item) : ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($item['name']) ?></td>
                                <td class="text-end"><?= esc((string) (int) $item['sold']) ?> unit</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="se-panel h-100">
            <div class="se-panel-header d-flex justify-content-between align-items-center">
                <span>Transaksi terbaru</span>
                <a class="small fw-semibold" href="<?= site_url('admin/orders') ?>">Semua pesanan</a>
            </div>
            <div class="table-responsive">
                <table class="table table-se align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentOrders === []) : ?>
                            <tr><td colspan="4" class="text-soft">Belum ada pesanan.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentOrders as $order) : ?>
                            <tr>
                                <td>
                                    <a class="fw-semibold text-reset" href="<?= site_url('admin/orders/' . (int) $order['id']) ?>">
                                        <?= esc($order['order_number']) ?>
                                    </a>
                                </td>
                                <td class="text-soft"><?= esc($order['customer_name']) ?></td>
                                <td class="text-end"><?= esc(rupiah($order['total'])) ?></td>
                                <td><?= order_status_badge($order['order_status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/chart.umd.min.js') ?>"></script>
<script>
    const labelHarian = <?= json_encode($salesSeries['labels'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const dataPenjualan = <?= json_encode($salesSeries['sales'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const labelStatus = <?= json_encode(array_map('order_status_label', array_keys($statusBreakdown)), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const dataStatus = <?= json_encode(array_values($statusBreakdown), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    new Chart(document.getElementById('grafikPenjualan'), {
        type: 'line',
        data: {
            labels: labelHarian,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: dataPenjualan,
                borderColor: '#2f4bd8',
                backgroundColor: 'rgba(47, 75, 216, 0.12)',
                fill: true,
                tension: 0.35,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + Number(value).toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('grafikStatus'), {
        type: 'doughnut',
        data: {
            labels: labelStatus,
            datasets: [{
                data: dataStatus,
                backgroundColor: ['#94a3b8', '#f0ad4e', '#38bdf8', '#2f4bd8', '#12805c', '#c0392b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
    });
</script>
<?= $this->endSection() ?>
