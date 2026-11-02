<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar penghuni</h6>
        <a href="<?= site_url('admin/penghuni/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah penghuni
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/penghuni') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari nama penghuni atau kamar" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/penghuni') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>KTP</th>
                        <th>Nama</th>
                        <th>Kamar</th>
                        <th>No. HP</th>
                        <th>Masuk</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($penghuni === []): ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data penghuni.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($penghuni as $p): ?>
                        <tr>
                            <td style="width: 90px;">
                                <?php if ($p['foto_ktp'] !== null): ?>
                                    <a href="<?= site_url('berkas/ktp/' . $p['foto_ktp']) ?>" target="_blank">
                                        <img src="<?= site_url('berkas/ktp/' . $p['foto_ktp']) ?>"
                                             alt="KTP <?= esc($p['nama']) ?>" class="img-thumbnail"
                                             style="width: 72px; height: 46px; object-fit: cover;">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($p['nama']) ?></strong><br>
                                <span class="small text-muted">@<?= esc($p['username'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($p['nomor_kamar'] ?? '-') ?></td>
                            <td><?= esc($p['no_hp'] ?? '-') ?></td>
                            <td><?= esc($p['tanggal_masuk'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-<?= $p['status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                    <?= esc(ucfirst($p['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/penghuni/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/penghuni/' . $p['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus data penghuni <?= esc($p['nama']) ?>?');">
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
