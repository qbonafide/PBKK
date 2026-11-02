<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Mahasiswa perwalian &middot; <?= esc($dosen['nama']) ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Angkatan</th>
                        <th>No. HP</th>
                        <th>SKS Dinilai</th>
                        <th>IPK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($mahasiswa === []): ?>
                        <tr><td colspan="7" class="text-center text-muted">
                            Belum ada mahasiswa yang Anda walikan.
                        </td></tr>
                    <?php endif; ?>

                    <?php foreach ($mahasiswa as $m): ?>
                        <tr>
                            <td><strong><?= esc($m['nim']) ?></strong></td>
                            <td><?= esc($m['nama']) ?></td>
                            <td><?= esc($m['nama_prodi'] ?? '-') ?></td>
                            <td><?= esc($m['angkatan']) ?></td>
                            <td><?= esc($m['no_hp'] ?? '-') ?></td>
                            <td><?= esc($m['ipk']['sks']) ?> SKS</td>
                            <td><span class="badge badge-primary"><?= number_format((float) $m['ipk']['ipk'], 2) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
