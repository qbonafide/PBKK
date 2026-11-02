<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark mb-3">Kos putri &amp; putra &middot; Sleman, Yogyakarta</span>
                <h1 class="display-5 fw-bold mb-3">Tinggal nyaman, bayar kos jadi lebih rapi.</h1>
                <p class="lead mb-4">
                    KosKu membantu pemilik kos mencatat kamar, penghuni, dan pembayaran bulanan
                    dalam satu tempat. Calon penghuni bisa melihat kamar yang masih kosong lengkap
                    dengan foto, harga, dan fasilitasnya.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= site_url('kamar') ?>" class="btn btn-warning fw-semibold px-4">Lihat Kamar Kosong</a>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-outline-light px-4">Hubungi Pengelola</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hero-stat">
                            <div class="fs-3 fw-bold"><?= esc($jumlahTotal) ?></div>
                            <div class="small">Total kamar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat">
                            <div class="fs-3 fw-bold"><?= esc($jumlahKosong) ?></div>
                            <div class="small">Kamar siap huni</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="hero-stat">
                            <div class="small mb-1">Fasilitas bersama</div>
                            <div class="fw-semibold">WiFi &middot; Dapur &middot; Parkir &middot; CCTV 24 jam</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="bagian-judul h3 mb-1">Kamar yang siap dihuni</h2>
                <p class="text-muted mb-0">Harga sudah termasuk listrik dan air.</p>
            </div>
            <a href="<?= site_url('kamar') ?>" class="btn btn-outline-success btn-sm">Semua kamar</a>
        </div>

        <div class="row g-4">
            <?php if ($kamarKosong === []): ?>
                <div class="col-12">
                    <div class="alert alert-warning mb-0">Belum ada kamar kosong saat ini. Silakan hubungi pengelola.</div>
                </div>
            <?php endif; ?>

            <?php foreach ($kamarKosong as $k): ?>
                <?= view('partials/kartu_kamar', ['kamar' => $k]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Data kamar transparan</h3>
                <p class="text-muted mb-0">
                    Setiap kamar punya foto, tipe, harga, dan status yang selalu diperbarui admin,
                    jadi calon penghuni tidak perlu menebak-nebak.
                </p>
            </div>
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Tagihan jelas tiap bulan</h3>
                <p class="text-muted mb-0">
                    Penghuni bisa melihat tagihan, mengunggah bukti transfer, dan memantau status
                    verifikasi tanpa harus chat bolak-balik.
                </p>
            </div>
            <div class="col-md-4">
                <h3 class="h5 fw-bold">Kelola dari satu dashboard</h3>
                <p class="text-muted mb-0">
                    Admin memantau kamar terisi, tunggakan, dan pemasukan bulan ini lewat satu
                    halaman ringkasan.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
