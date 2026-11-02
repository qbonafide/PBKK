<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar kamar</h6>
        <a href="<?= site_url('admin/kamar/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah kamar
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/kamar') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari nomor kamar atau fasilitas" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/kamar') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nomor</th>
                        <th>Tipe</th>
                        <th>Harga</th>
                        <th>Luas</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($kamar === []): ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada kamar yang cocok.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($kamar as $k): ?>
                        <tr>
                            <td style="width: 90px;">
                                <?php $foto = $k['foto'] !== null && $k['foto'] !== ''
                                    ? base_url('uploads/kamar/' . $k['foto'])
                                    : base_url('assets/img/placeholder-kamar.svg'); ?>
                                <img src="<?= esc($foto) ?>" alt="Foto kamar <?= esc($k['nomor_kamar']) ?>"
                                     class="img-thumbnail" style="width: 72px; height: 54px; object-fit: cover;">
                            </td>
                            <td><strong><?= esc($k['nomor_kamar']) ?></strong></td>
                            <td><?= esc(ucfirst($k['tipe'])) ?></td>
                            <td>Rp <?= number_format((int) $k['harga_bulanan'], 0, ',', '.') ?></td>
                            <td><?= esc($k['luas'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-<?= $k['status'] === 'kosong' ? 'success' : ($k['status'] === 'terisi' ? 'secondary' : 'warning') ?>">
                                    <?= esc(ucfirst($k['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/kamar/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/kamar/' . $k['id'] . '/delete') ?>" method="post"
                                      class="d-inline" onsubmit="return confirm('Hapus kamar <?= esc($k['nomor_kamar']) ?>?');">
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
