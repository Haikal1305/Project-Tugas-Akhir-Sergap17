<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"></a></p>

<h1 align="center">Aplikasi Absensi & Laporan Patroli Satpam</h1>
<p align="center"><strong>PT SERGAP 17</strong> — Berbasis Web (Laravel)</p>

---

## 📌 Tentang Aplikasi

Aplikasi ini dibangun untuk membantu **PT SERGAP 17** dalam mengelola:

- **Absensi** karyawan/satpam secara digital
- **Laporan Patroli Satpam** secara real-time berbasis web

Sistem otorisasi (hak akses) aplikasi ini dibangun berlapis menggunakan tiga pendekatan sekaligus:

| Lapisan | Teknologi | Fungsi |
|---|---|---|
| **RBAC** (Role-Based Access Control) | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) | Mengatur hak akses berdasarkan *role* (misal: Admin, Supervisor, Satpam) |
| **ReBAC** (Relationship-Based Access Control) | [Casbin](https://casbin.org/) | Mengatur akses berdasarkan relasi antar entitas (misal: satpam hanya bisa akses data di pos/area miliknya) |
| **ABAC** (Attribute-Based Access Control) | [Casbin](https://casbin.org/) | Mengatur akses berdasarkan atribut (misal: waktu shift, lokasi, status karyawan) |

Selain itu, aplikasi juga dilengkapi **Notifikasi Real-time** menggunakan **Laravel Reverb** (WebSocket server bawaan Laravel), sehingga laporan patroli, absensi, atau kejadian penting bisa langsung muncul ke pengguna lain tanpa perlu refresh halaman.

## ✨ Fitur Utama

- ✅ **Manajemen Absensi** — pencatatan kehadiran karyawan/satpam
- ✅ **Laporan Patroli Satpam** — pencatatan titik patroli, waktu, dan kondisi lapangan
- ✅ **Manajemen Role & Hak Akses** berlapis (RBAC + ReBAC + ABAC)
- ✅ **Notifikasi Real-time** via WebSocket (Laravel Reverb)
- ✅ **Dashboard** untuk monitoring aktivitas

> Silakan sesuaikan/lengkapi daftar fitur di atas sesuai modul yang sudah kamu buat di folder `app/` — saya buat poin-poin umum berdasarkan deskripsi project.

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel (PHP)
- **Autentikasi & Role:** Spatie Laravel Permission
- **Access Control:** Casbin (ReBAC & ABAC)
- **Real-time:** Laravel Reverb (WebSocket)
- **Database:** MySQL
- **Frontend Build Tool:** Vite

---

## 🚀 Cara Menjalankan di Localhost (Windows, Tanpa Docker)

Berikut langkah menjalankan aplikasi ini langsung di komputer Windows kamu (tanpa Docker).

### 1. Prasyarat (Install dulu di Windows)

Pastikan sudah terinstall:

- **PHP** (disarankan versi 8.2 atau lebih baru) — bisa pakai [Laragon](https://laragon.org/) atau [XAMPP](https://www.apachefriends.org/) biar gampang, atau install manual dari [windows.php.net](https://windows.php.net/download/)
- **Composer** — [getcomposer.org](https://getcomposer.org/download/)
- **Node.js** (LTS) & **NPM** — [nodejs.org](https://nodejs.org/)
- **MySQL** — bisa dari Laragon/XAMPP, atau install manual [MySQL Community Server](https://dev.mysql.com/downloads/mysql/)
- **Git** (opsional, kalau clone dari GitHub) — [git-scm.com](https://git-scm.com/download/win)

> 💡 Tips: **Laragon** paling praktis untuk Windows karena sudah menyediakan PHP, MySQL, dan Composer dalam satu paket.

### 2. Clone / Download Project

```bash
git clone https://github.com/mlnaarianto/Project-Tugas-Akhir-Sergap17.git
cd Project-Tugas-Akhir-Sergap17
```

### 3. Install Dependency PHP (Composer)

```bash
composer install
```

### 4. Install Dependency JavaScript (NPM)

```bash
npm install
```

### 5. Copy File `.env`

```bash
copy .env.example .env
```

> Kalau belum ada `.env.example`, buat file baru bernama `.env` lalu isi seperti contoh di bawah.

### 6. Sesuaikan Isi `.env` untuk Localhost (Windows)

Karena sebelumnya konfigurasi ini dipakai untuk Docker (`DB_HOST=db`, `REVERB_HOST=reverb`), untuk localhost Windows **ubah** menjadi seperti ini:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haikal
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

REVERB_APP_ID=916220
REVERB_APP_KEY=wki2hjv98ht8dhsvhp5e
REVERB_APP_SECRET=p4uqnycyj2oeuvc0urrm
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="127.0.0.1"
VITE_REVERB_PORT="8080"
VITE_REVERB_SCHEME="http"
```

**Poin penting yang diubah dari versi Docker:**

| Variabel | Versi Docker | Versi Localhost Windows |
|---|---|---|
| `DB_HOST` | `db` | `127.0.0.1` |
| `DB_PASSWORD` | `root` | kosongkan / sesuaikan password MySQL lokal kamu |
| `REVERB_HOST` | `reverb` | `127.0.0.1` |
| `VITE_REVERB_HOST` | `localhost` | `127.0.0.1` |
| `VITE_REVERB_PORT` | `8000` | `8080` (harus sama dengan `REVERB_PORT`) |

> ⚠️ Ganti `DB_PASSWORD` sesuai password MySQL yang kamu pakai di lokal (default Laragon biasanya kosong, default XAMPP juga biasanya kosong).

### 7. Buat Database

Buka **phpMyAdmin** (via Laragon/XAMPP) atau MySQL command line, lalu buat database baru bernama sesuai `DB_DATABASE` di `.env` (contoh: `haikal`):

```sql
CREATE DATABASE haikal;
```

### 8. Generate Application Key

```bash
php artisan key:generate
```

Perintah ini otomatis mengisi `APP_KEY` di file `.env`.

### 9. Jalankan Migration (dan Seeder jika ada)

```bash
php artisan migrate
```

Kalau ada data awal (seeder):

```bash
php artisan migrate --seed
```

### 10. Buat Symbolic Link Storage (jika aplikasi upload file/gambar)

```bash
php artisan storage:link
```

### 11. Build Asset Frontend (Vite)

Untuk mode development (auto reload saat ada perubahan):

```bash
npm run dev
```

Biarkan terminal ini tetap berjalan (jangan ditutup).

### 12. Jalankan Reverb (WebSocket Server untuk Notifikasi Real-time)

Buka **terminal baru** (jangan tutup terminal `npm run dev`), lalu jalankan:

```bash
php artisan reverb:start
```

Kalau ingin Reverb berjalan di background/tanpa nge-block terminal, bisa tambahkan `--debug` untuk melihat log koneksi:

```bash
php artisan reverb:start --debug
```

### 13. Jalankan Queue Worker (jika aplikasi pakai Job/Notification Queue)

Karena `QUEUE_CONNECTION=database`, buka **terminal baru lagi**, lalu jalankan:

```bash
php artisan queue:work
```

> Kalau notifikasi Reverb tidak muncul secara real-time, biasanya karena `queue:work` belum dijalankan (job broadcast masih tertahan di antrian).

### 14. Jalankan Server Laravel

Buka **terminal baru** (terpisah dari yang lain), lalu jalankan:

```bash
php artisan serve
```

Aplikasi akan berjalan di:

```
http://localhost:8000
```

---

## 🧾 Ringkasan Terminal yang Harus Berjalan Bersamaan

Karena ada beberapa proses berbeda, kamu perlu **4 terminal terbuka sekaligus** saat development:

| Terminal | Perintah | Fungsi |
|---|---|---|
| 1 | `npm run dev` | Compile asset (CSS/JS) via Vite |
| 2 | `php artisan reverb:start` | WebSocket server untuk notifikasi real-time |
| 3 | `php artisan queue:work` | Memproses antrian job/notifikasi |
| 4 | `php artisan serve` | Menjalankan web server Laravel |

> 💡 Tips: kalau pakai **Laragon**, kamu bisa skip `php artisan serve` dan langsung akses via domain virtual host Laragon (misal `http://project-tugas-akhir-sergap17.test`), asalkan folder project sudah ditaruh di `C:\laragon\www\`.

---

## ❗ Troubleshooting Umum

- **Error `SQLSTATE[HY000] [2002]`** → MySQL belum running, pastikan service MySQL aktif (cek di Laragon/XAMPP Control Panel)
- **Error `Class "Casbin\..." not found`** → jalankan ulang `composer install`
- **Notifikasi real-time tidak muncul** → pastikan `reverb:start` dan `queue:work` sama-sama berjalan, dan cek `VITE_REVERB_*` di `.env` sudah sesuai lalu jalankan ulang `npm run dev`
- **Halaman blank/error 500** → cek `storage/logs/laravel.log`, dan pastikan `APP_DEBUG=true` untuk melihat detail error saat development

---

## 📄 Lisensi

Project ini dibangun di atas framework [Laravel](https://laravel.com), yang berlisensi [MIT license](https://opensource.org/licenses/MIT).