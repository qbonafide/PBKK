<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <h1 class="h3 mb-4">Keranjang</h1>

    <?php if ($items === []) : ?>
        <div class="se-panel se-empty">
            <p class="fw-semibold mb-1">Keranjang masih kosong</p>
            <p class="mb-3">Tambahkan produk dari katalog untuk mulai berbelanja.</p>
            <a href="<?= site_url('catalog') ?>" class="btn btn-brand btn-sm">Buka katalog</a>
        </div>
    <?php else : ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="se-panel">
                    <div class="table-responsive">
                        <table class="table table-se align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga</th>
                                    <th style="width:150px;">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width:60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $index => $item) : ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= esc(product_image($item['image'])) ?>" class="se-thumb-sm"
                                                     alt="Foto <?= esc($item['name']) ?>">
                                                <div>
                                                    <a class="fw-semibold text-reset" href="<?= site_url('product/' . (int) $item['product_id']) ?>">
                                                        <?= esc($item['name']) ?>
                                                    </a>
                                                    <div class="small text-soft">Stok tersedia: <?= esc((string) (int) $item['stock']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end"><?= esc(rupiah($item['price'])) ?></td>
                                        <td>
                                            <form method="post" action="<?= site_url('cart/update') ?>" class="d-flex gap-2">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                                <label class="visually-hidden" for="qty-<?= (int) $item['id'] ?>">Jumlah</label>
                                                <input type="number" class="form-control form-control-sm" id="qty-<?= (int) $item['id'] ?>"
                                                       name="quantity" value="<?= (int) $item['quantity'] ?>" min="1"
                                                       max="<?= esc((string) (int) $item['stock']) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Ubah</button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-semibold"><?= esc(rupiah($summary['lines'][$index])) ?></td>
                                        <td class="text-end">
                                            <form method="post" action="<?= site_url('cart/remove/' . (int) $item['id']) ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Hapus produk ini dari keranjang?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="<?= site_url('catalog') ?>" class="btn btn-outline-secondary btn-sm mt-3">Lanjut belanja</a>
            </div>

            <div class="col-lg-4">
                <div class="se-panel">
                    <div class="se-panel-header">Ringkasan</div>
                    <div class="se-panel-body">
                        <div class="se-summary-row">
                            <span>Jumlah item</span>
                            <strong><?= esc((string) $summary['total_quantity']) ?> unit</strong>
                        </div>
                        <div class="se-summary-row">
                            <span>Subtotal</span>
                            <strong><?= esc(rupiah($summary['subtotal'])) ?></strong>
                        </div>
                        <div class="se-summary-row">
                            <span>Ongkos kirim</span>
                            <span>Dihitung saat checkout</span>
                        </div>

                        <div class="se-summary-total">
                            <span>Total sementara</span>
                            <span><?= esc(rupiah($summary['subtotal'])) ?></span>
                        </div>

                        <a href="<?= site_url('checkout') ?>" class="btn btn-brand w-100 mt-4">Lanjut ke checkout</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
