# ShopEase

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.7.6-dd4814)
![PHP](https://img.shields.io/badge/PHP-8.4-777bb4)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952b3)

---

## 1. Identitas Proyek

| Item | Keterangan |
|---|---|
| Nama | **Christina Tan** |
| NRP | **5025241060** |
| Kelas | **PBKK D** |
| Framework | CodeIgniter 4.7.6 (PHP 8.4) |
| Database | MySQL |


---

## 2. Sekilas ShopEase

ShopEase adalah aplikasi toko online sederhana. Pembeli membuka katalog, mencari
produk, memasukkan barang ke keranjang, lalu checkout. Setelah pesanan dibuat,
pembeli mentransfer dan mengunggah bukti pembayaran. Admin memeriksa bukti
tersebut, operator menyiapkan barang dan mengisi nomor resi, dan pembeli memantau
status pesanan sampai selesai.

Ada tiga peran dengan hak akses berbeda:

| Peran | Yang dapat dilakukan |
|---|---|
| Customer | Registrasi, login, melihat katalog, mengelola keranjang, checkout, unggah bukti pembayaran, melihat riwayat pesanan |
| Admin | Mengelola kategori, produk, pesanan, verifikasi pembayaran, dan laporan penjualan |
| Operator | Melihat dan memproses pesanan, mengisi data pengiriman |

---

## 3. Cara Menjalankan

### Langkah menjalankan

```bash
composer install
```

isi .env:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_shopease
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Buat database, jalankan migration, lalu isi data awal:

```bash
mysql -u root -p -e "CREATE DATABASE db_shopease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve
```

---

## 4. Penjelasan

| Layer | Berkas | Tugas |
|---|---|---|
| Routes | `app/Config/Routes.php` | Memetakan URL ke controller, menempelkan filter per peran, memberi nama route |
| Filter | `app/Filters/AuthFilter.php`, `app/Config/Filters.php` | Memeriksa login dan peran sebelum controller dijalankan |
| Controller | `app/Controllers/ProductController.php` | Membaca input, memanggil model, memilih view, mengirim data |
| Model | `app/Models/ProductModel.php` | Menyusun query, pagination, dan aturan data |
| View | `app/Views/frontend/catalog.php` | Menampilkan data, tanpa logika bisnis |

---

## 5. Struktur Project

```text
shopease/
├── app/
│   ├── Config/
│   │   ├── Routes.php        map route seluruh URL
│   │   ├── Filters.php       filter + CSRF global
│   │   └── Pager.php         template pagination Bootstrap
│   ├── Controllers/
│   │   ├── HomeController.php         beranda, about, contact
│   │   ├── AuthController.php         login, register, logout
│   │   ├── ProductController.php      katalog + detail
│   │   ├── CartController.php         keranjang
│   │   ├── CheckoutController.php     checkout + transaksi
│   │   ├── OrderController.php        riwayat, detail, unggah bukti
│   │   └── Admin/                     tujuh controller area admin
│   ├── Filters/
│   │   ├── AuthFilter.php    login + peran, 403 bila ditolak
│   │   └── GuestFilter.php   halaman login hanya untuk tamu
│   ├── Models/               sepuluh model, satu tabel per model
│   ├── Views/
│   │   ├── layouts/          main.php (publik), admin.php (sidebar)
│   │   ├── partials/         navbar, flash, footer, product_card, pager
│   │   ├── frontend/         home, about, contact, catalog, detail
│   │   ├── auth/             login, register
│   │   ├── cart/, checkout/, orders/
│   │   ├── admin/            dashboard, categories, products, orders, payments, reports
│   │   └── errors/html/      error_403.php, error_404.php
│   ├── Helpers/shopease_helper.php   rupiah, label status, badge, URL gambar
│   └── Database/{Migrations,Seeds}
├── database/db_shopease.sql   hasil ekspor database
├── public/
    ├── assets/               bootstrap, chart.js, app.css (lokal)
    └── uploads/products/     gambar produk

```

Alasan pemisahan:

- **Controller per tanggung jawab**, bukan satu controller besar. Keranjang dan
  checkout dipisah karena checkout punya transaksi database sendiri.
- **Folder `Admin/`** memisahkan area pengelolaan dari halaman pembeli, sehingga
  filter per peran bisa diterapkan sekali di level grup route.
- **Helper** menampung format yang dipakai berulang (Rupiah, label status) agar
  tidak disalin di belasan view.
- **Aset lokal** di `public/assets` supaya tampilan tidak bergantung pada CDN.

---

## 6. Routing

### Prinsip desain

3 rules yang dipakai:

1. **Grouping + filter per peran.** Otorisasi diletakkan di route, bukan di view.
   Satu grup `admin` dilindungi `auth:admin,operator`, grup lain `auth:admin`,
   dan pembeli `auth:customer`.
2. **Named route.** Setiap route punya nama lewat opsi `as`. View memakai
   `site_url()`, tidak ada URL yang ditulis manual di template.
3. **Aksi ubah data hanya POST.** Hapus kategori, hapus produk, ubah status,
   approve/reject pembayaran, simpan resi, dan logout semuanya `post()`. Tidak ada
   `get()` yang mengubah data, sehingga aksi tidak bisa terpicu oleh tautan,
   prefetch browser, atau crawler.

### Map routing aplikasi

| Method | URL | Controller::method | Filter / hak akses | Hasil |
|---|---|---|---|---|
| GET | `/` | `HomeController::index` | – | Beranda: kategori, produk terbaru, terlaris |
| GET | `/about` | `HomeController::about` | – | Halaman tentang |
| GET | `/contact` | `HomeController::contact` | – | Halaman kontak |
| GET | `/catalog` | `ProductController::catalog` | – | Katalog + pencarian + filter + pagination |
| GET | `/product/{id}` | `ProductController::detail` | – | Detail produk aktif; nonaktif → 404 |
| GET | `/login` | `AuthController::login` | `guest` | Formulir masuk |
| POST | `/login` | `AuthController::attemptLogin` | `guest` | Proses login |
| GET | `/register` | `AuthController::register` | `guest` | Formulir daftar |
| POST | `/register` | `AuthController::attemptRegister` | `guest` | Proses daftar |
| POST | `/logout` | `AuthController::logout` | `auth` | Hapus session |
| GET | `/cart` | `CartController::index` | `auth:customer` | Daftar item keranjang |
| POST | `/cart/add` | `CartController::add` | `auth:customer` | Tambah item |
| POST | `/cart/update` | `CartController::update` | `auth:customer` | Ubah kuantitas |
| POST | `/cart/remove/{id}` | `CartController::remove` | `auth:customer` | Hapus item |
| GET | `/checkout` | `CheckoutController::index` | `auth:customer` | Ringkasan sebelum konfirmasi |
| POST | `/checkout/address` | `CheckoutController::storeAddress` | `auth:customer` | Simpan alamat baru |
| POST | `/checkout/process` | `CheckoutController::process` | `auth:customer` | Buat pesanan dalam transaksi |
| GET | `/orders` | `OrderController::index` | `auth:customer` | Riwayat pesanan sendiri |
| GET | `/orders/{id}` | `OrderController::detail` | `auth:customer` | Detail; milik orang lain → 403 |
| POST | `/orders/{id}/payment` | `OrderController::uploadProof` | `auth:customer` | Unggah bukti pembayaran |
| GET | `/admin/dashboard` | `Admin\DashboardController::index` | `auth:admin,operator` | Statistik + grafik |
| GET | `/admin/orders` | `Admin\OrderController::index` | `auth:admin,operator` | Daftar pesanan |
| GET | `/admin/orders/{id}` | `Admin\OrderController::detail` | `auth:admin,operator` | Detail pesanan |
| POST | `/admin/orders/{id}/status` | `Admin\OrderController::updateStatus` | `auth:admin,operator` | Ubah status (validasi transisi) |
| POST | `/admin/shipments/{id}` | `Admin\ShipmentController::store` | `auth:admin,operator` | Simpan kurir + resi |
| GET | `/admin/categories` | `Admin\CategoryController::index` | `auth:admin` | Daftar kategori |
| GET | `/admin/categories/create` | `Admin\CategoryController::create` | `auth:admin` | Formulir tambah |
| POST | `/admin/categories` | `Admin\CategoryController::store` | `auth:admin` | Simpan kategori |
| GET | `/admin/categories/{id}/edit` | `Admin\CategoryController::edit` | `auth:admin` | Formulir ubah |
| POST | `/admin/categories/{id}` | `Admin\CategoryController::update` | `auth:admin` | Simpan perubahan |
| POST | `/admin/categories/{id}/delete` | `Admin\CategoryController::delete` | `auth:admin` | Hapus (ditolak jika ada produk) |
| GET | `/admin/products` | `Admin\ProductController::index` | `auth:admin` | Daftar produk + pencarian |
| GET | `/admin/products/create` | `Admin\ProductController::create` | `auth:admin` | Formulir tambah |
| POST | `/admin/products` | `Admin\ProductController::store` | `auth:admin` | Simpan + unggah gambar |
| GET | `/admin/products/{id}/edit` | `Admin\ProductController::edit` | `auth:admin` | Formulir ubah |
| POST | `/admin/products/{id}` | `Admin\ProductController::update` | `auth:admin` | Simpan perubahan |
| POST | `/admin/products/{id}/delete` | `Admin\ProductController::delete` | `auth:admin` | Hapus / nonaktifkan |
| GET | `/admin/payments` | `Admin\PaymentController::index` | `auth:admin` | Antrean verifikasi |
| POST | `/admin/payments/{id}/approve` | `Admin\PaymentController::approve` | `auth:admin` | Setujui pembayaran |
| POST | `/admin/payments/{id}/reject` | `Admin\PaymentController::reject` | `auth:admin` | Tolak + alasan |
| GET | `/admin/reports` | `Admin\ReportController::index` | `auth:admin` | Laporan periode + status |

### `Routes.php`

```php
$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin', ['as' => 'login.attempt']);
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister', ['as' => 'register.attempt']);
});

$routes->post('logout', 'AuthController::logout', ['as' => 'logout', 'filter' => 'auth']);

$routes->group('', ['filter' => 'auth:customer'], static function (RouteCollection $routes): void {
    $routes->get('cart', 'CartController::index', ['as' => 'cart.index']);
    $routes->post('cart/add', 'CartController::add', ['as' => 'cart.add']);
    $routes->post('cart/remove/(:num)', 'CartController::remove/$1', ['as' => 'cart.remove']);
    // ...
});

$routes->group('admin', ['filter' => 'auth:admin,operator'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'Admin\DashboardController::index', ['as' => 'admin.dashboard']);
    $routes->post('shipments/(:num)', 'Admin\ShipmentController::store/$1', ['as' => 'admin.shipments.store']);
});
```

Penjelasan:

- `auth:admin,operator`: argumen filter dipisah koma, dibaca sebagai daftar peran
  yang diizinkan. Operator cukup diberi akses ke pesanan dan pengiriman, bukan
  katalog.
- `(:num)` pada `cart/remove/(:num)`: Membatasi segmen menjadi angka. Nilai
  ditangkap sebagai `$1` dan diteruskan ke parameter method.
- `['as' => 'cart.index']`: Memberi nama route. Kalau URL diubah, view tidak perlu
  disentuh karena memakai `site_url('cart')`.
- Grup `admin` dipisah 2: satu untuk admin+operator, satu hanya admin.

## 7. Controller

### Map controller aplikasi

| Kelompok | Controller | Method |
|---|---|---|
| Publik | `HomeController` | `index`, `about`, `contact` |
| Publik | `ProductController` | `catalog`, `detail` |
| Auth | `AuthController` | `login`, `attemptLogin`, `register`, `attemptRegister`, `logout` |
| Customer | `CartController` | `index`, `add`, `update`, `remove` |
| Customer | `CheckoutController` | `index`, `storeAddress`, `process` |
| Customer | `OrderController` | `index`, `detail`, `uploadProof` |
| Admin | `Admin\DashboardController` | `index` |
| Admin | `Admin\CategoryController` | `index`, `create`, `store`, `edit`, `update`, `delete` |
| Admin | `Admin\ProductController` | `index`, `create`, `store`, `edit`, `update`, `delete` |
| Admin | `Admin\OrderController` | `index`, `detail`, `updateStatus` |
| Admin | `Admin\PaymentController` | `index`, `approve`, `reject` |
| Admin | `Admin\ShipmentController` | `store` |
| Admin | `Admin\ReportController` | `index` |

Notes: Validasi input di controller, tetapi query dan aturan data ada di model. Contohnya `CartController::add` tidak tahu tabel apa pun; ia memanggil `ProductModel::findActive()` dan
`CartItemModel::findProductInCart()`.

### Keranjang: validasi stok dan penggabungan item

```php
public function add()
{
    $productId = (int) $this->request->getPost('product_id');
    $quantity  = (int) $this->request->getPost('quantity');

    if ($quantity < 1) {
        return redirect()->back()->with('error', 'Jumlah produk minimal 1.');
    }

    $product = (new ProductModel())->findActive($productId);

    if ($product === null) {
        return redirect()->back()->with('error', 'Produk tidak tersedia atau sudah tidak dijual.');
    }

    if ((int) $product['stock'] < $quantity) {
        return redirect()->back()->with('error', 'Stok tidak mencukupi. Sisa stok: ' . (int) $product['stock'] . '.');
    }

    $cartModel = new CartModel();
    $itemModel = new CartItemModel();
    $cart      = $cartModel->forUser($this->userId());

    $existing = $itemModel->findProductInCart((int) $cart['id'], $productId);
    $wanted   = $existing === null ? $quantity : (int) $existing['quantity'] + $quantity;

    if ($wanted > (int) $product['stock']) {
        return redirect()->back()
            ->with('error', 'Total jumlah di keranjang melebihi stok tersedia (' . (int) $product['stock'] . ').');
    }
    // insert atau update, lalu redirect ke /cart
}
```

- `findActive()` memakai `where('status','active')`, jadi produk nonaktif tidak
  bisa dibeli walaupun ID-nya dikirim manual (BR-002).
- Kuantitas divalidasi dua kali: terhadap stok, dan terhadap **total setelah
  digabung**. Tanpa pemeriksaan kedua, membeli 5 + 5 dengan stok 8 akan lolos di
  masing-masing permintaan.
- Harga tidak pernah dibaca dari input. Kolom `price` diambil dari database saat
  menampilkan keranjang, sehingga parameter `price` yang disisipkan ke form
  diabaikan.

### Checkout: satu transaksi database

```php
$db = \Config\Database::connect();
$db->transBegin();

try {
    // 1. Validasi ulang setiap produk langsung dari database.
    foreach ($items as $item) {
        $product = $productModel->find((int) $item['product_id']);

        if ($product === null || $product['status'] !== 'active') {
            throw new RuntimeException('Produk "' . $item['name'] . '" sudah tidak dijual.');
        }

        if ((int) $product['stock'] < (int) $item['quantity']) {
            throw new RuntimeException('Stok "' . $product['name'] . '" tidak mencukupi.');
        }

        $line      = (float) $product['price'] * (int) $item['quantity'];
        $subtotal += $line;

        $snapshots[] = [
            'product_id'   => (int) $product['id'],
            'product_name' => $product['name'],
            'price'        => (float) $product['price'],
            'quantity'     => (int) $item['quantity'],
            'subtotal'     => $line,
        ];
    }

    // 2. orders, 3. order_items, 4. kurangi stok, 5. payments, 6. kosongkan keranjang.
    $db->transCommit();
} catch (\Throwable $e) {
    $db->transRollback();

    return redirect()->to(site_url('checkout'))->with('error', $e->getMessage());
}
```

Urutan yang dijalankan di dalam transaksi:

1. Ambil ulang produk dari database (bukan dari data keranjang yang mungkin sudah
   lama).
2. Tolak bila produk nonaktif atau stok kurang.
3. Hitung subtotal dari harga database, tambah ongkos kirim tetap Rp 20.000.
4. Insert `orders` dengan nomor unik `SE-YYYYMMDD-XXXX`.
5. Insert `order_items` berisi snapshot nama dan harga.
6. Kurangi stok dengan `WHERE stock >= quantity`; bila tidak ada baris terpengaruh,
   dilempar exception.
7. Insert `payments` berstatus `unpaid`.
8. Kosongkan keranjang dan hapus token checkout.

Kalau satu langkah gagal, `transRollback()` membatalkan semuanya: tidak ada pesanan
setengah jadi, stok tidak berkurang, dan isi keranjang tetap utuh. Ini diuji
dengan menurunkan stok di tengah alur; hasilnya jumlah `orders` tetap 0 dan stok
tidak berubah.

Pencegahan submit ganda dilakukan dengan token sekali pakai: `index()` membuat
token acak, menyimpannya di session, dan mengirimkannya ke form. `process()`
membandingkan token dengan `hash_equals()`, lalu menghapusnya.

## 8. View

### Arsitektur layout

Halaman publik memakai `layouts/main.php`, area admin memakai `layouts/admin.php`.
Keduanya hanya berisi kerangka; isi halaman disuntikkan lewat section.

```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Beranda') ?> &middot; ShopEase</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <?= $this->include('partials/navbar') ?>

    <main class="flex-grow-1">
        <?= $this->include('partials/flash') ?>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
```

Halaman child cukup begini:

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container section-pad">
    <h1 class="h3 mb-4">Keranjang</h1>
    <!-- isi halaman -->
</div>
<?= $this->endSection() ?>
```

Bagian yang dihasilkan:

| Bagian | Berkas | Isi |
|---|---|---|
| Main | `layouts/main.php` | `<head>`, `<main>`, footer, script |
| Navigasi | `partials/navbar.php` | Brand, menu, pencarian, menu akun |
| Pesan | `partials/flash.php` | Flash success/error/info + daftar error validasi |
| Footer | `partials/footer.php` | Tautan katalog, tentang, kontak, akun |
| Card produk | `partials/product_card.php` | Gambar, kategori, stok, harga |
| Pagination | `partials/pager.php` | Markup Bootstrap 5 |

### Data dari controller ke view

Controller mengirim satu array asosiatif ke `view()`. View tidak memanggil model
dan tidak menjalankan query. Contoh pada halaman detail pesanan, controller
mengirim empat variabel: `order`, `items`, `payment`, dan `shipment`. View hanya
memilih cara menampilkannya.

### 9.1 Alur pembeli

**Beranda** — `GET /` · route `home` · `HomeController::index` · `frontend/home.php`

![Beranda](docs/screenshots/01-home.png)

Menampilkan kategori beserta jumlah produk, produk terbaru, dan produk terlaris.
Angka "produk terlaris" dihitung dari `order_items` pada pesanan yang sudah
dibayar atau selesai.

**Tentang** — `GET /about` · route `about` · `HomeController::about` · `frontend/about.php`

![Tentang](docs/screenshots/02-about.png)

**Kontak** — `GET /contact` · route `contact` · `HomeController::contact` · `frontend/contact.php`

![Kontak](docs/screenshots/03-contact.png)

**Katalog** — `GET /catalog` · route `catalog` · `ProductController::catalog` · `frontend/catalog.php`

![Katalog](docs/screenshots/04-catalog.png)

Menampilkan 16 produk aktif dalam 2 halaman (8 per halaman). Dua produk nonaktif
tidak muncul.

**Pencarian, filter, dan pengurutan** — `GET /catalog?q=headphone&category=1&sort=price_asc`
Route `catalog` → `ProductController::catalog`

![Pencarian dan filter](docs/screenshots/05-catalog-search-filter.png)

Ketiga parameter diproses sekaligus: `q` mencari nama, `category` membatasi
kategori, `sort` menentukan urutan harga.

**Detail produk** — `GET /product/1` · route `product.detail` · `ProductController::detail/$1` · `frontend/product_detail.php`

![Detail produk](docs/screenshots/06-product-detail.png)

Menampilkan foto, harga, stok tersedia, dan form tambah ke keranjang. Untuk tamu,
form diganti tautan masuk; untuk peran selain customer, ditampilkan keterangan
bahwa keranjang hanya untuk customer.

**Halaman daftar** — `GET /register` · route `register` · `AuthController::register` · `auth/register.php`

![Daftar](docs/screenshots/07-register.png)

**Validasi gagal saat daftar** — `POST /register`
Route `register.attempt` → `AuthController::attemptRegister`

![Validasi daftar](docs/screenshots/08-register-validation-error.png)

Mengirim email yang sudah terdaftar. Form kembali dengan pesan kesalahan dan
isian sebelumnya tidak hilang.

**Halaman masuk** — `GET /login` · route `login` · `AuthController::login` · `auth/login.php`

![Masuk](docs/screenshots/09-login.png)

**Akses keranjang tanpa login** — `GET /cart`
Route `cart.index` → `CartController::index`, filter `auth:customer`

![Redirect ke login](docs/screenshots/10-redirect-login.png)

Permintaan `/cart` tanpa session diarahkan ke `/login` dengan pesan. Ini bukti
filter berjalan sebelum controller dieksekusi.

**Keranjang** — `GET /cart` · route `cart.index` · `CartController::index` · `cart/index.php`

![Keranjang](docs/screenshots/11-cart.png)

Subtotal per baris dan total dihitung dari harga di database. Kuantitas dapat
diubah lewat form POST ke `/cart/update`.

**Checkout** — `GET /checkout` · route `checkout.index` · `CheckoutController::index` · `checkout/index.php`

![Checkout](docs/screenshots/12-checkout.png)

Menampilkan pilihan alamat, rincian item, ongkos kirim Rp 20.000, dan total.
Formulir konfirmasi membawa token sekali pakai.

**Detail pesanan setelah dibuat** — `GET /orders/1` · route `orders.detail` · `OrderController::detail/$1` · `orders/detail.php`

![Detail pesanan](docs/screenshots/13-order-detail.png)

Status awal `pending_payment` dengan status pembayaran `unpaid`.

**Unggah bukti pembayaran** — `POST /orders/1/payment`
Route `orders.payment` → `OrderController::uploadProof/$1`

![Unggah bukti](docs/screenshots/14-upload-proof.png)

Setelah unggahan berhasil, status pembayaran menjadi `submitted` dan status
pesanan menjadi `waiting_verification`.

**Riwayat pesanan** — `GET /orders` · route `orders.index` · `OrderController::index` · `orders/index.php`

![Riwayat pesanan](docs/screenshots/15-order-history.png)

**Pesanan sudah dikirim** — `GET /orders/1`
Route `orders.detail` → `OrderController::detail/$1`

![Pesanan dikirim](docs/screenshots/26-order-shipped.png)

Menampilkan kurir, nomor resi, dan waktu pengiriman yang diisi operator.

### 9.2 Alur admin dan operator

**Dashboard** — `GET /admin/dashboard` · route `admin.dashboard` · `Admin\DashboardController::index` · `admin/dashboard.php`

![Dashboard](docs/screenshots/16-admin-dashboard.png)

Kartu statistik, grafik penjualan 14 hari terakhir (Chart.js), distribusi status,
produk terlaris, dan lima transaksi terbaru. Statistik dihitung dari tabel
`orders`, `payments`, `products`, dan `users`.

**Daftar kategori** — `GET /admin/categories`
Route `admin.categories.index` → `Admin\CategoryController::index`

![Kategori](docs/screenshots/17-admin-categories.png)

Kolom produk menunjukkan jumlah produk per kategori, sekaligus alasan mengapa
tombol hapus bisa ditolak.

**Formulir kategori** — `GET /admin/categories/create`
Route `admin.categories.create` → `Admin\CategoryController::create`

![Form kategori](docs/screenshots/18-admin-category-form.png)

Slug tidak diisi manual; dibuat dari nama dan diperiksa keunikannya di controller.

**Daftar produk** — `GET /admin/products`
Route `admin.products.index` → `Admin\ProductController::index`

![Produk](docs/screenshots/19-admin-products.png)

Menampilkan gambar, kategori, harga, stok, dan status. Terdapat pencarian nama
serta pagination 10 baris.

**Formulir produk** — `GET /admin/products/create`
Route `admin.products.create` → `Admin\ProductController::create`

![Form produk](docs/screenshots/20-admin-product-form.png)

Termasuk unggah gambar dengan batas 2 MB dan tipe JPG/PNG/WEBP.

**Daftar pesanan** — `GET /admin/orders`
Route `admin.orders.index` → `Admin\OrderController::index`

![Pesanan admin](docs/screenshots/21-admin-orders.png)

Dapat difilter berdasarkan status dan dicari berdasarkan nomor pesanan atau nama
customer.

**Detail pesanan** — `GET /admin/orders/1`
Route `admin.orders.detail` → `Admin\OrderController::detail/$1`

![Detail pesanan admin](docs/screenshots/22-admin-order-detail.png)

Memuat rincian item, alamat, bukti pembayaran, dan form pengiriman.

**Verifikasi pembayaran** — `GET /admin/payments`
Route `admin.payments.index` → `Admin\PaymentController::index`

![Verifikasi pembayaran](docs/screenshots/23-admin-payment-verify.png)

Hanya menampilkan pembayaran berstatus `submitted`. Tombol setujui mengirim POST
ke `/admin/payments/{id}/approve`, tombol tolak membuka isian alasan.

**Data pengiriman** — `POST /admin/shipments/1`
Route `admin.shipments.store` → `Admin\ShipmentController::store/$1`

![Pengiriman](docs/screenshots/24-admin-shipment.png)

Setelah pembayaran disetujui, pesanan berstatus `processing` dan form kurir serta
nomor resi muncul. Menyimpannya mengubah status pesanan menjadi `shipped`.

**Laporan penjualan** — `GET /admin/reports`
Route `admin.reports.index` → `Admin\ReportController::index`

![Laporan](docs/screenshots/25-admin-reports.png)

Filter periode dan status, ringkasan omzet, serta tabel transaksi. Omzet hanya
menghitung pesanan `processing`, `shipped`, dan `completed`.

### Lainnya

**Halaman tidak ditemukan** — `GET /halaman-yang-tidak-ada`

![404](docs/screenshots/27-404.png)

Tidak ada route yang cocok, sehingga CI4 memakai view 404 kustom. Path yang
diminta ikut ditampilkan.

**Akses ditolak** — `GET /admin/dashboard` sebagai customer

![403](docs/screenshots/28-forbidden.png)

`AuthFilter` mengembalikan status 403 dengan halaman kustom. URL tetap
`/admin/dashboard`, jadi terlihat bahwa yang ditolak memang route admin.

**Daftar route** — keluaran `php spark routes`

![Daftar route](docs/screenshots/29-terminal-routes.png)

---

## 10. Alur lengkap pembeli

Contoh: pembeli `bagas@shopease.test` membeli 2 unit Wireless Headphone BT-100
(Rp 299.000) dan 1 Kaos Polos Premium (Rp 89.000).

| # | URL | Controller | Yang terjadi di database |
|---|---|---|---|
| 1 | `GET /product/1` | `ProductController::detail` | – |
| 2 | `POST /cart/add` | `CartController::add` | `carts` dibuat bila belum ada; `cart_items` bertambah 2 |
| 3 | `POST /cart/add` | `CartController::add` | `cart_items` bertambah 1 baris untuk kaos |
| 4 | `GET /cart` | `CartController::index` | Baca `cart_items` + `products`; subtotal Rp 687.000 |
| 5 | `GET /checkout` | `CheckoutController::index` | Token checkout dibuat di session |
| 6 | `POST /checkout/process` | `CheckoutController::process` | Satu transaksi: `orders`, `order_items`, stok, `payments`, `cart_items` |
| 7 | `GET /orders/1` | `OrderController::detail` | Pesanan tampil, status `pending_payment` |
| 8 | `POST /orders/1/payment` | `OrderController::uploadProof` | `payments.proof_file` terisi, status `submitted`; `orders` → `waiting_verification` |
| 9 | `POST /admin/payments/1/approve` | `Admin\PaymentController::approve` | `payments` → `verified` + `verified_by`, `verified_at`; `orders` → `processing` |
| 10 | `POST /admin/shipments/1` | `Admin\ShipmentController::store` | `shipments` terisi kurir dan resi; `orders` → `shipped` |
| 11 | `GET /orders/1` | `OrderController::detail` | Pembeli melihat resi |
| 12 | `POST /admin/orders/1/status` | `Admin\OrderController::updateStatus` | `orders` → `completed`; `shipments` → `delivered` |

Nilai yang tersimpan:

| Tabel | Isi |
|---|---|
| `orders` | `order_number` = `SE-20261108-XXXX`, `subtotal` = 687000.00, `shipping_cost` = 20000.00, `total` = 707000.00, `order_status` |
| `order_items` | Dua baris: snapshot `product_name` dan `price` saat transaksi |
| `products` | `stock` berkurang 2 untuk produk 1 dan 1 untuk produk 7 |
| `payments` | `amount` = 707000.00, `payment_status`, `verified_by`, `verified_at` |
| `shipments` | `courier`, `tracking_number`, `shipment_status`, `shipped_at`, `delivered_at` |

Perubahan status lengkapnya:

```text
pending_payment
  --(unggah bukti)-->      waiting_verification
  --(admin setujui)-->     processing
  --(operator isi resi)--> shipped
  --(tandai selesai)-->    completed
```

Kalau bukti ditolak, pesanan kembali ke `pending_payment` dan pembeli dapat
mengunggah ulang. Alasan penolakan tersimpan di `payments.notes`.

---