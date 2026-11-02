<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak KHS &middot; <?= esc($mhs['nim']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111;
            background: #fff;
            margin: 0;
            padding: 32px;
            font-size: 13px;
        }
        .kop {
            text-align: center;
            border-bottom: 3px double #111;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .kop h1 { font-size: 17px; margin: 0 0 4px; letter-spacing: .5px; }
        .kop p { margin: 0; font-size: 12px; }
        h2 { font-size: 14px; text-align: center; margin: 18px 0 14px; text-decoration: underline; }
        h3 { font-size: 13px; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #444; padding: 5px 7px; }
        th { background: #f0f0f0; font-size: 12px; }
        .identitas { border: 0; margin-bottom: 14px; }
        .identitas td { border: 0; padding: 2px 0; }
        .identitas td:first-child { width: 130px; }
        .ringkas { margin: 10px 0 0; font-size: 13px; }
        .ringkas strong { font-size: 14px; }
        .ttd { margin-top: 34px; }
        .ttd td { border: 0; text-align: center; }
        .garis-ttd { height: 58px; }
        .cetak { text-align: right; margin-bottom: 15px; }
        .cetak button {
            padding: 7px 16px; font-size: 13px; cursor: pointer;
            border: 1px solid #333; background: #f5f5f5;
        }
        @media print {
            body { padding: 0; }
            .cetak { display: none; }
            @page { margin: 18mm; }
        }
    </style>
</head>
<body>

<div class="cetak">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="kop">
    <h1>UNIVERSITAS CONTOH NUSANTARA</h1>
    <p>Fakultas Teknologi Informasi &middot; Jl. Pendidikan No. 1, Yogyakarta</p>
</div>

<h2>KARTU HASIL STUDI (KHS)</h2>

<table class="identitas">
    <tr><td>Nama</td><td>: <?= esc($mhs['nama']) ?></td></tr>
    <tr><td>NIM</td><td>: <?= esc($mhs['nim']) ?></td></tr>
</table>

<?php if ($rekap === []): ?>
    <p>Belum ada data nilai.</p>
<?php endif; ?>

<?php foreach ($rekap as $semester): ?>
    <h3>Semester <?= esc($semester['semester']) ?></h3>

    <table>
        <thead>
            <tr>
                <th style="width: 34px;">No</th>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th style="width: 44px;">SKS</th>
                <th style="width: 60px;">Nilai</th>
                <th style="width: 55px;">Huruf</th>
                <th style="width: 55px;">Bobot</th>
                <th style="width: 70px;">Mutu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($semester['baris'] as $i => $b): ?>
                <tr>
                    <td style="text-align: center;"><?= $i + 1 ?></td>
                    <td><?= esc($b['kode']) ?></td>
                    <td><?= esc($b['nama']) ?></td>
                    <td style="text-align: center;"><?= esc($b['sks']) ?></td>
                    <td style="text-align: center;"><?= $b['nilai_angka'] === null ? '-' : esc($b['nilai_angka']) ?></td>
                    <td style="text-align: center;"><?= esc($b['nilai_huruf'] ?? '-') ?></td>
                    <td style="text-align: center;"><?= $b['bobot'] === null ? '-' : esc($b['bobot']) ?></td>
                    <td style="text-align: center;"><?= $b['bobot'] === null ? '-' : number_format((float) $b['mutu'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">Jumlah</th>
                <th style="text-align: center;"><?= esc($semester['total_sks']) ?></th>
                <th colspan="2"></th>
                <th></th>
                <th style="text-align: center;"><?= number_format((float) $semester['total_mutu'], 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <p class="ringkas">
        <?php if ($semester['total_sks'] > 0): ?>
            IP Semester: <?= number_format((float) $semester['total_mutu'], 2) ?> /
            <?= esc($semester['total_sks']) ?> SKS =
            <strong><?= number_format((float) $semester['ip'], 2) ?></strong>
        <?php else: ?>
            IP Semester: <em>belum ada nilai</em>
        <?php endif; ?>
    </p>
<?php endforeach; ?>

<p class="ringkas">
    IPK Kumulatif: <strong><?= number_format((float) $ipk['ipk'], 2) ?></strong>
    (<?= esc($ipk['sks']) ?> SKS dinilai)
</p>

<table class="ttd">
    <tr>
        <td>Wali Akademik,<div class="garis-ttd"></div>....................................</td>
        <td>Yogyakarta, <?= date('d-m-Y') ?><br>Bagian Akademik,<div class="garis-ttd"></div>....................................</td>
    </tr>
</table>

</body>
</html>
