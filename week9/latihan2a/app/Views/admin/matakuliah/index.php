<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar mata kuliah</h6>
        <a href="<?= site_url('admin/matakuliah/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah mata kuliah
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/matakuliah') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari kode atau nama mata kuliah" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/matakuliah') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Prodi</th>
                        <th>Dosen</th>
                        <th>SKS</th>
                        <th>Smt</th>
                        <th>Kuota</th>
                        <th>Jadwal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($matkul === []): ?>
                        <tr><td colspan="9" class="text-center text-muted">Belum ada mata kuliah.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($matkul as $m): ?>
                        <tr>
                            <td><strong><?= esc($m['kode']) ?></strong></td>
                            <td><?= esc($m['nama']) ?></td>
                            <td><?= esc($m['nama_prodi'] ?? '-') ?></td>
                            <td><?= esc($m['nama_dosen'] ?? '-') ?></td>
                            <td><?= esc($m['sks']) ?></td>
                            <td><?= esc($m['semester']) ?></td>
                            <td><?= esc($m['kuota']) ?></td>
                            <td class="small text-muted">
                                <?= esc($m['hari'] ?? '-') ?>
                                <?= $m['jam_mulai'] !== null ? esc(substr((string) $m['jam_mulai'], 0, 5)) : '' ?>
                                <?= $m['jam_selesai'] !== null ? '-' . esc(substr((string) $m['jam_selesai'], 0, 5)) : '' ?>
                                <br><?= esc($m['ruang'] ?? '-') ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/matakuliah/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/matakuliah/' . $m['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus mata kuliah <?= esc($m['nama']) ?>?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>
