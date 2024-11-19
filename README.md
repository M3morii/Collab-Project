# Collab Project

Collab Project adalah Web pembelajaran yang memiliki 3 Role yaitu Admin, Teacher, Student. Project ini menggunakan Laravel Versi 9

## Prasyarat

- PHP >= 8.1.10
- JSON PHP Extension
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
Setting.env
php artisan key:generate

# Sesuaikan konfigurasi database dan setting lainnya di .env


4. Setup aplikasi
php artisan db:seed
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

# Tabel ERD

![ERD 3 Roles](https://github.com/user-attachments/assets/88dddaa9-a71e-460a-99b6-89b1aae0c962)

# Tabel DFD

![DFD 3 Roles](https://github.com/user-attachments/assets/415a3915-0403-4785-b499-b0dc89b5b389)
