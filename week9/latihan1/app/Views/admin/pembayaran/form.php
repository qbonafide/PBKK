<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$edit   = $bayar !== null;
$action = $edit ? site_url('admin/pembayaran/' . $bayar['id']) : site_url('admin/pembayaran');
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?= $edit ? 'Edit tagihan' : 'Tambah tagihan baru' ?>
                </h6>
            </div>
            <div class="card-body">

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger">
                        <strong>Periksa kembali isian berikut:</strong>
                        <ul class="mb-0 mt-1 small">
                            <?php foreach ($errors as $pesan): ?>
                                <li><?= esc($pesan) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= $action ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="penghuni_id">Penghuni <span class="text-danger">*</span></label>
                        <select class="form-control" id="penghuni_id" name="penghuni_id" required>
                            <option value="">-- pilih penghuni --</option>
                            <?php foreach ($penghuni as $p): ?>
                                <option value="<?= esc($p['id']) ?>"
                                    <?= (int) old('penghuni_id', $bayar['penghuni_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['nama']) ?> - Kamar <?= esc($p['nomor_kamar']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="bulan">Bulan <span class="text-danger">*</span></label>
                            <select class="form-control" id="bulan" name="bulan" required>
                                <?php
                                $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $bulanTerpilih = (int) old('bulan', $bayar['bulan'] ?? date('n'));
                                foreach ($namaBulan as $i => $nama): ?>
                                    <option value="<?= $i + 1 ?>" <?= $bulanTerpilih === $i + 1 ? 'selected' : '' ?>>
                                        <?= esc($nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tahun">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tahun" name="tahun" min="2020" max="2100"
                                   value="<?= esc(old('tahun', $bayar['tahun'] ?? date('Y'))) ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="jumlah">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" step="1000"
                                   value="<?= esc(old('jumlah', $bayar['jumlah'] ?? 750000)) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status">
                            <?php
                            $daftarStatus = [
                                'belum_bayar'         => 'Belum bayar',
                                'menunggu_verifikasi' => 'Menunggu verifikasi',
                                'lunas'               => 'Lunas',
                                'ditolak'             => 'Ditolak',
                            ];
                            $statusTerpilih = old('status', $bayar['status'] ?? 'belum_bayar');
                            foreach ($daftarStatus as $nilai => $label): ?>
                                <option value="<?= esc($nilai) ?>" <?= $statusTerpilih === $nilai ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2"
                                  placeholder="Opsional, misal alasan penolakan"><?= esc(old('catatan', $bayar['catatan'] ?? '')) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save fa-sm mr-1"></i> <?= $edit ? 'Simpan perubahan' : 'Simpan tagihan' ?>
                    </button>
                    <a href="<?= site_url('admin/pembayaran') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
