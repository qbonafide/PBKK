<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <form class="d-flex gap-2 align-items-end" method="get" action="<?= site_url('admin/payments') ?>">
        <div>
            <label for="status" class="form-label">Filter status pembayaran</label>
            <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                <option value="" <?= $status === '' ? 'selected' : '' ?>>Menunggu verifikasi</option>
                <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Submitted (antrean)</option>
                <option value="verified" <?= $status === 'verified' ? 'selected' : '' ?>>Terverifikasi</option>
                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                <option value="unpaid" <?= $status === 'unpaid' ? 'selected' : '' ?>>Belum dibayar</option>
            </select>
        </div>
    </form>
    <p class="small text-soft mb-0">Hanya admin yang dapat menyetujui atau menolak pembayaran.</p>
</div>

<?php if ($payments === []) : ?>
    <div class="se-panel se-empty">
        <p class="fw-semibold mb-1">Tidak ada pembayaran pada filter ini</p>
        <p class="mb-0">Antrean verifikasi akan muncul setelah customer mengunggah bukti transfer.</p>
    </div>
<?php else : ?>
    <div class="d-grid gap-3">
        <?php foreach ($payments as $payment) : ?>
            <div class="se-panel">
                <div class="se-panel-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-3">
                            <div class="se-muted-label mb-1">Pesanan</div>
                            <a class="fw-semibold text-reset" href="<?= site_url('admin/orders/' . (int) $payment['order_id']) ?>">
                                <?= esc($payment['order_number']) ?>
                            </a>
                            <div class="small text-soft"><?= esc($payment['customer_name']) ?></div>
                        </div>

                        <div class="col-lg-2">
                            <div class="se-muted-label mb-1">Jumlah</div>
                            <div class="fw-semibold"><?= esc(rupiah($payment['amount'])) ?></div>
                        </div>

                        <div class="col-lg-2">
                            <div class="se-muted-label mb-1">Status</div>
                            <?= payment_status_badge($payment['payment_status']) ?>
                            <div class="small text-soft mt-1">Pesanan: <?= esc(order_status_label($payment['order_status'])) ?></div>
                        </div>

                        <div class="col-lg-2">
                            <div class="se-muted-label mb-1">Bukti</div>
                            <?php if (! empty($payment['proof_file'])) : ?>
                                <a href="<?= base_url('uploads/payments/' . esc($payment['proof_file'], 'attr')) ?>"
                                   target="_blank" rel="noopener" class="small fw-semibold">Lihat berkas</a>
                            <?php else : ?>
                                <span class="small text-soft">Belum ada</span>
                            <?php endif; ?>
                        </div>

                        <div class="col-lg-3">
                            <?php if (has_role('admin') && $payment['payment_status'] === 'submitted' && $payment['order_status'] === 'waiting_verification') : ?>
                                <div class="d-flex gap-2">
                                    <form method="post" action="<?= site_url('admin/payments/' . (int) $payment['id'] . '/approve') ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Setujui pembayaran ini?')">Setujui</button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#tolak-<?= (int) $payment['id'] ?>">
                                        Tolak
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="tolak-<?= (int) $payment['id'] ?>">
                                    <form method="post" action="<?= site_url('admin/payments/' . (int) $payment['id'] . '/reject') ?>">
                                        <?= csrf_field() ?>
                                        <label class="form-label small" for="notes-<?= (int) $payment['id'] ?>">Alasan</label>
                                        <textarea class="form-control form-control-sm mb-2" id="notes-<?= (int) $payment['id'] ?>"
                                                  name="notes" rows="2" required></textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Kirim penolakan</button>
                                    </form>
                                </div>
                            <?php elseif ($payment['payment_status'] === 'submitted') : ?>
                                <span class="small text-soft">Menunggu status pesanan siap verifikasi.</span>
                            <?php else : ?>
                                <span class="small text-soft">
                                    <?= $payment['verified_at'] !== null ? 'Diproses ' . esc(date('d M Y H:i', strtotime($payment['verified_at']))) : 'Tidak ada aksi' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (! empty($payment['notes'])) : ?>
                        <div class="alert alert-warning small mt-3 mb-0">
                            <strong>Catatan:</strong> <?= esc($payment['notes']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex justify-content-center">
        <?= $pager->links('default', 'shopease') ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
