<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pesanan <?= esc($pesanan['invoice']) ?></h6>
                <span class="badge badge-primary"><?= esc($label[$pesanan['status']] ?? $pesanan['status']) ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-3">
                        <thead class="thead-light">
                            <tr>
                                <th>Produk (snapshot)</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pesanan['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?= esc($item['nama_produk']) ?>
                                        <?php if ($item['produk_id'] === null): ?>
                                            <span class="badge badge-secondary">produk dihapus</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= format_rupiah((int) $item['harga']) ?></td>
                                    <td><?= esc($item['qty']) ?></td>
                                    <td><?= format_rupiah((int) $item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row small">
                    <div class="col-md-6">
                        <div class="text-muted">Subtotal</div>
                        <div class="font-weight-bold mb-2"><?= format_rupiah((int) $pesanan['subtotal']) ?></div>

                        <div class="text-muted">Ongkir</div>
                        <div class="font-weight-bold mb-2">
                            <?= (int) $pesanan['ongkir'] === 0 ? 'Gratis' : format_rupiah((int) $pesanan['ongkir']) ?>
                        </div>

                        <div class="text-muted">Total</div>
                        <div class="h5 font-weight-bold text-primary mb-0"><?= format_rupiah((int) $pesanan['total']) ?></div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted">Penerima</div>
                        <div class="font-weight-bold"><?= esc($pesanan['nama_penerima']) ?></div>
                        <div><?= esc($pesanan['no_hp']) ?></div>
                        <div class="mb-2"><?= nl2br(esc($pesanan['alamat_kirim'])) ?></div>

                        <?php if (! empty($pesanan['catatan'])): ?>
                            <div class="text-muted">Catatan pembeli</div>
                            <div class="mb-2"><?= esc($pesanan['catatan']) ?></div>
                        <?php endif; ?>

                        <div class="text-muted">Batas pembayaran</div>
                        <div><?= esc($pesanan['expired_at']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Verifikasi &amp; ubah status</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Status saat ini: <strong><?= esc($label[$pesanan['status']] ?? $pesanan['status']) ?></strong>
                </p>

                <?php if (! empty($pesanan['bukti_bayar'])): ?>
                    <a href="<?= site_url('berkas/bukti/' . $pesanan['bukti_bayar']) ?>" target="_blank"
                       class="btn btn-outline-info btn-sm btn-block mb-3">
                        <i class="fas fa-receipt fa-sm mr-1"></i> Lihat bukti transfer
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning small py-2">Pembeli belum mengunggah bukti transfer.</div>
                <?php endif; ?>

                <?php if ($berikutnya === []): ?>
                    <div class="alert alert-secondary small mb-0">
                        Pesanan sudah di status akhir, tidak ada perubahan status lagi.
                    </div>
                <?php else: ?>
                    <form action="<?= site_url('admin/pesanan/' . $pesanan['id'] . '/status') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="status">Status tujuan</label>
                            <select class="form-control" id="status" name="status" required>
                                <?php foreach ($berikutnya as $status): ?>
                                    <option value="<?= esc($status) ?>"><?= esc($label[$status] ?? $status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">
                                Hanya status yang diizinkan oleh alur pesanan yang muncul di sini.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="no_resi">Nomor resi (isi saat status dikirim)</label>
                            <input type="text" class="form-control" id="no_resi" name="no_resi"
                                   value="<?= esc($pesanan['no_resi'] ?? '') ?>" placeholder="Contoh: JNE1234567890">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm mr-1"></i> Simpan status
                        </button>
                    </form>
                <?php endif; ?>

                <hr>

                <p class="small text-muted mb-0">
                    Pembatalan otomatis mengembalikan stok produk yang sebelumnya ditahan.
                </p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body small">
                <div class="font-weight-bold text-gray-700 mb-1">Alur status yang berlaku</div>
                <div>menunggu_pembayaran &rarr; menunggu_verifikasi / dibatalkan</div>
                <div>menunggu_verifikasi &rarr; diproses / menunggu_pembayaran / dibatalkan</div>
                <div>diproses &rarr; dikirim / dibatalkan</div>
                <div>dikirim &rarr; selesai</div>
                <div>selesai &amp; dibatalkan &rarr; (akhir)</div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
