<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$paymentStatus = $payment['payment_status'] ?? 'unpaid';
$canApprove = $paymentStatus === 'submitted' && $order['order_status'] === 'waiting_verification';
$allowedTransitions = \App\Models\OrderModel::TRANSITIONS[$order['order_status']] ?? [];
$manualTargets = array_values(array_intersect($allowedTransitions, ['cancelled', 'completed']));
?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <div class="fw-semibold h5 mb-1"><?= esc($order['order_number']) ?></div>
        <div class="small text-soft">
            <?= esc($order['customer_name']) ?> &middot; <?= esc($order['customer_email']) ?><br>
            Dibuat <?= esc(date('d M Y H:i', strtotime($order['order_date']))) ?>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?= order_status_badge($order['order_status']) ?>
        <?= payment_status_badge($paymentStatus) ?>
        <a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="se-panel mb-3">
            <div class="se-panel-header">Rincian item</div>
            <div class="table-responsive">
                <table class="table table-se align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($item['product_name']) ?></td>
                                <td class="text-end"><?= esc(rupiah($item['price'])) ?></td>
                                <td class="text-center"><?= esc((string) (int) $item['quantity']) ?></td>
                                <td class="text-end"><?= esc(rupiah($item['subtotal'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="se-panel-body">
                <div class="se-summary-row">
                    <span>Subtotal</span><strong><?= esc(rupiah($order['subtotal'])) ?></strong>
                </div>
                <div class="se-summary-row">
                    <span>Ongkos kirim</span><strong><?= esc(rupiah($order['shipping_cost'])) ?></strong>
                </div>
                <div class="se-summary-total">
                    <span>Total</span><span><?= esc(rupiah($order['total'])) ?></span>
                </div>
            </div>
        </div>

        <div class="se-panel mb-3">
            <div class="se-panel-header">Verifikasi pembayaran</div>
            <div class="se-panel-body">
                <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
                    <div>
                        <div class="small text-soft">Metode</div>
                        <div class="fw-semibold"><?= esc($payment['method'] ?? '-') ?></div>
                    </div>
                    <div>
                        <div class="small text-soft">Jumlah</div>
                        <div class="fw-semibold"><?= esc(rupiah($payment['amount'] ?? $order['total'])) ?></div>
                    </div>
                    <div>
                        <div class="small text-soft">Status</div>
                        <div><?= payment_status_badge($paymentStatus) ?></div>
                    </div>
                </div>

                <?php if (! empty($payment['proof_file'])) : ?>
                    <p class="small mb-2">
                        Bukti transfer:
                        <a href="<?= base_url('uploads/payments/' . esc($payment['proof_file'], 'attr')) ?>" target="_blank" rel="noopener">
                            buka berkas bukti
                        </a>
                    </p>
                    <a href="<?= base_url('uploads/payments/' . esc($payment['proof_file'], 'attr')) ?>" target="_blank" rel="noopener">
                        <img src="<?= base_url('uploads/payments/' . esc($payment['proof_file'], 'attr')) ?>"
                             alt="Bukti pembayaran <?= esc($order['order_number']) ?>"
                             style="max-height: 220px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    </a>
                <?php else : ?>
                    <p class="small text-soft">Customer belum mengunggah bukti pembayaran.</p>
                <?php endif; ?>

                <?php if (! empty($payment['notes'])) : ?>
                    <div class="alert alert-warning small mt-3 mb-0">
                        <strong>Catatan verifikasi:</strong> <?= esc($payment['notes']) ?>
                    </div>
                <?php endif; ?>

                <?php if (has_role('admin') && $canApprove) : ?>
                    <div class="se-divider"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <form method="post" action="<?= site_url('admin/payments/' . (int) $payment['id'] . '/approve') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Setujui pembayaran pesanan ini?')">Setujui pembayaran</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="post" action="<?= site_url('admin/payments/' . (int) $payment['id'] . '/reject') ?>">
                                <?= csrf_field() ?>
                                <label for="notes" class="form-label small">Alasan penolakan</label>
                                <textarea class="form-control mb-2" id="notes" name="notes" rows="2"
                                          placeholder="mis. nominal transfer tidak sesuai" required></textarea>
                                <button type="submit" class="btn btn-outline-danger w-100">Tolak bukti</button>
                            </form>
                        </div>
                    </div>
                <?php elseif (! has_role('admin')) : ?>
                    <p class="small text-soft mb-0">Verifikasi pembayaran hanya dapat dilakukan oleh admin.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="se-panel mb-3">
            <div class="se-panel-header">Alamat pengiriman</div>
            <div class="se-panel-body small">
                <div class="fw-semibold"><?= esc($order['recipient_name'] ?? '-') ?></div>
                <div class="text-soft">
                    <?= esc($order['phone'] ?? '') ?><br>
                    <?= esc($order['address'] ?? '') ?><br>
                    <?= esc(($order['city'] ?? '') . ', ' . ($order['province'] ?? '') . ' ' . ($order['postal_code'] ?? '')) ?>
                </div>
                <?php if (! empty($order['notes'])) : ?>
                    <div class="se-divider"></div>
                    <div><span class="text-soft">Catatan customer:</span> <?= esc($order['notes']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="se-panel mb-3">
            <div class="se-panel-header">Pengiriman</div>
            <div class="se-panel-body">
                <?php if ($shipment !== null) : ?>
                    <div class="small mb-3">
                        <div class="se-summary-row"><span>Kurir</span><strong><?= esc($shipment['courier'] ?? '-') ?></strong></div>
                        <div class="se-summary-row"><span>Resi</span><strong><?= esc($shipment['tracking_number'] ?? '-') ?></strong></div>
                        <div class="se-summary-row"><span>Status</span><span><?= esc(shipment_status_label($shipment['shipment_status'])) ?></span></div>
                    </div>
                <?php endif; ?>

                <?php if (in_array($order['order_status'], ['processing', 'shipped'], true)) : ?>
                    <form method="post" action="<?= site_url('admin/shipments/' . (int) $order['id']) ?>" class="d-grid gap-2">
                        <?= csrf_field() ?>
                        <div>
                            <label for="courier" class="form-label">Kurir</label>
                            <input type="text" class="form-control" id="courier" name="courier"
                                   value="<?= esc($shipment['courier'] ?? '') ?>" placeholder="mis. JNE Reguler" required>
                        </div>
                        <div>
                            <label for="tracking_number" class="form-label">Nomor resi</label>
                            <input type="text" class="form-control" id="tracking_number" name="tracking_number"
                                   value="<?= esc($shipment['tracking_number'] ?? '') ?>" placeholder="mis. JNE123456789" required>
                        </div>
                        <button type="submit" class="btn btn-brand">
                            <?= $order['order_status'] === 'processing' ? 'Simpan & tandai dikirim' : 'Perbarui data pengiriman' ?>
                        </button>
                    </form>
                <?php else : ?>
                    <p class="small text-soft mb-0">
                        Data pengiriman dapat diisi setelah pembayaran diverifikasi (status pesanan
                        <em>Diproses</em>).
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($manualTargets !== []) : ?>
            <div class="se-panel">
                <div class="se-panel-header">Ubah status manual</div>
                <div class="se-panel-body d-grid gap-2">
                    <?php foreach ($manualTargets as $target) : ?>
                        <form method="post" action="<?= site_url('admin/orders/' . (int) $order['id'] . '/status') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_status" value="<?= esc($target, 'attr') ?>">
                            <button type="submit"
                                    class="btn w-100 <?= $target === 'cancelled' ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                    onclick="return confirm('Ubah status menjadi <?= esc(order_status_label($target), 'attr') ?>?')">
                                Tandai <?= esc(order_status_label($target)) ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                    <p class="small text-soft mb-0">
                        Status lain ditentukan otomatis: verifikasi pembayaran &rarr; <em>Diproses</em>,
                        input resi &rarr; <em>Dikirim</em>.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
