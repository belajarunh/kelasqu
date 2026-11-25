# KelasQu

Aplikasi kelas online berbasis Laravel 12 dengan autentikasi, dashboard learner, dashboard admin, dan MySQL.

## Persiapan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql`
- Composer
- Node.js dan npm
- MySQL (Laragon dapat menggunakan user `root` tanpa password untuk pengembangan lokal)

Buat database MySQL bernama `kelaqu`, lalu sesuaikan koneksi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kelaqu
DB_USERNAME=root
DB_PASSWORD=
```

Untuk mengaktifkan Cloudflare Turnstile pada halaman login dan pendaftaran, buat site key dan secret key di dashboard Cloudflare, lalu isi `.env`:

```env
TURNSTILE_SITE_KEY=site_key_dari_cloudflare
TURNSTILE_SECRET_KEY=secret_key_dari_cloudflare
```

Pastikan hostname aplikasi (misalnya `localhost` untuk pengembangan lokal) diizinkan pada konfigurasi Turnstile. Setelah mengubah `.env`, jalankan `php artisan config:clear`.

## Menjalankan aplikasi

```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk menjalankan server Laravel dan Vite dalam mode pengembangan sekaligus, gunakan `composer run dev` setelah dependency Composer dan npm terpasang.

## Materi pertemuan kelas

Admin dapat membuka **Kelola kelas**, memilih detail kelas, lalu menambahkan pertemuan berurutan yang berisi materi teks, tautan video, lampiran privat, instruksi tugas, dan soal kuis pilihan ganda. Peserta yang sudah enroll dapat membuka materi, mengunduh lampiran, mencoba kuis tanpa batas, dan menandai pertemuan selesai. Nilai terbaik kuis disimpan; nilai lulus awal 75% dan dapat diubah per pertemuan.

## Akun demo

- Admin: `admin@kelasqu.id` / `KelasQuAdmin123!`
- Learner: `siswa@kelasqu.id` / `KelasQuUser123!`

Kredensial admin dapat diatur melalui `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`. Ganti seluruh password demo sebelum aplikasi digunakan di luar lingkungan lokal.

## Pengujian

```bash
php artisan test
```
