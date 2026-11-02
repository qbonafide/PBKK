<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-3">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="font-weight-bold text-primary mb-2">
                    <?= esc($matkul['kode']) ?> &middot; <?= esc($matkul['nama']) ?>
                </h6>
                <p class="small text-muted mb-1">
                    <?= esc($matkul['sks']) ?> SKS &middot; Semester <?= esc($matkul['semester']) ?>
                    &middot; Kuota <?= esc($matkul['kuota']) ?>
                </p>
                <p class="small text-muted mb-0">
                    Tahun ajaran <strong><?= esc($tahunAjaran) ?></strong> &middot;
                    nilai angka 0-100 otomatis dikonversi ke huruf dan bobot.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body small">
                <div class="font-weight-bold text-gray-700 mb-1">Skala nilai</div>
                A &ge; 85 (4.0) &middot; AB &ge; 75 (3.5) &middot; B &ge; 65 (3.0)<br>
                BC &ge; 60 (2.5) &middot; C &ge; 55 (2.0) &middot; D &ge; 40 (1.0) &middot; E &lt; 40 (0)
            </div>
        </div>
    </div>
</div>

<?php if ($daftarTa !== []): ?>
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <span class="small font-weight-bold text-gray-700 mr-2">Tahun ajaran:</span>
            <?php foreach ($daftarTa as $ta): ?>
                <a href="<?= site_url('dosen/nilai/' . $matkul['id'] . '?ta=' . urlencode($ta['tahun_ajaran'])) ?>"
                   class="btn btn-sm mr-1 mb-1 <?= $ta['tahun_ajaran'] === $tahunAjaran ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= esc($ta['tahun_ajaran']) ?> (<?= esc($ta['jumlah']) ?>)
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar peserta &amp; input nilai</h6>
    </div>
    <div class="card-body">
        <?php if ($peserta === []): ?>
            <div class="alert alert-info mb-0">
                Belum ada mahasiswa yang mengambil kelas ini pada tahun ajaran <?= esc($tahunAjaran) ?>.
            </div>
        <?php else: ?>

            <form action="<?= site_url('dosen/nilai/' . $matkul['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 130px;">NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th style="width: 140px;">Nilai angka</th>
                                <th style="width: 120px;">Huruf</th>
                                <th style="width: 90px;">Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peserta as $p): ?>
                                <tr>
                                    <td><strong><?= esc($p['nim']) ?></strong></td>
                                    <td><?= esc($p['nama']) ?></td>
                                    <td class="small text-muted"><?= esc($p['nama_prodi'] ?? '-') ?></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm"
                                               name="nilai[<?= esc($p['id']) ?>]" min="0" max="100"
                                               value="<?= $p['nilai_angka'] === null ? '' : esc($p['nilai_angka']) ?>"
                                               placeholder="-">
                                    </td>
                                    <td>
                                        <?php if ($p['nilai_huruf'] === null): ?>
                                            <span class="text-muted small">belum dinilai</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><?= esc($p['nilai_huruf']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $p['bobot'] === null ? '<span class="text-muted small">-</span>' : esc($p['bobot']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save fa-sm mr-1"></i> Simpan nilai
                </button>
                <a href="<?= site_url('dosen/kelas') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
