# Deployment Checklist

Ringkasan singkat langkah-langkah penting sebelum dan saat deploy aplikasi ke production.

## 1) Persyaratan Sistem
- PHP >= 8.2
- PHP extensions: `gd`, `pdo_mysql` (atau driver DB anda), `fileinfo`, `mbstring`, `openssl`.
- Composer & Node/NPM untuk build aset
- Redis (direkomendasikan) atau database untuk queue

## 2) Konfigurasi PHP (php.ini)
- upload_max_filesize = 8M
- post_max_size = 10M
- memory_limit = 128M (atau lebih sesuai kebutuhan)
- max_execution_time = 300
- Pastikan `gd` aktif: `php -m | grep gd` atau `php -r "var_export(extension_loaded('gd'));"`

> Catatan: Aplikasi mengharuskan kompresi gambar (ImageService) menggunakan GD. Tanpa GD, proses akan tetap fallback tetapi kompresi tidak berjalan.

## 3) Environment (.env)
Atur variabel penting:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `QUEUE_CONNECTION=redis` (direkomendasikan) atau `database`
- `WHATSAPP_API_TOKEN=...` (WA provider)
- `ADMIN_EMAIL=ops@example.com` (alert WA provider failures)
- DB_* konfigurasi dan mail provider

## 4) Migrate & Seed
```bash
php artisan migrate --force
php artisan db:seed --force   # jika perlu
```

## 5) Storage & Permissions
```bash
php artisan storage:link
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 6) Queue Workers (Production)
Aplikasi memproses kompresi gambar di background dan job lain (WA, email). Pastikan worker berjalan, contoh untuk beberapa platform:

- systemd (Linux):
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/project/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
WorkingDirectory=/var/www/project

[Install]
WantedBy=multi-user.target
```

- Supervisor (Linux):
```ini
[program:laravel-queue]
command=php /var/www/project/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
process_name=%(program_name)s_%(process_num)02d
numprocs=2
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel-queue.log
```

- NSSM (Windows): contoh singkat
```powershell
nssm install laravel-queue C:\php\php.exe "C:\path\to\project\artisan queue:work redis --sleep=3 --tries=3 --timeout=90"
nssm set laravel-queue AppDirectory C:\path\to\project
nssm set laravel-queue AppStdout C:\path\to\project\storage\logs\worker.log
nssm start laravel-queue
```

> Tips: saat deploy, jalankan `php artisan queue:restart` untuk menjalankan ulang proses worker dengan kode baru.

- **KTP / Pengikut Image Handling**: Aplikasi sekarang menyimpan foto dalam format Base64 secara instan. Pastikan database dikonfigurasi untuk menangani ukuran baris data yang besar (longtext).
- **Server-side Compression**: Pastikan modul `mod_deflate` (Apache) atau `gzip` (Nginx) aktif untuk memanfaatkan optimasi kecepatan yang ada di `.htaccess`.

## 7) Prosedur Build (Production)
Sebelum mengunggah kode ke server, jalankan perintah build aset untuk memastikan performa maksimal:
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# Compile & Minify Assets
npm run build
```

## 8) Prosedur Pengujian (Testing)
Pastikan semua fitur berjalan normal sebelum rilis:
```bash
# Jalankan unit & feature tests
php artisan test

# Pastikan tidak ada eror pada route & view
php artisan route:list
php artisan view:cache
```

## 9) Langkah Rilis (v1.2.0 Update)
Jika Anda memperbarui dari versi sebelumnya ke v1.2.0, ikuti urutan ini:
1.  **Backup Database**: Selalu cadangkan data sebelum melakukan migrasi.
2.  **Pull Code**: Ambil perubahan terbaru.
3.  **Migrate**: Jalankan index database baru.
    ```bash
    php artisan migrate --force
    ```
4.  **Optimize**: Bersihkan dan bangun ulang cache Laravel.
    ```bash
    php artisan optimize
    ```
5.  **Queue Restart**: Jika menggunakan background worker.
    ```bash
    php artisan queue:restart
    ```

## 10) Monitoring & Logging
- Pantau `storage/logs/laravel.log` untuk error runtime.
- Pantau worker log (mis. `/var/log/laravel-queue.log` atau `storage/logs/worker.log`).
- Pantau penggunaan memori database karena penyimpanan Base64.

---

Jika mau, saya bisa membuat script deploy otomatis (`deploy.sh` atau `deploy.ps1`) untuk menjalankan langkah-langkah di atas dalam satu perintah. Pilih: `script` / `tidak`.