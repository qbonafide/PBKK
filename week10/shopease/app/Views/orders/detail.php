<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$paymentStatus = $payment['payment_status'] ?? 'unpaid';
$canUpload = in_array($order['order_status'], ['pending_payment', 'waiting_verification'], true)
    && $paymentStatus !== 'verified';
?>
<div class="container section-pad">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($order['order_number']) ?></h1>
            <p class="text-soft mb-0 small">
                Dibuat <?= esc(date('d M Y H:i', strtotime($order['order_date']))) ?> &middot;
                <?= order_status_badge($order['order_status']) ?>
            </p>
        </div>
        <a href="<?= site_url('orders') ?>" class="btn btn-outline-secondary btn-sm">&larr; Riwayat pesanan</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="se-panel mb-4">
                <div class="se-panel-header">Item pesanan</div>
                <div class="table-responsive">
                    <table class="table table-se align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Harga saat beli</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= esc(product_image($item['image'] ?? null)) ?>" class="se-thumb-sm"
                                                 alt="Foto <?= esc($item['product_name']) ?>">
                                            <div>
                                                <div class="fw-semibold"><?= esc($item['product_name']) ?></div>
                                                <a class="small" href="<?= site_url('product/' . (int) $item['product_id']) ?>">Lihat produk</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end"><?= esc(rupiah($item['price'])) ?></td>
                                    <td class="text-center"><?= esc((string) (int) $item['quantity']) ?></td>
                                    <td class="text-end fw-semibold"><?= esc(rupiah($item['subtotal'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="se-panel mb-4">
                <div class="se-panel-header">Alamat pengiriman</div>
                <div class="se-panel-body">
                    <div class="fw-semibold"><?= esc($order['recipient_name'] ?? 'Alamat tidak tersedia') ?></div>
                    <div class="small text-soft">
                        <?= esc($order['phone'] ?? '') ?><br>
                        <?= esc($order['address'] ?? '') ?><br>
                        <?= esc(($order['city'] ?? '') . ', ' . ($order['province'] ?? '') . ' ' . ($order['postal_code'] ?? '')) ?>
                    </div>
                    <?php if (! empty($order['notes'])) : ?>
                        <div class="se-divider"></div>
                        <div class="small"><span class="text-soft">Catatan:</span> <?= esc($order['notes']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="se-panel">
                <div class="se-panel-header">Pembayaran</div>
                <div class="se-panel-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <div class="se-muted-label mb-1">Status pembayaran</div>
                            <div><?= payment_status_badge($paymentStatus) ?></div>
                        </div>
                        <div class="text-end">
                            <div class="se-muted-label mb-1">Jumlah tagihan</div>
                            <div class="se-price"><?= esc(rupiah($payment['amount'] ?? $order['total'])) ?></div>
                        </div>
                    </div>

                    <?php if (! empty($payment['proof_file'])) : ?>
                        <p class="small text-soft">
                            Bukti pembayaran terakhir:
                            <a href="<?= base_url('uploads/payments/' . esc($payment['proof_file'], 'attr')) ?>" target="_blank" rel="noopener">lihat berkas</a>
                        </p>
                    <?php endif; ?>

                    <?php if ($paymentStatus === 'rejected' && ! empty($payment['notes'])) : ?>
                        <div class="alert alert-danger small">
                            <strong>Bukti ditolak:</strong> <?= esc($payment['notes']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($canUpload) : ?>
                        <div class="se-divider"></div>
                        <form method="post" action="<?= site_url('orders/' . (int) $order['id'] . '/payment') ?>"
                              enctype="multipart/form-data" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-md-5">
                                <label for="method" class="form-label">Metode pembayaran</label>
                                <select class="form-select" id="method" name="method">
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="Virtual Account">Virtual Account</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label for="proof" class="form-label">Bukti pembayaran (JPG/PNG/WEBP, maks. 2 MB)</label>
                                <input type="file" class="form-control" id="proof" name="proof"
                                       accept="image/jpeg,image/png,image/webp" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-brand">Unggah bukti pembayaran</button>
                            </div>
                        </form>
                    <?php elseif ($paymentStatus === 'verified') : ?>
                        <div class="alert alert-success small mb-0">Pembayaran sudah diverifikasi admin.</div>
                    <?php else : ?>
                        <div class="alert alert-secondary small mb-0">
                            Unggah bukti pembayaran hanya dapat dilakukan saat pesanan menunggu pembayaran
                            atau menunggu verifikasi.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="se-panel mb-4">
                <div class="se-panel-header">Ringkasan biaya</div>
                <div class="se-panel-body">
                    <div class="se-summary-row">
                        <span>Subtotal</span>
                        <strong><?= esc(rupiah($order['subtotal'])) ?></strong>
                    </div>
                    <div class="se-summary-row">
                        <span>Ongkos kirim</span>
                        <strong><?= esc(rupiah($order['shipping_cost'])) ?></strong>
                    </div>
                    <div class="se-summary-total">
                        <span>Total</span>
                        <span><?= esc(rupiah($order['total'])) ?></span>
                    </div>
                </div>
            </div>

            <div class="se-panel">
                <div class="se-panel-header">Pengiriman</div>
                <div class="se-panel-body">
                    <?php if ($shipment === null) : ?>
                        <p class="text-soft small mb-0">Belum ada data pengiriman. Nomor resi akan muncul setelah pesanan dikirim.</p>
                    <?php else : ?>
                        <div class="se-summary-row">
                            <span>Kurir</span>
                            <strong><?= esc($shipment['courier'] ?? '-') ?></strong>
                        </div>
                        <div class="se-summary-row">
                            <span>Nomor resi</span>
                            <strong><?= esc($shipment['tracking_number'] ?? '-') ?></strong>
                        </div>
                        <div class="se-summary-row">
                            <span>Status</span>
                            <strong><?= esc(shipment_status_label($shipment['shipment_status'])) ?></strong>
                        </div>
                        <?php if (! empty($shipment['shipped_at'])) : ?>
                            <div class="se-summary-row">
                                <span>Dikirim</span>
                                <span><?= esc(date('d M Y H:i', strtotime($shipment['shipped_at']))) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (! empty($shipment['delivered_at'])) : ?>
                            <div class="se-summary-row">
                                <span>Diterima</span>
                                <span><?= esc(date('d M Y H:i', strtotime($shipment['delivered_at']))) ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
