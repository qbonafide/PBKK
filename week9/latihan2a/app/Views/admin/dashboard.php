<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mahasiswa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($jumlahMhs) ?> orang</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dosen</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($jumlahDosen) ?> orang</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Mata kuliah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($jumlahMatkul) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Program studi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($jumlahProdi) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-university fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">KRS terbaru</h6>
                <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-sm btn-primary">Data mahasiswa</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>NIM</th>
                                <th>Mahasiswa</th>
                                <th>Mata kuliah</th>
                                <th>SKS</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($krsTerbaru === []): ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada data KRS.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($krsTerbaru as $k): ?>
                                <tr>
                                    <td><?= esc($k['nim'] ?? '-') ?></td>
                                    <td><?= esc($k['nama_mahasiswa'] ?? '-') ?></td>
                                    <td><?= esc($k['kode'] ?? '-') ?> - <?= esc($k['nama_matakuliah'] ?? '-') ?></td>
                                    <td><?= esc($k['sks'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($k['nilai_huruf'] === null): ?>
                                            <span class="badge badge-secondary">Belum dinilai</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                <?= esc($k['nilai_huruf']) ?> (<?= esc($k['nilai_angka']) ?>)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sebaran mahasiswa per prodi</h6>
            </div>
            <div class="card-body">
                <?php if ($sebaranProdi === []): ?>
                    <p class="text-muted mb-0">Belum ada data.</p>
                <?php endif; ?>

                <?php foreach ($sebaranProdi as $s): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= esc($s['nama'] ?? '-') ?></span>
                            <strong><?= esc($s['jumlah']) ?> mhs</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary"
                                 style="width: <?= $jumlahMhs > 0 ? (int) ((int) $s['jumlah'] / $jumlahMhs * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
