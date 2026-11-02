<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar pengguna</h6>
        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah pengguna
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/users') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari nama atau username" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada pengguna.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong><?= esc($u['nama']) ?></strong></td>
                            <td>@<?= esc($u['username']) ?></td>
                            <td><?= esc($u['email'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-<?= $u['role'] === 'admin' ? 'primary' : 'info' ?>">
                                    <?= esc(ucfirst($u['role'])) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= esc($u['created_at'] ?? '-') ?></td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/users/' . $u['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus akun <?= esc($u['username']) ?>?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
