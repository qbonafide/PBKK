<footer class="se-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="se-brand mb-2" style="color:#fff">Shop<span style="color:#8ea3ff">Ease</span></div>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">Jelajahi</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="<?= site_url('catalog') ?>">Katalog produk</a></li>
                    <li><a href="<?= site_url('about') ?>">Tentang kami</a></li>
                    <li><a href="<?= site_url('contact') ?>">Kontak</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h6 class="mb-3">Akun</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <?php if (current_user() === null) : ?>
                        <li><a href="<?= site_url('login') ?>">Masuk</a></li>
                        <li><a href="<?= site_url('register') ?>">Daftar akun</a></li>
                    <?php else : ?>
                        <li><a href="<?= site_url('orders') ?>">Riwayat pesanan</a></li>
                        <li><a href="<?= site_url('cart') ?>">Keranjang</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary-subtle mt-4 pt-3 small">
            <span>&copy; <?= date('Y') ?> ShopEase</span>
        </div>
    </div>
</footer>
