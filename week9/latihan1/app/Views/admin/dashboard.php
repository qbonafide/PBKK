<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dataGrafik = [];
for ($i = 1; $i <= 12; $i++) {
    $dataGrafik[] = (int) ($grafik[$i] ?? 0);
}
?>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Kamar terisi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= esc($kamarTerisi) ?> / <?= esc($totalKamar) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-closed fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Kamar kosong
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= esc($kamarKosong) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Penghuni aktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= esc($penghuniAktif) ?> orang
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tunggakan <?= esc($bulanNama) ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($tunggakanBulanIni, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    Pemasukan bulan ini: <strong>Rp <?= number_format($pemasukanBulanIni, 0, ',', '.') ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pemasukan lunas per bulan (<?= esc($tahun) ?>)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="grafikPemasukan"></canvas>
                </div>
                <p class="small text-muted mb-0 mt-3">
                    Angka diambil dari total tagihan berstatus <em>lunas</em> pada tabel pembayaran.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status kamar</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Terisi</span><strong><?= esc($kamarTerisi) ?></strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: <?= $totalKamar > 0 ? (int) ($kamarTerisi / $totalKamar * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Kosong</span><strong><?= esc($kamarKosong) ?></strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= $totalKamar > 0 ? (int) ($kamarKosong / $totalKamar * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Perbaikan</span><strong><?= esc($kamarPerbaikan) ?></strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?= $totalKamar > 0 ? (int) ($kamarPerbaikan / $totalKamar * 100) : 0 ?>%"></div>
                    </div>
                </div>

                <a href="<?= site_url('admin/kamar') ?>" class="btn btn-sm btn-outline-primary btn-block">
                    Kelola kamar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pembayaran terbaru</h6>
        <a href="<?= site_url('admin/pembayaran') ?>" class="btn btn-sm btn-primary">Lihat semua</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Penghuni</th>
                        <th>Kamar</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pembayaranTerbaru === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data pembayaran.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($pembayaranTerbaru as $b): ?>
                        <tr>
                            <td><?= esc($b['nama_penghuni'] ?? '-') ?></td>
                            <td><?= esc($b['nomor_kamar'] ?? '-') ?></td>
                            <td><?= esc($b['bulan']) ?>/<?= esc($b['tahun']) ?></td>
                            <td>Rp <?= number_format((int) $b['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge badge-<?= $b['status'] === 'lunas' ? 'success' : ($b['status'] === 'menunggu_verifikasi' ? 'warning' : ($b['status'] === 'ditolak' ? 'danger' : 'secondary')) ?>">
                                    <?= esc(str_replace('_', ' ', $b['status'])) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= esc($b['updated_at'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/sbadmin/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
    var ctx = document.getElementById('grafikPemasukan');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: <?= json_encode($dataGrafik) ?>,
                backgroundColor: '#1cc88a',
                hoverBackgroundColor: '#17a673',
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
