<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar kategori</h6>
        <a href="<?= site_url('admin/kategori/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah kategori
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Jumlah produk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($kategori === []): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada kategori.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($kategori as $k): ?>
                        <tr>
                            <td class="font-weight-bold"><?= esc($k['nama']) ?></td>
                            <td><code><?= esc($k['slug']) ?></code></td>
                            <td><?= esc($k['jumlah_produk']) ?> produk</td>
                            <td class="text-center text-nowrap">
                                <a href="<?= site_url('admin/kategori/' . $k['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/kategori/' . $k['id'] . '/delete') ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori <?= esc($k['nama']) ?>?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
