<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <div class="row g-5">
        <div class="col-lg-6">
            <h1 class="h2 mb-3">Hubungi kami</h1>
            <p class="text-soft mb-4">
                Untuk pertanyaan tentang pesanan, sebutkan nomor pesanan
                (format <span class="fw-semibold">SE-YYYYMMDD-XXXX</span>) agar riwayatnya mudah ditemukan.
            </p>

            <div class="se-panel">
                <div class="se-panel-body d-grid gap-3">
                    <div class="d-flex justify-content-between gap-3">
                        <span class="text-soft">Email</span>
                        <span class="fw-semibold">halo@shopease.test</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3">
                        <span class="text-soft">Telepon</span>
                        <span class="fw-semibold">(021) 0000-0001</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3">
                        <span class="text-soft">Jam layanan</span>
                        <span class="fw-semibold">Senin&ndash;Jumat, 09.00&ndash;17.00 WIB</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3">
                        <span class="text-soft">Alamat</span>
                        <span class="fw-semibold text-end">Jl. Contoh No. 1, Jakarta Selatan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="se-panel h-100">
                <div class="se-panel-header">Sebelum menghubungi</div>
                <div class="se-panel-body">
                    <ol class="text-soft ps-3 d-grid gap-3 mb-0 small">
                        <li>Status pesanan dan pembayaran dapat dilihat pada menu Pesanan.</li>
                        <li>Alasan penolakan bukti transfer tampil pada detail pesanan.</li>
                        <li>Nomor resi tersedia setelah pesanan ditandai dikirim.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
