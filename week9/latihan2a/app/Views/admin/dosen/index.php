<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar dosen</h6>
        <a href="<?= site_url('admin/dosen/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah dosen
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/dosen') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari NIP atau nama dosen" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/dosen') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Akun</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dosen === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data dosen.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($dosen as $d): ?>
                        <tr>
                            <td><strong><?= esc($d['nip']) ?></strong></td>
                            <td><?= esc($d['nama']) ?></td>
                            <td><?= esc($d['email'] ?? '-') ?></td>
                            <td><?= esc($d['no_hp'] ?? '-') ?></td>
                            <td><span class="badge badge-info">@<?= esc($d['username'] ?? '-') ?></span></td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/dosen/' . $d['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/dosen/' . $d['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus dosen <?= esc($d['nama']) ?> beserta akunnya?');">
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
