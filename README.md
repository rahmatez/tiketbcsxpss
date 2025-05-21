# Sistem Pemesanan Tiket dengan QR Code

<p align="center">
  <img src="public/images/logo.png" alt="Logo Pesan Tiket QR Code" width="200">
</p>

## Tentang Aplikasi

Sistem Pemesanan Tiket dengan QR Code adalah aplikasi berbasis web yang dikembangkan menggunakan framework Laravel. Aplikasi ini dirancang untuk memudahkan pengguna dalam memesan tiket pertandingan olahraga dengan sistem pembayaran terintegrasi dan validasi tiket menggunakan QR Code.

## Fitur Utama

-   **Manajemen Pertandingan**: Admin dapat mengelola data pertandingan termasuk detail tim, waktu pertandingan, venue, dan kategori tiket.
-   **Pemesanan Tiket**: Pengguna dapat memesan tiket dari berbagai kategori untuk pertandingan yang tersedia.
-   **Pembayaran Online**: Integrasi dengan Midtrans untuk memfasilitasi pembayaran online secara aman.
-   **QR Code**: Setiap tiket dilengkapi dengan QR Code unik untuk validasi kehadiran.
-   **Pemindaian Tiket**: Admin dapat memindai QR Code untuk memvalidasi tiket saat pengguna hadir di venue.
-   **Notifikasi**: Sistem notifikasi untuk menginformasikan pengguna tentang status pemesanan dan informasi penting lainnya.
-   **Laporan**: Admin dapat mengakses berbagai laporan terkait penjualan tiket dan kehadiran.
-   **Kontak dan Bantuan**: Pengguna dapat mengirim pesan dan pertanyaan terkait pemesanan atau masalah lainnya.

## Teknologi yang Digunakan

-   **Framework**: Laravel 10
-   **Database**: MySQL
-   **Frontend**: HTML, CSS, JavaScript, Bootstrap
-   **QR Code**: Bacon QR Code Generator
-   **PDF Generator**: Laravel Snappy
-   **Payment Gateway**: Midtrans

## Persyaratan Sistem

-   PHP >= 8.1
-   Composer
-   MySQL atau MariaDB
-   Node.js dan NPM
-   Ekstensi PHP yang dibutuhkan: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalasi

1. **Clone repository**

    ```bash
    git clone https://github.com/username/pesan-tiket-qrcode.git
    cd pesan-tiket-qrcode
    ```

2. **Instal dependensi PHP**

    ```bash
    composer install
    ```

3. **Instal dependensi JavaScript**

    ```bash
    npm install
    npm run build
    ```

4. **Siapkan file .env**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Konfigurasi database di file .env**

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database
    DB_USERNAME=username
    DB_PASSWORD=password
    ```

6. **Konfigurasi Midtrans di file .env**

    ```
    MIDTRANS_SERVER_KEY=your_server_key
    MIDTRANS_CLIENT_KEY=your_client_key
    MIDTRANS_IS_PRODUCTION=false
    MIDTRANS_SANITIZE=true
    MIDTRANS_3DS=true
    ```

7. **Jalankan migrasi dan seeder**

    ```bash
    php artisan migrate --seed
    ```

8. **Buat symbolic link untuk penyimpanan**

    ```bash
    php artisan storage:link
    ```

9. **Jalankan aplikasi**
    ```bash
    php artisan serve
    ```

## Struktur Database

Sistem ini menggunakan 11 tabel utama:

-   **users**: Menyimpan data pengguna
-   **admins**: Menyimpan data administrator
-   **games**: Informasi pertandingan yang tersedia
-   **tickets**: Kategori tiket untuk setiap pertandingan
-   **orders**: Transaksi pemesanan tiket
-   **ticket_scans**: Riwayat pemindaian tiket
-   **provinces** & **cities**: Data wilayah administratif
-   **pdf_templates**: Template untuk pembuatan PDF tiket
-   **notifications**: Notifikasi untuk pengguna
-   **contact_messages**: Pesan kontak dari pengguna

Untuk penjelasan lebih detail tentang struktur database, silakan lihat dokumentasi di folder `documentation/`.

## Pengembangan

### Menjalankan Development Server

```bash
php artisan serve
npm run dev
```

### Migrasi Database

```bash
php artisan migrate
```

### Seed Database

```bash
php artisan db:seed
```

### Testing

```bash
php artisan test
```

## Kontribusi

Kami sangat terbuka untuk kontribusi! Jika Anda ingin berkontribusi pada proyek ini, silakan ikuti langkah-langkah berikut:

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan Anda (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
