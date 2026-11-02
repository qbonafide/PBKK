# Log Pengerjaan Tugas PBKK - CodeIgniter 4

Catatan kronologis setiap perintah penting, error, dan solusinya. Dokumen ini menjadi
bahan utama untuk bagian **Langkah Instalasi** pada `README.md`.

---

## Fase 0 - Persiapan Environment

### Depedency Check

```bash
php -v
composer -V
node -v
npm -v
mysql --version
git --version
```

### Membuat database

```bash
mysql -u root -p -e "
CREATE DATABASE ci4_kosku    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE ci4_siakad   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE ci4_thriftin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Notes

Laragon dipakai karena menyediakan PHP, MySQL, Composer, dan Node dalam satu folder
(`C:\laragon`) sehingga tidak perlu mengatur `PATH` manual seperti pada XAMPP.
Halaman web dijalankan lewat `php spark serve` pada port berbeda
(8081/8082/8083), bukan lewat virtual host Apache, supaya ketiga aplikasi dapat
hidup bersamaan tanpa konfigurasi `hosts` tambahan.

---

## Fase 1 - Instalasi & Konfigurasi CodeIgniter 4

### Membuat tiga project

```bash
cd C:\laragon\www\pbkk-codeigniter
composer create-project codeigniter4/appstarter latihan1-kosku
composer create-project codeigniter4/appstarter latihan2a-siakad
composer create-project codeigniter4/appstarter latihan2b-thriftin
```

Versi framework yang terpasang (dicek dengan `php spark --version`):

```
CodeIgniter v4.7.6 Command Line Tool
```

### Konfigurasi `.env`

Setiap project memakai database sendiri dan port sendiri, karena ketiganya
dijalankan bersamaan.

| Project | Port | Database |
|---|---|---|
| latihan1-kosku | 8081 | ci4_kosku |
| latihan2a-siakad | 8082 | ci4_siakad |
| latihan2b-thriftin | 8083 | ci4_thriftin |

Isi `.env`:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8081/'
app.indexPage = ''
app.appTimezone = 'Asia/Jakarta'
app.defaultLocale = 'id'

database.default.hostname = localhost
database.default.database = ci4_kosku
database.default.username = root
database.default.password = ******
database.default.DBDriver = MySQLi
database.default.port = 3306
```

`app.indexPage` dikosongkan supaya `index.php` tidak muncul di URL.
`appTimezone` dan `defaultLocale` juga ditulis di `.env`, jadi tidak perlu
mengubah `app/Config/App.php` lagi (nilai di `.env` menimpa nilai config).

### Konfigurasi tambahan

- CSRF diaktifkan global: di `app/Config/Filters.php`, baris `// 'csrf',` pada
  `$globals['before']` dibuka komentarnya.
- Helper global ditambahkan di `app/Controllers/BaseController.php`:
  `$this->helpers = ['form', 'url', 'text', 'berkas'];`
- Debug toolbar dimatikan (baris `'toolbar'` pada `$required['after']`
  dikomentari) karena toolbar menempel di bawah halaman dan ikut terfoto pada
  screenshot. Pesan error tetap lengkap karena environment masih `development`.

### Aset tampilan disiapkan lokal

SB Admin 2 diunduh lewat npm lalu disalin ke `public/assets/sbadmin/` pada
ketiga project:

```bash
cd scripts
npm pack startbootstrap-sb-admin-2     # menghasilkan startbootstrap-sb-admin-2-4.1.4.tgz
tar -xf startbootstrap-sb-admin-2-4.1.4.tgz
```

Bootstrap 5.3.3 untuk halaman publik diunduh sekali lalu disimpan lokal di
`public/assets/bootstrap/` (tidak memakai CDN agar aplikasi tetap tampil normal
tanpa internet).

### Verifikasi instalasi

```bash
php spark serve --port 8081
```

Halaman `Welcome to CodeIgniter 4!` tampil (HTTP 200) dan koneksi database
terbukti lewat `php spark db:table --show` yang menampilkan `ci4_kosku`.

---

## Fase 2 - Aplikasi KosKu (Latihan 1)

### Migrasi & seeder

Empat migration dibuat berurutan: `users`, `kamar`, `penghuni`, `pembayaran`.
Relasi foreign key: `penghuni.user_id -> users.id`, `penghuni.kamar_id -> kamar.id`,
`pembayaran.penghuni_id -> penghuni.id`. Kombinasi unik
`(penghuni_id, bulan, tahun)` mencegah tagihan ganda.

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Hasil: 8 user, 9 kamar (6 terisi, 2 kosong, 1 perbaikan), 6 penghuni,
18 tagihan (3 bulan terakhir dengan status bervariasi).


