# KelasQu

Aplikasi kelas online berbasis Laravel 12 dengan autentikasi, dashboard learner, dashboard admin, dan MySQL.

## Persiapan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql`
- Composer
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

## Menjalankan aplikasi

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun demo

- Admin: `admin@kelasqu.id` / `KelasQuAdmin123!`
- Learner: `siswa@kelasqu.id` / `KelasQuUser123!`

Kredensial admin dapat diatur melalui `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`. Ganti seluruh password demo sebelum aplikasi digunakan di luar lingkungan lokal.

## Pengujian

```bash
php artisan test
```
