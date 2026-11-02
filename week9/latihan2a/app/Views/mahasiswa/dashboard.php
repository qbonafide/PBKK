<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php if ($mhs === null): ?>
    <div class="alert alert-warning">
        Akun Anda belum terhubung dengan data mahasiswa. Silakan hubungi admin akademik.
    </div>
<?php else: ?>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">IPK kumulatif</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format((float) $ipk['ipk'], 2) ?></div>
                <div class="small text-muted mt-1">dari <?= esc($ipk['sks']) ?> SKS yang sudah dinilai</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">SKS diambil tahun ini</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($sksAmbil) ?> SKS</div>
                <div class="small text-muted mt-1">tahun ajaran <?= esc($tahunAjaran ?? '-') ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status KRS</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?= $krsAktif === [] ? 'Belum diisi' : count($krsAktif) . ' mata kuliah' ?>
                </div>
                <div class="small text-muted mt-1">
                    <?= esc($mhs['nim']) ?> &middot; <?= esc($prodi['nama'] ?? '-') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Mata kuliah yang diambil tahun ini</h6>
        <div>
            <a href="<?= site_url('mahasiswa/krs') ?>" class="btn btn-sm btn-primary">Isi KRS</a>
            <a href="<?= site_url('mahasiswa/khs') ?>" class="btn btn-sm btn-outline-primary">Lihat KHS</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Dosen</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($krsAktif === []): ?>
                        <tr><td colspan="5" class="text-center text-muted">
                            Belum ada mata kuliah yang diambil pada tahun ajaran ini.
                        </td></tr>
                    <?php endif; ?>

                    <?php foreach ($krsAktif as $k): ?>
                        <tr>
                            <td><strong><?= esc($k['kode']) ?></strong></td>
                            <td><?= esc($k['nama']) ?></td>
                            <td><?= esc($k['sks']) ?></td>
                            <td><?= esc($k['nama_dosen'] ?? '-') ?></td>
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

<?php endif; ?>

<?= $this->endSection() ?>
