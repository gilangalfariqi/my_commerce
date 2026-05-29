# MotoPartHub — Motorcycle Spare Parts E-Commerce

<p align="center">
  <a href="#" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

**MotoPartHub** adalah platform e-commerce premium berbasis web yang dirancang khusus untuk penjualan suku cadang (spare part) sepeda motor. Aplikasi ini dibangun menggunakan **Laravel 13**, **Livewire 3**, dan **Alpine.js** dengan berfokus penuh pada estetika visual modern, performa tinggi, optimasi mesin pencari (SEO), serta pengalaman pengguna seluler yang luar biasa (*mobile-first*).

---

## 🚀 Fitur Utama

### 1. Desain Premium & Mobile-First
* **Aethetics Premium**: Antarmuka bertema gelap yang elegan (`#0b0f19`) menggunakan perpaduan tipografi **Outfit** (untuk judul) dan **Plus Jakarta Sans** (untuk teks isi) dengan aksen warna merah menyala (`#dc2626`).
* **Responsif Sempurna**: Seluruh elemen halaman utama, katalog, detail produk, laci keranjang belanja (*cart drawer*), hingga halaman checkout dirancang fleksibel mulai dari resolusi layar HP terkecil (320px) hingga monitor layar lebar.
* **Galeri Swiper Mobile**: Di layar HP, gambar utama produk dapat digeser (*swipe*) secara natural menggunakan *scroll snap native* yang dilengkapi indikator halaman (misal: `1/3`), terhubung langsung dengan thumbnail strip.
* **Daftar Kompatibilitas Collapsible**: Daftar kompatibilitas motor yang sangat panjang pada halaman detail produk (PDP) akan dilipat secara otomatis hanya menampilkan 4 motor teratas di HP untuk menghindari kelelahan scrolling (*scroll fatigue*).
* **Sticky Bottom WhatsApp CTA**: Tombol pemesanan WhatsApp melayang yang akan meluncur naik secara otomatis ketika tombol utama di halaman detail tergulung keluar dari layar HP.

### 2. Livewire 3 Dynamic Filtering & Search
* **Pencarian Real-Time**: Kolom pencarian global pada header dilengkapi dengan dropdown saran otomatis (*autocomplete suggestions*) yang responsif.
* **Filter Kompatibilitas Motor (Garasi Saya)**: Filter katalog berdasarkan Merek Motor, Model, dan Tahun. Pilihan motor disimpan secara global di dalam store Alpine.js sehingga mempersonalisasi kecocokan suku cadang untuk motor pengguna.
* **Filter Dinamis Multi-Kriteria**: Penyaringan instan berdasarkan kategori, rentang harga, status stok, serta metode pengurutan (Terbaru, Terpopuler, Harga Terendah/Tertinggi) tanpa *reload* halaman.
* **Flat URL Parameter (SEO-Friendly)**: Semua binding pencarian Livewire menggunakan format query parameter datar (`?category=...&brand=...`) untuk memudahkan pengindeksan oleh robot Google.

### 3. Alur WhatsApp Fast Checkout
* **Checkout Instan WhatsApp**: Mengganti gerbang pembayaran (payment gateway) yang rumit dengan checkout langsung ke WhatsApp yang efisien dan andal.
* **Pesan Terstruktur**: Menyusun informasi pemesanan secara rapi, otomatis menyertakan nama pembeli, rincian barang, jumlah pesanan, total harga, link detail produk, serta detail motor yang disesuaikan oleh pembeli untuk meminimalisir kesalahan order.

### 4. Arsitektur SEO Profesional
* **Meta Tag Dinamis & OpenGraph**: Otomatis menghasilkan judul, deskripsi meta, kata kunci, dan OpenGraph tag untuk optimasi pembagian link di media sosial.
* **Skema Terstruktur (JSON-LD Schemas)**: Menyematkan skema Google Search Engine untuk Breadcrumb dan informasi detail produk (*Product Schema*) termasuk harga, ketersediaan stok, dan SKU.
* **Sitemap Otomatis**: Menyediakan sitemap XML dinamis (`/sitemap.xml`) yang ramah mesin pencari.
* **Konfigurasi Robots.txt**: Mengarahkan crawler mesin pencari dengan file `/robots.txt` yang dikonfigurasi secara tepat.

---

## 🛠️ Spesifikasi Teknologi

* **Backend Framework**: Laravel 13.x (PHP 8.2+)
* **Frontend Interactivity**: Livewire 3.x & Alpine.js 3.x
* **Styling**: Tailwind CSS (CDN Integration)
* **Database**: SQLite (Default Development)
* **Icon Library**: Font Awesome 6.4.0

