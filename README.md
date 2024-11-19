# Collab Project

Collab Project adalah Web pembelajaran yang memiliki 3 Role yaitu Admin, Teacher, Student. Project ini menggunakan Laravel Versi 9

## Prasyarat

- PHP >= 8.1.10
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Composer versi 2+
- Database (MySQL 5.7+)
- Web Server (Apache 2.4+)

## Instalasi

1. Clone repository
git clone <url_repository>
cd <nama_folder>

2. Install dependencies
composer install

3. Setup environment
cp .env.example .env
# Sesuaikan konfigurasi database dan setting lainnya di .env
php artisan key:generate

4. Setup aplikasi
php artisan migrate
php artisan storage:link


## Maintenance

- Selalu backup database sebelum melakukan migrasi
- Jalankan `composer update` untuk update dependencies
- Jalankan `php artisan migrate` jika ada perubahan database
- Update file `.env` jika ada konfigurasi baru

## Troubleshooting

- Jika terjadi error permission: periksa folder storage dan bootstrap/cache
- Jika ada masalah cache: coba clear semua cache
- Jika ada error database: periksa konfigurasi di .env
