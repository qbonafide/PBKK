<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-3">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="font-weight-bold text-primary mb-2">Kartu Hasil Studi</h6>
                <p class="small text-muted mb-0">
                    <?= esc($mhs['nama']) ?> &middot; <?= esc($mhs['nim']) ?><br>
                    IPK kumulatif: <strong><?= number_format((float) $ipk['ipk'], 2) ?></strong>
                    dari <?= esc($ipk['sks']) ?> SKS yang sudah dinilai
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body d-flex align-items-center">
                <a href="<?= site_url('mahasiswa/khs/cetak') ?>" class="btn btn-outline-primary btn-sm btn-block" target="_blank">
                    <i class="fas fa-print fa-sm mr-1"></i> Cetak KHS
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($rekap === []): ?>
    <div class="alert alert-info">Belum ada data nilai. Silakan isi KRS terlebih dahulu.</div>
<?php endif; ?>

<?php foreach ($rekap as $semester): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Semester <?= esc($semester['semester']) ?></h6>
            <?php if ($semester['total_sks'] > 0): ?>
                <span class="badge badge-primary">
                    IP <?= number_format((float) $semester['ip'], 2) ?> &middot;
                    <?= esc($semester['total_sks']) ?> SKS
                </span>
            <?php else: ?>
                <span class="badge badge-secondary">Belum ada nilai</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Nilai Angka</th>
                            <th>Huruf</th>
                            <th>Bobot</th>
                            <th>Mutu (SKS &times; Bobot)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semester['baris'] as $b): ?>
                            <tr>
                                <td><?= esc($b['kode']) ?></td>
                                <td><?= esc($b['nama']) ?></td>
                                <td><?= esc($b['sks']) ?></td>
                                <td><?= $b['nilai_angka'] === null ? '-' : esc($b['nilai_angka']) ?></td>
                                <td>
                                    <?= $b['nilai_huruf'] === null
                                        ? '<span class="text-muted small">belum dinilai</span>'
                                        : '<span class="badge badge-success">' . esc($b['nilai_huruf']) . '</span>' ?>
                                </td>
                                <td><?= $b['bobot'] === null ? '-' : esc($b['bobot']) ?></td>
                                <td><?= $b['bobot'] === null ? '-' : number_format((float) $b['mutu'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="2">Total semester</td>
                            <td><?= esc($semester['total_sks']) ?></td>
                            <td colspan="2"></td>
                            <td colspan="2"><?= number_format((float) $semester['total_mutu'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="small text-muted mt-2 mb-0">
                IP semester = <?= number_format((float) $semester['total_mutu'], 2) ?>
                / <?= esc($semester['total_sks']) ?> SKS =
                <strong><?= number_format((float) $semester['ip'], 2) ?></strong>
            </p>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>
