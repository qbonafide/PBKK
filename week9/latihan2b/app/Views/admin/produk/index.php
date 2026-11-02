<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar produk</h6>
        <a href="<?= site_url('admin/produk/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah produk
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/produk') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari nama atau brand" value="<?= esc($keyword) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= site_url('admin/produk') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Ukuran</th>
                        <th>Kondisi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produk === []): ?>
                        <tr><td colspan="9" class="text-center text-muted">Belum ada produk.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($produk as $p): ?>
                        <?php
                        $foto = $p['foto'] !== null && $p['foto'] !== ''
                            ? base_url('uploads/produk/' . $p['foto'])
                            : base_url('assets/img/placeholder-produk.svg');
                        ?>
                        <tr>
                            <td style="width: 80px;">
                                <img src="<?= esc($foto) ?>" alt="Foto <?= esc($p['nama']) ?>"
                                     class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;">
                            </td>
                            <td>
                                <strong><?= esc($p['nama']) ?></strong><br>
                                <span class="small text-muted"><?= esc($p['brand'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($p['nama_kategori'] ?? '-') ?></td>
                            <td><?= esc($p['ukuran'] ?? '-') ?></td>
                            <td><?= esc(label_kondisi((string) $p['kondisi'])) ?></td>
                            <td><?= format_rupiah((int) $p['harga']) ?></td>
                            <td>
                                <span class="badge badge-<?= (int) $p['stok'] === 0 ? 'danger' : ((int) $p['stok'] <= 1 ? 'warning' : 'success') ?>">
                                    <?= esc($p['stok']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= (int) $p['aktif'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int) $p['aktif'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('produk/' . $p['slug']) ?>" target="_blank"
                                   class="btn btn-sm btn-outline-secondary" title="Lihat di toko">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/produk/' . $p['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/produk/' . $p['id'] . '/delete') ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('Hapus produk <?= esc($p['nama']) ?>?');">
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
