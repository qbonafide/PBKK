<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php if ($dosen === null): ?>
    <div class="alert alert-warning">
        Akun Anda belum terhubung dengan data dosen. Silakan hubungi admin akademik.
    </div>
<?php else: ?>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kelas diampu</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= esc($totalKelas) ?> kelas</div>
                <div class="small text-muted mt-1">Kelas pada seluruh tahun ajaran</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mahasiswa di kelas Anda</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= esc($totalMhs) ?> mahasiswa</div>
                <div class="small text-muted mt-1">Total baris KRS di seluruh kelas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dosen</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800"><?= esc($dosen['nama']) ?></div>
                <div class="small text-muted mt-1">NIP <?= esc($dosen['nip']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kelas yang saya ampu</h6>
        <a href="<?= site_url('dosen/kelas') ?>" class="btn btn-sm btn-primary">Kelola nilai</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Smt</th>
                        <th>Jadwal</th>
                        <th>Peserta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($kelas === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada kelas yang diampu.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($kelas as $k): ?>
                        <tr>
                            <td><strong><?= esc($k['kode']) ?></strong></td>
                            <td><?= esc($k['nama']) ?></td>
                            <td><?= esc($k['sks']) ?></td>
                            <td><?= esc($k['semester']) ?></td>
                            <td class="small text-muted">
                                <?= esc($k['hari'] ?? '-') ?>
                                <?= $k['jam_mulai'] !== null ? esc(substr((string) $k['jam_mulai'], 0, 5)) : '' ?>
                                <?= $k['jam_selesai'] !== null ? '-' . esc(substr((string) $k['jam_selesai'], 0, 5)) : '' ?>
                            </td>
                            <td><?= esc($k['peserta']) ?> mhs<br><span class="small text-muted">TA <?= esc($k['tahun_ajaran']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>
