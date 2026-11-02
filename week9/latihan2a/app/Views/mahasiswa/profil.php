<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Profil mahasiswa</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="small text-muted">NIM</div>
                <div class="font-weight-bold"><?= esc($mhs['nim']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Nama</div>
                <div class="font-weight-bold"><?= esc($mhs['nama']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Program studi</div>
                <div class="font-weight-bold"><?= esc($mhs['nama_prodi'] ?? '-') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Angkatan</div>
                <div class="font-weight-bold"><?= esc($mhs['angkatan']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">Dosen wali</div>
                <div class="font-weight-bold"><?= esc($mhs['nama_wali'] ?? 'Belum ditentukan') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="small text-muted">No. HP</div>
                <div class="font-weight-bold"><?= esc($mhs['no_hp'] ?? '-') ?></div>
            </div>
            <div class="col-12 mb-0">
                <div class="small text-muted">Alamat</div>
                <div class="font-weight-bold"><?= esc($mhs['alamat'] ?? '-') ?></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
