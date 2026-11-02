<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Kelas saya</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Prodi</th>
                        <th>SKS</th>
                        <th>Smt</th>
                        <th>Jadwal</th>
                        <th>Peserta terbaru</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($kelas === []): ?>
                        <tr><td colspan="8" class="text-center text-muted">
                            Anda belum mengampu kelas apa pun.
                        </td></tr>
                    <?php endif; ?>

                    <?php foreach ($kelas as $k): ?>
                        <tr>
                            <td><strong><?= esc($k['kode']) ?></strong></td>
                            <td><?= esc($k['nama']) ?></td>
                            <td><?= esc($k['nama_prodi'] ?? '-') ?></td>
                            <td><?= esc($k['sks']) ?></td>
                            <td><?= esc($k['semester']) ?></td>
                            <td class="small text-muted">
                                <?= esc($k['hari'] ?? '-') ?>
                                <?= $k['jam_mulai'] !== null ? esc(substr((string) $k['jam_mulai'], 0, 5)) : '' ?>
                                <?= $k['jam_selesai'] !== null ? '-' . esc(substr((string) $k['jam_selesai'], 0, 5)) : '' ?>
                                <?= $k['ruang'] !== null ? ' &middot; ' . esc($k['ruang']) : '' ?>
                            </td>
                            <td class="small">
                                <?= esc($k['peserta']) ?> mahasiswa
                                <?php if ($k['tahun_ajaran'] !== []): ?>
                                    <br><span class="text-muted">TA <?= esc($k['tahun_ajaran'][0]['tahun_ajaran']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('dosen/nilai/' . $k['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen fa-sm mr-1"></i> Input nilai
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
