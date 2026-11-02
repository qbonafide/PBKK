<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dataGrafik = [];
for ($i = 1; $i <= 12; $i++) {
    $dataGrafik[] = (int) ($grafik[$i] ?? 0);
}
$labelStatus = [
    'menunggu_pembayaran' => ['teks' => 'Menunggu bayar', 'kelas' => 'warning'],
    'menunggu_verifikasi' => ['teks' => 'Menunggu verifikasi', 'kelas' => 'info'],
    'diproses'            => ['teks' => 'Diproses', 'kelas' => 'primary'],
    'dikirim'             => ['teks' => 'Dikirim', 'kelas' => 'primary'],
    'selesai'             => ['teks' => 'Selesai', 'kelas' => 'success'],
    'dibatalkan'          => ['teks' => 'Dibatalkan', 'kelas' => 'secondary'],
];
?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Omzet</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= format_rupiah($omzet) ?></div>
                <div class="small text-muted mt-1">pesanan yang sudah dibayar/diproses</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Produk aktif</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalProduk) ?></div>
                <div class="small text-muted mt-1"><?= esc($totalKategori) ?> kategori</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produk terjual</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($produkTerjual) ?> unit</div>
                <div class="small text-muted mt-1">tidak termasuk pesanan dibatalkan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perlu ditindak</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?= esc(($jumlahStatus['menunggu_verifikasi'] ?? 0) + ($jumlahStatus['diproses'] ?? 0)) ?> pesanan
                </div>
                <div class="small text-muted mt-1">
                    verifikasi <?= esc($jumlahStatus['menunggu_verifikasi'] ?? 0) ?> &middot;
                    diproses <?= esc($jumlahStatus['diproses'] ?? 0) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Omzet per bulan (<?= esc($tahun) ?>)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="grafikOmzet"></canvas>
                </div>
                <p class="small text-muted mb-0 mt-3">
                    Dihitung dari pesanan berstatus menunggu verifikasi sampai selesai.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pesanan per status</h6>
            </div>
            <div class="card-body">
                <?php foreach ($labelStatus as $status => $info): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge badge-<?= esc($info['kelas']) ?>"><?= esc($info['teks']) ?></span>
                        <strong><?= esc($jumlahStatus[$status] ?? 0) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pesanan terbaru</h6>
        <a href="<?= site_url('admin/pesanan') ?>" class="btn btn-sm btn-primary">Lihat semua</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pesananTerbaru === []): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada pesanan.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($pesananTerbaru as $p): ?>
                        <?php $info = $labelStatus[$p['status']] ?? ['teks' => $p['status'], 'kelas' => 'secondary']; ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('admin/pesanan/' . $p['id']) ?>" class="font-weight-bold">
                                    <?= esc($p['invoice']) ?>
                                </a>
                            </td>
                            <td><?= esc($p['nama_pembeli'] ?? '-') ?></td>
                            <td><?= format_rupiah((int) $p['total']) ?></td>
                            <td><span class="badge badge-<?= esc($info['kelas']) ?>"><?= esc($info['teks']) ?></span></td>
                            <td class="small text-muted"><?= esc($p['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Stok menipis (sisa &le; 1)</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <?php if ($stokRendah === []): ?>
                <div class="col-12 text-muted small">Semua produk stoknya masih aman.</div>
            <?php endif; ?>

            <?php foreach ($stokRendah as $s): ?>
                <div class="col-md-4 mb-2">
                    <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                        <span class="small"><?= esc($s['nama']) ?></span>
                        <span class="badge badge-<?= (int) $s['stok'] === 0 ? 'danger' : 'warning' ?>">
                            <?= esc($s['stok']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/sbadmin/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
    var ctx = document.getElementById('grafikOmzet');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Omzet (Rp)',
                data: <?= json_encode($dataGrafik) ?>,
                backgroundColor: '#7c3aed',
                hoverBackgroundColor: '#5b21b6',
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    gridLines: { color: 'rgba(0, 0, 0, 0.05)' }
                }],
                xAxes: [{ gridLines: { display: false } }]
            }
        }
    });
</script>
<?= $this->endSection() ?>
