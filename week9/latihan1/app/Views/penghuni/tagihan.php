<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger">
        <strong>Bukti transfer gagal diunggah:</strong>
        <ul class="mb-0 mt-1 small">
            <?php foreach ($errors as $pesan): ?>
                <li><?= esc($pesan) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tagihan saya</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti transfer</th>
                        <th>Catatan admin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tagihan === []): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada tagihan untuk akun Anda.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($tagihan as $t): ?>
                        <?php
                        $bisaUpload = in_array($t['status'], ['belum_bayar', 'ditolak'], true);
                        ?>
                        <tr>
                            <td><?= esc($t['bulan']) ?>/<?= esc($t['tahun']) ?></td>
                            <td>Rp <?= number_format((int) $t['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge badge-<?= $t['status'] === 'lunas' ? 'success' : ($t['status'] === 'menunggu_verifikasi' ? 'warning' : ($t['status'] === 'ditolak' ? 'danger' : 'secondary')) ?>">
                                    <?= esc(ucfirst(str_replace('_', ' ', $t['status']))) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($t['bukti_transfer'] !== null): ?>
                                    <a href="<?= site_url('berkas/bukti/' . $t['bukti_transfer']) ?>" target="_blank"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-receipt"></i> Lihat bukti
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Belum diunggah</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= esc($t['catatan'] ?? '-') ?></td>
                            <td class="text-center text-nowrap">
                                <?php if ($bisaUpload): ?>
                                    <button class="btn btn-sm btn-success" type="button" data-toggle="collapse"
                                            data-target="#formBayar<?= esc($t['id']) ?>" aria-expanded="false">
                                        <i class="fas fa-upload fa-sm mr-1"></i> Unggah bukti
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ($bisaUpload): ?>
                            <tr class="collapse" id="formBayar<?= esc($t['id']) ?>">
                                <td colspan="6" class="bg-light">
                                    <form action="<?= site_url('penghuni/tagihan/' . $t['id'] . '/bayar') ?>"
                                          method="post" enctype="multipart/form-data" class="row align-items-end">
                                        <?= csrf_field() ?>

                                        <div class="col-md-5 mb-2">
                                            <label for="bukti<?= esc($t['id']) ?>" class="small font-weight-bold">
                                                Bukti transfer (JPG/PNG/WEBP, maks 2 MB)
                                            </label>
                                            <input type="file" class="form-control-file" id="bukti<?= esc($t['id']) ?>"
                                                   name="bukti_transfer" accept="image/*" required>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label for="catatan<?= esc($t['id']) ?>" class="small font-weight-bold">
                                                Catatan (opsional)
                                            </label>
                                            <input type="text" class="form-control form-control-sm"
                                                   id="catatan<?= esc($t['id']) ?>" name="catatan"
                                                   placeholder="Contoh: transfer dari BCA a.n. Rina">
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <button type="submit" class="btn btn-success btn-sm btn-block">
                                                Kirim bukti
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body small text-muted">
        Setelah bukti dikirim, status tagihan berubah menjadi <strong>menunggu verifikasi</strong>.
        Admin akan memeriksa nominal transfer, lalu menandainya <strong>lunas</strong> atau
        <strong>ditolak</strong> beserta alasannya.
    </div>
</div>

<?= $this->endSection() ?>
