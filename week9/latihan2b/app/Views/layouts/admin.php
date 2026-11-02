<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Admin') ?> &middot; ThriftIn</title>

    <link href="<?= base_url('assets/sbadmin/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/sbadmin/css/sb-admin-2.min.css') ?>" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <?= view('partials/sidebar') ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?= view('partials/topbar') ?>

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <h1 class="h3 mb-0 text-gray-800"><?= esc($title ?? 'Dashboard') ?></h1>
                    <span class="text-muted small"><?= date('d F Y') ?></span>
                </div>

                <?php if (session()->getFlashdata('sukses')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('sukses')) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>&copy; <?= date('Y') ?> ThriftIn &middot; CodeIgniter 4 + SB Admin 2</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="<?= base_url('assets/sbadmin/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
<script src="<?= base_url('assets/sbadmin/js/sb-admin-2.min.js') ?>"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
