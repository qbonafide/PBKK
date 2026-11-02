<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-3">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="font-weight-bold text-primary mb-2">Kartu Rencana Studi</h6>
                <p class="small text-muted mb-1">
                    Tahun ajaran <strong><?= esc($tahunAjaran) ?></strong> &middot;
                    batas maksimal <strong><?= esc($maxSks) ?> SKS</strong>
                </p>
                <p class="small text-muted mb-0">
                    SKS yang sudah diambil tahun ini: <strong><?= esc($sksSekarang) ?> SKS</strong>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <a href="<?= site_url('mahasiswa/krs/cetak') ?>" class="btn btn-outline-primary btn-sm mb-2" target="_blank">
                    <i class="fas fa-print fa-sm mr-1"></i> Cetak KRS
                </a>
                <a href="<?= site_url('mahasiswa/khs') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-scroll fa-sm mr-1"></i> Lihat KHS
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Mata kuliah yang sudah diambil</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Smt</th>
                        <th>Dosen</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($diambil === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">
                            Belum ada mata kuliah yang diambil pada tahun ajaran ini.
                        </td></tr>
                    <?php endif; ?>

                    <?php foreach ($diambil as $d): ?>
                        <tr>
                            <td><?= esc($d['kode']) ?></td>
                            <td><?= esc($d['nama']) ?></td>
                            <td><?= esc($d['sks']) ?></td>
                            <td><?= esc($d['semester']) ?></td>
                            <td class="small"><?= esc($d['nama_dosen'] ?? '-') ?></td>
                            <td>
                                <?= $d['nilai_huruf'] === null
                                    ? '<span class="badge badge-secondary">Belum dinilai</span>'
                                    : '<span class="badge badge-success">' . esc($d['nilai_huruf']) . '</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Mata kuliah yang ditawarkan</h6>
    </div>
    <div class="card-body">
        <form action="<?= site_url('mahasiswa/krs') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">Pilih</th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Smt</th>
                            <th>Dosen</th>
                            <th>Jadwal</th>
                            <th>Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($daftar === []): ?>
                            <tr><td colspan="8" class="text-center text-muted">
                                Belum ada mata kuliah untuk program studi Anda.
                            </td></tr>
                        <?php endif; ?>

                        <?php foreach ($daftar as $m): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($m['sudah_ambil']): ?>
                                        <i class="fas fa-check text-success" title="Sudah diambil"></i>
                                    <?php elseif ($m['penuh']): ?>
                                        <i class="fas fa-ban text-danger" title="Kuota penuh"></i>
                                    <?php else: ?>
                                        <input type="checkbox" name="matakuliah[]" value="<?= esc($m['id']) ?>">
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= esc($m['kode']) ?></strong></td>
                                <td>
                                    <?= esc($m['nama']) ?>
                                    <?php if ($m['sudah_ambil']): ?>
                                        <span class="badge badge-light border ml-1">sudah diambil</span>
                                    <?php elseif ($m['penuh']): ?>
                                        <span class="badge badge-danger ml-1">kuota penuh</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($m['sks']) ?></td>
                                <td><?= esc($m['semester']) ?></td>
                                <td class="small"><?= esc($m['nama_dosen'] ?? '-') ?></td>
                                <td class="small text-muted">
                                    <?= esc($m['hari'] ?? '-') ?>
                                    <?= $m['jam_mulai'] !== null ? esc(substr((string) $m['jam_mulai'], 0, 5)) : '' ?>
                                    <?= $m['jam_selesai'] !== null ? '-' . esc(substr((string) $m['jam_selesai'], 0, 5)) : '' ?>
                                    <br><?= esc($m['ruang'] ?? '-') ?>
                                </td>
                                <td><?= esc($m['peserta']) ?> / <?= esc($m['kuota']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save fa-sm mr-1"></i> Simpan KRS
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
