<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<section class="py-4 border-bottom bg-white">
    <div class="container">
        <h1 class="bagian-judul h2 mb-1">Daftar Kamar</h1>
        <p class="text-muted mb-0">Cari kamar sesuai tipe dan status yang Anda butuhkan.</p>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <form method="get" action="<?= site_url('kamar') ?>" class="row g-2 align-items-end mb-4">
            <div class="col-md-5">
                <label for="q" class="form-label small text-muted mb-1">Kata kunci</label>
                <input type="text" class="form-control" id="q" name="q" value="<?= esc($keyword) ?>"
                       placeholder="Nomor kamar atau fasilitas, mis. AC">
            </div>
            <div class="col-md-3">
                <label for="tipe" class="form-label small text-muted mb-1">Tipe</label>
                <select class="form-select" id="tipe" name="tipe">
                    <option value="">Semua tipe</option>
                    <?php foreach (['standar' => 'Standar', 'ac' => 'AC', 'premium' => 'Premium'] as $nilai => $label): ?>
                        <option value="<?= esc($nilai) ?>" <?= $tipe === $nilai ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label small text-muted mb-1">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua status</option>
                    <?php foreach (['kosong' => 'Kosong', 'terisi' => 'Terisi', 'perbaikan' => 'Perbaikan'] as $nilai => $label): ?>
                        <option value="<?= esc($nilai) ?>" <?= $status === $nilai ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Cari</button>
            </div>
        </form>

        <div class="row g-4">
            <?php if ($kamar === []): ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Tidak ada kamar yang cocok dengan pencarian Anda. Coba kata kunci atau filter lain.
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($kamar as $k): ?>
                <?= view('partials/kartu_kamar', ['kamar' => $k]) ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
