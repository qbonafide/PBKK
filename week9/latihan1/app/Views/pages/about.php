<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <h1 class="bagian-judul h2 mb-3">Tentang KosKu</h1>
                <p>
                    KosKu adalah kos dengan 9 kamar yang dikelola langsung oleh pemilik. Aplikasi ini
                    dibuat sebagai tugas mata kuliah Pemrograman Berbasis Kerangka Kerja dengan
                    CodeIgniter 4. Tadinya pencatatan kamar dan pembayaran dilakukan lewat buku catatan
                    dan pesan WhatsApp, sekarang semuanya masuk ke satu sistem.
                </p>
                <p>
                    Ada dua jenis pengguna. Admin (pemilik kos) mengelola kamar, data penghuni, tagihan,
                    dan akun pengguna. Penghuni hanya bisa melihat kamar yang ia tempati, daftar
                    tagihannya, lalu mengunggah bukti transfer untuk diverifikasi admin.
                </p>

                <h2 class="h4 fw-bold mt-4 mb-3">Fasilitas</h2>
                <div class="row g-3">
                    <?php foreach ([
                        'WiFi gratis di seluruh area',
                        'Dapur bersama dan dispenser',
                        'Parkir motor &amp; mobil',
                        'CCTV 24 jam',
                        'Kunci kamar pribadi',
                        'Listrik dan air termasuk',
                    ] as $fasilitas): ?>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <span class="text-success fw-bold">&#10003;</span>
                                <span><?= $fasilitas ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Aturan tinggal</h2>
                        <ol class="mb-0 ps-3">
                            <li class="mb-2">Pembayaran kos dilakukan setiap bulan, paling lambat tanggal 10.</li>
                            <li class="mb-2">Tamu lawan jenis tidak boleh masuk ke kamar.</li>
                            <li class="mb-2">Menjaga kebersihan area bersama.</li>
                            <li class="mb-2">Tidak boleh membawa hewan peliharaan.</li>
                            <li>Pemberitahuan keluar kos minimal 30 hari sebelumnya.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
