<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar mahasiswa</h6>
        <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah mahasiswa
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/mahasiswa') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari NIM atau nama mahasiswa" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Angkatan</th>
                        <th>Dosen Wali</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($mhs === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data mahasiswa.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($mhs as $m): ?>
                        <tr>
                            <td><strong><?= esc($m['nim']) ?></strong></td>
                            <td>
                                <?= esc($m['nama']) ?><br>
                                <span class="small text-muted">@<?= esc($m['username'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($m['kode_prodi'] ?? '-') ?> - <?= esc($m['nama_prodi'] ?? '-') ?></td>
                            <td><?= esc($m['angkatan']) ?></td>
                            <td><?= esc($m['nama_wali'] ?? '-') ?></td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/mahasiswa/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/mahasiswa/' . $m['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus mahasiswa <?= esc($m['nama']) ?> beserta akunnya?');">
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