### Verifikasi alur

Diuji dengan server `php spark serve --port 8081`:

- halaman publik `/`, `/kamar`, `/kamar/kamar-a1`, `/tentang`, `/kontak` -> 200,
- `/kamar/tidak-ada` -> 404 dengan halaman kustom,
- login admin -> `/admin/dashboard`,
- login penghuni -> `/penghuni/dashboard`,
- penghuni membuka `/admin/users` -> **403** (halaman akses ditolak),
- unggah bukti transfer mengubah status tagihan menjadi `menunggu_verifikasi`,
- verifikasi admin mengubah status menjadi `lunas` beserta tanggal bayar,
- foto KTP & bukti transfer hanya bisa dibuka admin atau pemiliknya.

---

## Fase 3 - Aplikasi SIAKAD (Latihan 2a)

### Skema & migrasi

Enam migration dibuat berurutan: `users`, `prodi`, `dosen`, `mahasiswa`,
`matakuliah`, `krs`. Kombinasi unik `(mahasiswa_id, matakuliah_id, tahun_ajaran)`
pada tabel `krs` mencegah satu mata kuliah diambil dua kali di tahun ajaran yang
sama.

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Hasil seed: 26 user (1 admin, 5 dosen, 20 mahasiswa), 3 prodi, 12 mata kuliah,
65 baris KRS.


### Verifikasi alur

- login tiga role berhasil dan diarahkan ke dashboard masing-masing,
- dosen membuka kelas dosen lain -> **403**,
- input nilai 95 pada 12 mahasiswa -> tersimpan sebagai huruf `A` bobot `4.00`,
- KRS melebihi batas -> ditolak: "Total SKS menjadi 29 SKS, melebihi batas maksimal 24 SKS.",
- KRS mata kuliah yang sudah diambil -> ditolak: "sudah diambil pada tahun ajaran ini",
- KRS kuota penuh -> ditolak: "Kuota mata kuliah ... sudah penuh (6/1).",
- KHS: IP semester 3.80 dan 3.85, IPK kumulatif **3.83**
  (total mutu 76.5 / 20 SKS, dihitung manual dan hasilnya sama).

---

## Fase 4 - Aplikasi ThriftIn (Latihan 2b)

### Skema & migrasi

Lima migration: `users`, `kategori`, `produk`, `orders`, `order_items`.
`order_items` menyimpan snapshot `nama_produk` dan `harga`, sehingga riwayat
pesanan tetap benar walau produk berubah harga atau dihapus.

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Hasil seed: 3 user demo, 6 kategori, 26 produk, 7 pesanan dengan enam status
berbeda, 9 baris item pesanan.

### Catatan: qty keranjang menyesuaikan stok

`hitung_keranjang()` selalu membaca ulang stok dari database. Bila stok berubah
turun setelah produk masuk keranjang, qty otomatis dipotong (dan item dihapus
bila stok menjadi 0). Ini yang membuat pembeli tidak bisa memesan melebihi stok,
sementara query bersyarat di checkout tetap menjadi pengaman terakhir.

```php
$db->table('produk')
   ->set('stok', 'stok - ' . (int) $qty, false)
   ->where('id', $id)->where('stok >=', (int) $qty)->update();

if ($db->affectedRows() !== 1) {
    throw new \RuntimeException('Stok tidak mencukupi');
}
```

### Verifikasi alur

- katalog + filter kategori/kondisi/urut harga + pagination berjalan,
- tambah keranjang -> checkout membuat invoice `TI-20261102-0004`,
  subtotal Rp 110.000 + ongkir Rp 20.000 = Rp 130.000, stok produk 1 -> 0,
- unggah bukti transfer -> status menjadi `menunggu_verifikasi`,
- pembeli lain membuka pesanan/bukti tersebut -> **403**,
- admin mengubah `menunggu_verifikasi` -> `diproses` berhasil,
- admin mencoba `diproses` -> `selesai` ditolak:
  "Perubahan status dari Diproses ke Selesai tidak diizinkan.",
- pembatalan pesanan mengembalikan stok (0 -> 1),
- `php spark order:expire` membatalkan 1 pesanan kedaluwarsa dan mengembalikan
  stok (0 -> 2),
- query stok bersyarat saat stok 0 dan qty 5 menghasilkan `affectedRows = 0`
  dan stok tetap 0 (tidak minus).

---
