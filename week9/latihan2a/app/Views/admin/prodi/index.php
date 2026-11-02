<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar program studi</h6>
        <a href="<?= site_url('admin/prodi/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah prodi
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/prodi') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari kode atau nama prodi" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/prodi') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($prodi === []): ?>
                        <tr><td colspan="3" class="text-center text-muted">Belum ada program studi.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($prodi as $p): ?>
                        <tr>
                            <td><strong><?= esc($p['kode']) ?></strong></td>
                            <td><?= esc($p['nama']) ?></td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/prodi/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/prodi/' . $p['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus prodi <?= esc($p['nama']) ?>?');">
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
