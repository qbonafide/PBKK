<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak KRS &middot; <?= esc($mhs['nim']) ?></title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #444; padding: 5px 7px; }
        th { background: #f0f0f0; font-size: 12px; }
        .identitas { width: 100%; border: 0; margin-bottom: 14px; }
        .identitas td { border: 0; padding: 2px 0; }
        .identitas td:first-child { width: 120px; }
        .ttd { margin-top: 36px; width: 100%; }
        .ttd td { border: 0; text-align: center; padding-top: 8px; }
        .garis-ttd { height: 60px; }
        .cetak {
            text-align: right;
            margin-bottom: 15px;
        }
        .cetak button {
            padding: 7px 16px;
            font-size: 13px;
            cursor: pointer;
            border: 1px solid #333;
            background: #f5f5f5;
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

<h2>KARTU RENCANA STUDI (KRS)</h2>

<table class="identitas">
    <tr><td>Nama</td><td>: <?= esc($mhs['nama']) ?></td></tr>
    <tr><td>NIM</td><td>: <?= esc($mhs['nim']) ?></td></tr>
    <tr><td>Tahun Ajaran</td><td>: <?= esc($tahunAjaran) ?></td></tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 34px;">No</th>
            <th>Kode</th>
            <th>Mata Kuliah</th>
            <th style="width: 44px;">SKS</th>
            <th style="width: 60px;">Semester</th>
            <th>Dosen</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($baris === []): ?>
            <tr><td colspan="6" style="text-align: center;">Belum ada mata kuliah yang diambil.</td></tr>
        <?php endif; ?>

        <?php foreach ($baris as $i => $b): ?>
            <tr>
                <td style="text-align: center;"><?= $i + 1 ?></td>
                <td><?= esc($b['kode']) ?></td>
                <td><?= esc($b['nama']) ?></td>
                <td style="text-align: center;"><?= esc($b['sks']) ?></td>
                <td style="text-align: center;"><?= esc($b['semester']) ?></td>
                <td><?= esc($b['nama_dosen'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right;">Total SKS</th>
            <th style="text-align: center;"><?= esc($totalSks) ?></th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>

<table class="ttd">
    <tr>
        <td>Mahasiswa,<div class="garis-ttd"></div><?= esc($mhs['nama']) ?></td>
        <td>Dosen Wali,<div class="garis-ttd"></div>....................................</td>
    </tr>
</table>

<p style="font-size: 11px; margin-top: 22px;">
    Dicetak dari aplikasi SIAKAD pada <?= date('d-m-Y H:i') ?> WIB.
</p>

</body>
</html>
