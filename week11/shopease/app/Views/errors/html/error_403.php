<?php
/**
 * Halaman 403 kustom (dipakai AuthFilter dan BaseController::forbidden).
 * Sengaja mandiri: tidak memakai layout utama agar tetap tampil
 * walaupun error terjadi sebelum data halaman siap.
 */
$requestedPath = (string) ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Akses Ditolak') ?> &middot; ShopEase</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem;
            font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            color: #0f172a;
            background: radial-gradient(120% 120% at 100% 0%, #e8ecff 0%, #f6f8fb 45%, #ffffff 100%);
        }
        .card {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 2.6rem 2.2rem;
            box-shadow: 0 30px 60px -40px rgba(15, 23, 42, 0.5);
            text-align: center;
        }
        .code {
            font-size: 3.4rem;
            font-weight: 700;
            letter-spacing: -0.04em;
            color: #c0392b;
            line-height: 1;
            margin-bottom: 0.75rem;
        }
        h1 { font-size: 1.35rem; margin: 0 0 0.6rem; }
        p { color: #4b5b70; margin: 0 0 1.5rem; line-height: 1.6; }
        .path {
            display: inline-block;
            background: #f6f8fb;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.45rem 0.75rem;
            font-family: Consolas, "Courier New", monospace;
            font-size: 0.82rem;
            color: #4b5b70;
            word-break: break-all;
            margin-bottom: 1.5rem;
        }
        .actions { display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; }
        a.btn {
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.6rem 1.25rem;
            border-radius: 999px;
            border: 1px solid #2f4bd8;
            background: #2f4bd8;
            color: #fff;
        }
        a.btn.ghost { background: #fff; color: #2f4bd8; }
    </style>
</head>
<body>
    <main class="card">
        <div class="code">403</div>
        <h1><?= esc($title ?? 'Akses Ditolak') ?></h1>
        <p><?= esc($message ?? 'Role akun Anda tidak memiliki izin untuk membuka halaman ini.') ?></p>
        <div class="path"><?= esc($requestedPath) ?></div>
        <div class="actions">
            <a class="btn" href="<?= site_url('/') ?>">Kembali ke beranda</a>
            <a class="btn ghost" href="<?= site_url('login') ?>">Masuk dengan akun lain</a>
        </div>
    </main>
</body>
</html>
