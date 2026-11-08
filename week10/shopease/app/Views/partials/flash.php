<?php
/**
 * Flash message + daftar error validasi.
 * Semua pesan di-escape karena sebagian berasal dari input pengguna.
 */
$messages = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'info'    => 'alert-info',
    'warning' => 'alert-warning',
];

$hasFlash = false;
foreach (array_keys($messages) as $key) {
    if (session()->getFlashdata($key) !== null) {
        $hasFlash = true;
        break;
    }
}

$validationErrors = session()->getFlashdata('errors') ?? [];
?>
<?php if ($hasFlash || $validationErrors !== []) : ?>
    <div class="container mt-4">
        <?php foreach ($messages as $key => $class) : ?>
            <?php $message = session()->getFlashdata($key); ?>
            <?php if ($message !== null) : ?>
                <div class="alert <?= $class ?> alert-dismissible fade show" role="alert">
                    <?= esc($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($validationErrors !== []) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong class="d-block mb-1">Periksa kembali isian berikut:</strong>
                <ul class="mb-0 ps-3">
                    <?php foreach ($validationErrors as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
