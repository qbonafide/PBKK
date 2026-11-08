<?php
/**
 * Sidebar area admin/operator.
 * Menu yang tampil mengikuti role agar tampilan konsisten dengan hak akses route.
 */
$menu = [
    ['label' => 'Dashboard', 'url' => 'admin/dashboard', 'match' => 'admin/dashboard'],
    ['label' => 'Pesanan', 'url' => 'admin/orders', 'match' => 'admin/orders*'],
];

if (has_role('admin')) {
    $menu[] = ['label' => 'Kategori', 'url' => 'admin/categories', 'match' => 'admin/categories*'];
    $menu[] = ['label' => 'Produk', 'url' => 'admin/products', 'match' => 'admin/products*'];
}

$menu[] = ['label' => 'Pembayaran', 'url' => 'admin/payments', 'match' => 'admin/payments*'];

if (has_role('admin')) {
    $menu[] = ['label' => 'Laporan', 'url' => 'admin/reports', 'match' => 'admin/reports*'];
}
?>
<aside class="se-sidebar">
    <div>
        <a class="se-brand d-inline-block" href="<?= site_url('admin/dashboard') ?>">Shop<span>Ease</span></a>
        <div class="se-sidebar-role mt-1">Panel <?= esc(current_user()['role'] ?? '') ?></div>
    </div>

    <div>
        <p class="se-sidebar-heading">Menu utama</p>
        <nav class="se-sidebar-nav">
            <?php foreach ($menu as $item) : ?>
                <a class="se-sidebar-link <?= url_is($item['match']) ? 'active' : '' ?>" href="<?= site_url($item['url']) ?>">
                    <span><?= esc($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="se-sidebar-nav">
        <a class="se-sidebar-link" href="<?= site_url('/') ?>">Kembali ke toko</a>
        <form action="<?= site_url('logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="se-sidebar-link border-0 bg-transparent w-100 text-start">Keluar</button>
        </form>
    </div>

    <div class="se-sidebar-footer">
        ShopEase
    </div>
</aside>
