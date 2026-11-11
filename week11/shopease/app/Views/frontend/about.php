<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <div class="row g-5 align-items-start">
        <div class="col-lg-7">
            <h1 class="h2 mb-3">Belanja kebutuhan apapun di ShopEase</h1>

            <p class="text-soft">
                Tersedia elektronik, pakaian, aksesoris, dan perlengkapan rumah.
                Setiap produk mencantumkan harga dan stok yang tersedia.
            </p>
            <p class="text-soft mb-0">
                Setelah checkout, lakukan pembayaran melalui transfer dan unggah bukti
                pembayaran pada halaman pesanan. Pesanan diproses setelah pembayaran
                diperiksa.
            </p>
        </div>

        <div class="col-lg-5">
            <div class="se-panel">
                <div class="se-panel-header">Cara memesan</div>
                <div class="se-panel-body">
                    <ol class="text-soft ps-3 d-grid gap-3 mb-0 small">
                        <li>Pilih produk dan tambahkan ke keranjang.</li>
                        <li>Periksa jumlah, lalu lanjutkan ke checkout.</li>
                        <li>Unggah bukti transfer pada detail pesanan.</li>
                        <li>Pantau status hingga pesanan diterima.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
