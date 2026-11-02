<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$labelStatus = [
    'belum_bayar'          => ['teks' => 'Belum bayar', 'kelas' => 'secondary'],
    'menunggu_verifikasi'  => ['teks' => 'Menunggu verifikasi', 'kelas' => 'warning'],
    'lunas'                => ['teks' => 'Lunas', 'kelas' => 'success'],
    'ditolak'              => ['teks' => 'Ditolak', 'kelas' => 'danger'],
];
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar tagihan &amp; pembayaran</h6>
        <a href="<?= site_url('admin/pembayaran/create') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah tagihan
        </a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/pembayaran') ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control form-control-sm mr-2 mb-2"
                   placeholder="Cari nama penghuni" value="<?= esc($keyword) ?>">
            <select name="status" class="form-control form-control-sm mr-2 mb-2">
                <option value="">Semua status</option>
                <?php foreach ($labelStatus as $nilai => $info): ?>
                    <option value="<?= esc($nilai) ?>" <?= $status === $nilai ? 'selected' : '' ?>>
                        <?= esc($info['teks']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">Filter</button>
            <?php if ($keyword !== '' || $status !== ''): ?>
                <a href="<?= site_url('admin/pembayaran') ?>" class="btn btn-sm btn-link mb-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Penghuni</th>
                        <th>Kamar</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bayar === []): ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada tagihan yang cocok.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($bayar as $b): ?>
                        <?php $info = $labelStatus[$b['status']] ?? ['teks' => $b['status'], 'kelas' => 'secondary']; ?>
                        <tr>
                            <td><strong><?= esc($b['nama_penghuni'] ?? '-') ?></strong></td>
                            <td><?= esc($b['nomor_kamar'] ?? '-') ?></td>
                            <td><?= esc($b['bulan']) ?>/<?= esc($b['tahun']) ?></td>
                            <td>Rp <?= number_format((int) $b['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($b['bukti_transfer'] !== null): ?>
                                    <a href="<?= site_url('berkas/bukti/' . $b['bukti_transfer']) ?>" target="_blank"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-receipt"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= esc($info['kelas']) ?>"><?= esc($info['teks']) ?></span>
                                <?php if ($b['catatan'] !== null && $b['catatan'] !== ''): ?>
                                    <div class="small text-muted mt-1"><?= esc($b['catatan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <?php if ($b['status'] === 'menunggu_verifikasi'): ?>
                                    <form action="<?= site_url('admin/pembayaran/' . $b['id'] . '/verifikasi') ?>"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Tandai pembayaran ini lunas?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="Verifikasi">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger btn-tolak"
                                            data-toggle="modal" data-target="#modalTolak"
                                            data-id="<?= esc($b['id']) ?>"
                                            data-nama="<?= esc($b['nama_penghuni'] ?? '-') ?>"
                                            title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>

                                <a href="<?= site_url('admin/pembayaran/' . $b['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= site_url('admin/pembayaran/' . $b['id'] . '/delete') ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('Hapus tagihan periode ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-secondary" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= $pager->links() ?>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="judulModalTolak" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" id="formTolak" action="#">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModalTolak">Tolak bukti pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Penghuni: <strong id="namaPenghuniTolak">-</strong></p>
                    <div class="form-group mb-0">
                        <label for="catatanTolak">Alasan penolakan</label>
                        <textarea class="form-control" id="catatanTolak" name="catatan" rows="3"
                                  placeholder="Contoh: nominal transfer tidak sesuai tagihan."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak pembayaran</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Isi modal penolakan sesuai baris yang diklik.
    var tombolTolak = document.querySelectorAll('.btn-tolak');
    tombolTolak.forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            document.getElementById('formTolak').action =
                '<?= site_url('admin/pembayaran') ?>/' + tombol.dataset.id + '/tolak';
            document.getElementById('namaPenghuniTolak').textContent = tombol.dataset.nama;
        });
    });
</script>
<?= $this->endSection() ?>