---

## 📂 Struktur Berkas Penting

```bash
my_commerce/
├── app/
│   ├── Http/Controllers/Frontend/
│   │   ├── HomeController.php          # Logika banner, flash sale & produk terbaru
│   │   ├── ProductController.php       # Detail produk & schema builder
│   │   ├── CheckoutController.php      # Validasi & perakitan URL WhatsApp
│   │   └── SitemapController.php       # Generator Sitemap XML dinamis
│   ├── Livewire/Catalog/
│   │   └── ProductFilter.php           # Logika filter katalog Livewire
│   ├── Models/
│   │   ├── Product.php                 # Relasi fitment & format harga final
│   │   ├── BikeBrand.php               # Model merek sepeda motor
│   │   ├── BikeModel.php               # Model varian sepeda motor
│   │   └── ProductFitment.php          # Pivot table kompatibilitas motor & tahun
│   └── Services/SEO/
│       ├── MetaService.php             # Penanganan meta tag dinamis
│       └── SchemaService.php           # Generator skema JSON-LD untuk SEO
├── database/
│   ├── migrations/                     # Migrasi tabel produk & kecocokan motor
│   └── seeders/
│       ├── DatabaseSeeder.php          # Pemanggil seeder utama
│       └── BikeFitmentSeeder.php       # Data bawaan kompatibilitas motor
├── resources/views/
│   ├── layouts/
│   │   └── frontend.blade.php          # Tata letak utama & Cart Drawer
│   ├── frontend/
│   │   ├── home.blade.php              # Halaman Beranda (Hero, Garage Widget)
│   │   ├── product/
│   │   │   └── show.blade.php          # Halaman Detail Produk & Swiper
│   │   └── checkout/
│   │       └── whatsapp_fast.blade.php # Halaman Formulir Checkout
│   └── livewire/catalog/
│       ├── product-filter.blade.php    # Tampilan Sidebar & Grid Filter
│       └── product-card.blade.php      # Blade partial kartu produk
└── routes/
    └── web.php                         # Definisi rute aplikasi
```

---

## ⚙️ Cara Instalasi & Setup Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan project MotoPartHub di komputer lokal Anda:

### 1. Kloning Repositori
```bash
git clone https://github.com/username/my_commerce.git
cd my_commerce
```

### 2. Pasang Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Salin Konfigurasi Environment
```bash
cp .env.example .env
```
Buka file `.env` baru Anda dan sesuaikan pengaturan penting seperti nomor WhatsApp toko Anda:
```env
WHATSAPP_NUMBER=6282174128947
APP_URL=http://localhost:8000
```

### 4. Konfigurasi Database (SQLite)
Secara bawaan, aplikasi ini dikonfigurasi menggunakan SQLite untuk kemudahan setup. Jalankan perintah berikut untuk membuat file database kosong:

**Di Windows (PowerShell):**
```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

**Di Linux/macOS:**
```bash
touch database/database.sqlite
```

### 5. Jalankan Migrasi & Seeder Database
Populasikan tabel produk, kategori, banner, serta database kecocokan motor bawaan:
```bash
php artisan migrate --seed
```

### 6. Bersihkan Cache & Siapkan Tautan Media
```bash
php artisan storage:link
php artisan view:clear
php artisan cache:clear
```

### 7. Jalankan Server Pengembangan Lokal
Jalankan server PHP Artisan dan jalankan server build asset Vite:
```bash
php artisan serve
```
Buka `http://localhost:8000` pada browser Anda untuk mengakses aplikasi.

---

## 📝 Format Pesan WhatsApp Checkout

Ketika pelanggan melakukan checkout, detail belanja akan diformat seperti berikut dan diteruskan secara otomatis ke tautan WhatsApp admin toko:

```text
Halo, saya ingin memesan produk:

🛒 DETAIL PESANAN:
- 1x Kampas Rem Depan Honda Vario 150 (Rp 45.000)
- 2x Busi NGK Iridium Gixxer 150 (Rp 90.000)

🔧 DETAIL KENDARAAN:
- Honda Vario 150 (Tahun 2021)

💰 RINGKASAN:
- Total Item: 3 pcs
- Subtotal: Rp 225.000
- Total Pembayaran: Rp 225.000

👤 DATA PEMBELI:
- Nama: Gilang Al Fariqi
- Telepon: 08123456789
- Alamat: Jl. Sudirman No. 12, Jakarta

Mohon konfirmasi ketersediaan barang dan petunjuk pembayaran. Terima kasih!
```

---

## 📄 Lisensi

Project ini dilisensikan di bawah lisensi open-source **MIT License**.
