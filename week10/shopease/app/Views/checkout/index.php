<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <ol class="se-steps">
        <li>Keranjang</li>
        <li class="is-active">Checkout</li>
        <li>Pembayaran</li>
        <li>Selesai</li>
    </ol>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="se-panel mb-4">
                <div class="se-panel-header">Alamat pengiriman</div>
                <div class="se-panel-body">
                    <?php if ($addresses === []) : ?>
                        <p class="text-soft mb-0">Belum ada alamat tersimpan. Tambahkan alamat pada formulir di bawah.</p>
                    <?php else : ?>
                        <div class="d-grid gap-2">
                            <?php $loop_first = true; ?>
                            <?php foreach ($addresses as $address) : ?>
                                <label class="d-flex gap-3 border rounded-3 p-3 align-items-start" style="cursor:pointer">
                                    <input class="form-check-input mt-1" type="radio" form="formCheckout" name="address_id"
                                           value="<?= (int) $address['id'] ?>"
                                           <?= ((int) $address['is_default'] === 1 || $loop_first) ? 'checked' : '' ?> required>
                                    <span>
                                        <span class="fw-semibold"><?= esc($address['recipient_name']) ?></span>
                                        <?php if ((int) $address['is_default'] === 1) : ?>
                                            <span class="badge text-bg-light border ms-1">Utama</span>
                                        <?php endif; ?>
                                        <span class="small text-soft d-block">
                                            <?= esc($address['phone']) ?> &middot;
                                            <?= esc($address['address']) ?>, <?= esc($address['city']) ?>,
                                            <?= esc($address['province']) ?> <?= esc($address['postal_code'] ?? '') ?>
                                        </span>
                                    </span>
                                </label>
                                <?php $loop_first = false; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="se-divider"></div>

                    <a class="fw-semibold small" data-bs-toggle="collapse" href="#formAlamat" role="button"
                       aria-expanded="false" aria-controls="formAlamat">
                        + Tambah alamat baru
                    </a>

                    <div class="collapse mt-3" id="formAlamat">
                        <form method="post" action="<?= site_url('checkout/address') ?>" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-md-6">
                                <label for="recipient_name" class="form-label">Nama penerima</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name"
                                       value="<?= esc(old('recipient_name')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                       value="<?= esc(old('phone')) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Alamat lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required><?= esc(old('address')) ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">Kota/Kabupaten</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= esc(old('city')) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="province" class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="province" name="province" value="<?= esc(old('province')) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Kode pos</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= esc(old('postal_code')) ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-brand btn-sm">Simpan alamat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="se-panel">
                <div class="se-panel-header">Rincian pesanan</div>
                <div class="table-responsive">
                    <table class="table table-se align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= esc(product_image($item['image'])) ?>" class="se-thumb-sm"
                                                 alt="Foto <?= esc($item['name']) ?>">
                                            <span class="fw-semibold"><?= esc($item['name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end"><?= esc(rupiah($item['price'])) ?></td>
                                    <td class="text-center"><?= esc((string) (int) $item['quantity']) ?></td>
                                    <td class="text-end fw-semibold">
                                        <?= esc(rupiah((float) $item['price'] * (int) $item['quantity'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="se-panel">
                <div class="se-panel-header">Total pembayaran</div>
                <div class="se-panel-body">
                    <form method="post" action="<?= site_url('checkout/process') ?>" id="formCheckout">
                        <?= csrf_field() ?>
                        <input type="hidden" name="checkout_token" value="<?= esc($token, 'attr') ?>">

                        <div class="se-summary-row">
                            <span>Subtotal</span>
                            <strong><?= esc(rupiah($subtotal)) ?></strong>
                        </div>
                        <div class="se-summary-row">
                            <span>Ongkos kirim</span>
                            <strong><?= esc(rupiah($shipping)) ?></strong>
                        </div>

                        <div class="se-summary-total">
                            <span>Total</span>
                            <span><?= esc(rupiah($total)) ?></span>
                        </div>

                        <div class="mt-3">
                            <label for="notes" class="form-label">Catatan pesanan <span class="text-soft fw-normal">(opsional)</span></label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                      placeholder="mis. titip di pos keamanan"></textarea>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 mt-3">Buat pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
