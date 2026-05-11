# 🚀 Panduan Update Deployment ke cPanel (npkpadi.ircodes.xyz)

> Gunakan panduan ini setiap kali ada perubahan kode yang perlu di-deploy ke hosting.

---

## Prasyarat

- Akses ke cPanel Anymhost → Terminal
- SSH sudah aktif
- Git sudah terkonfigurasi di server

---

## Langkah 1 — Masuk ke Terminal cPanel

1. Login ke cPanel: `furina.kawaiihost.net:2083`
2. Cari menu **Terminal** → klik
3. Atau gunakan SSH dari laptop:
   ```bash
   ssh oouaruxf@furina.kawaiihost.net -p 2083
   ```

---

## Langkah 2 — Pull Kode Terbaru

```bash
cd ~/npkpadi
git pull origin main
```

Pastikan output menunjukkan file yang diupdate, bukan `Already up to date`.

---

## Langkah 3 — Jalankan Migration (jika ada perubahan database)

> ⚠️ Jalankan ini jika ada file migration baru (cek folder `database/migrations/`)

```bash
php artisan migrate --force
```

**Update ini (11 Mei 2026)** menambahkan kolom `planting_date` ke tabel `lands`:
```
Running migrations:
  2026_05_11_000000_add_planting_date_to_lands_table ✔
```

---

## Langkah 4 — Install Dependency Baru (jika composer.json berubah)

```bash
composer install --no-dev --optimize-autoloader
```

> Lewati langkah ini jika `composer.json` tidak berubah.

---

## Langkah 5 — Clear & Rebuild Cache

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

Lalu rebuild cache untuk production:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Langkah 6 — Verifikasi

Buka browser ke **http://npkpadi.ircodes.xyz** dan pastikan:

- [ ] Halaman login bisa diakses
- [ ] Login sebagai admin → cek `/admin/settings` (baru!)
- [ ] Login sebagai petani → cek `/farmer/settings` (baru!)
- [ ] Tambah lahan baru → ada field **Tanggal Tanam** (baru!)

---

## Troubleshooting

### Halaman error 500
```bash
tail -50 ~/npkpadi/storage/logs/laravel.log
```

### Permission error
```bash
chmod -R 775 ~/npkpadi/storage
chmod -R 775 ~/npkpadi/bootstrap/cache
```

### View tidak update
```bash
php artisan view:clear
php artisan view:cache
```

### Config tidak terbaca
```bash
php artisan config:clear
php artisan config:cache
```

---

## Ringkasan Perubahan (Update 11 Mei 2026)

| Fitur | Keterangan |
|-------|-----------|
| ⚙️ Halaman Settings Admin | `/admin/settings` — ubah nama, email, password |
| ⚙️ Halaman Settings Petani | `/farmer/settings` — ubah nama, email, password |
| 📅 Tanggal Tanam | Field baru di form tambah/edit lahan |
| 📊 Dashboard NPK | Perhitungan N/P/K lebih akurat (berdasarkan nama) |
| 🔑 Reset Password Petani | Admin bisa reset password petani ke default `petani123` |

---

## Informasi Server

| Item | Nilai |
|------|-------|
| Domain | `npkpadi.ircodes.xyz` |
| Path | `/home/oouaruxf/npkpadi` |
| PHP | 8.4 (alt-php84) |
| Database | `oouaruxf_npkpadi` |
| Git Remote | `https://github.com/ilmiriyadi/npkpadi.git` |

---

## Perintah Lengkap (Copy-Paste)

```bash
cd ~/npkpadi \
  && git pull origin main \
  && php artisan migrate --force \
  && php artisan view:clear \
  && php artisan config:clear \
  && php artisan route:clear \
  && php artisan config:cache \
  && php artisan route:cache \
  && echo "✅ Deploy selesai!"
```
