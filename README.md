# LabTrack 

**Sistem Manajemen Peminjaman Inventaris Laboratorium Berbasis Web**

LabTrack adalah aplikasi web yang dirancang untuk mempermudah pengelolaan inventaris dan proses peminjaman alat/barang di laboratorium kampus. Sistem ini menyediakan alur kerja yang terstruktur bagi Admin laboratorium dan Mahasiswa, mulai dari pengajuan peminjaman hingga konfirmasi pengembalian barang.

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| **Backend Framework** | Laravel 12 |
| **Frontend Styling** | Tailwind CSS (via Vite) |
| **Template Engine** | Blade |
| **Database** | SQLite |
| **Autentikasi** | Laravel Breeze |
| **Testing** | Pest PHP |
| **Arsitektur** | MVC (Model-View-Controller) |

---

## Fitur Utama

### 👤 Role Admin
- **Dashboard** — Statistik real-time (total stok, tersedia, dipinjam, menunggu persetujuan), daftar barang terpopuler, aktivitas terbaru, dan **Advanced Analytics** visualisasi grafik (Chart.js) tren peminjaman mingguan, distribusi kategori barang, serta kondisi barang.
- **Manajemen Kategori** — CRUD kategori barang (Create, Read, Update, Delete).
- **Manajemen Inventaris** — CRUD barang dengan **Upload Gambar Barang**, pengelolaan kondisi (Baik, Rusak, Hilang, Perawatan) dan sinkronisasi status otomatis.
- **Persetujuan Peminjaman** — Menyetujui atau menolak permintaan peminjaman dari mahasiswa.
- **Konfirmasi Pengembalian & Denda** — Mengonfirmasi pengembalian barang, mencatat tanggal kembali aktual, menghitung **Sistem Denda Keterlambatan** otomatis (Rp 5.000 / hari), dan memperbarui stok secara otomatis.
- **Laporan Denda** — Menampilkan rekapitulasi data denda keterlambatan mahasiswa beserta total akumulasi denda.
- **Manajemen Mahasiswa** — CRUD akun mahasiswa.
- **Riwayat Transaksi** — Melihat seluruh log aktivitas peminjaman dan pengembalian lengkap dengan tanggal aktual dikembalikan dan jumlah denda.
- **Laporan Administratif (Export CSV)** — Mengunduh laporan instan dalam format CSV untuk Riwayat Transaksi, Inventaris Barang, dan Rekapitulasi Denda.

### 🎓 Role Mahasiswa
- **Dashboard** — Ringkasan peminjaman aktif, permintaan tertunda, dan total riwayat.
- **Katalog Barang** — Melihat daftar barang yang tersedia dengan visualisasi gambar barang, pencarian, dan filter kategori.
- **Ajukan Peminjaman** — Mengajukan permintaan peminjaman dengan menentukan jumlah dan tanggal peminjaman/pengembalian.
- **Ajukan Pengembalian** — Mengirim permintaan pengembalian barang yang sudah disetujui.
- **Riwayat Pribadi** — Melihat seluruh riwayat peminjaman milik sendiri lengkap dengan informasi denda jika terlambat mengembalikan barang.

---

## Alur Kerja Sistem

```
Mahasiswa Mengajukan ──► [pending]
        │
        ▼ (Admin)
   Disetujui ──► [approved]  ← Stok berkurang
   Ditolak  ──► [rejected]   ← Stok tidak berubah
        │
        ▼ (Mahasiswa)
Ajukan Pengembalian ──► [return_requested]
        │
        ▼ (Admin)
Konfirmasi Pengembalian ──► [returned]  ← Stok bertambah kembali
```

---

## Cara Instalasi & Menjalankan

### Prasyarat
Pastikan perangkat Anda sudah terinstall:
- **PHP** >= 8.2
- **Composer**
- **Node.js** & **npm**

### Langkah Instalasi

**1. Clone repositori & masuk ke direktori proyek**
```bash
git clone https://github.com/nikamushi/labtrack.git
cd labtrack
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi Node.js**
```bash
npm install
```

**4. Salin file konfigurasi environment**
```bash
cp .env.example .env
```

**5. Generate application key**
```bash
php artisan key:generate
```

**6. Jalankan migrasi dan seeder database**
```bash
php artisan migrate:fresh --seed
```

**7. Jalankan server pengembangan (buka 2 terminal terpisah)**

*Terminal 1 — Backend Laravel:*
```bash
php artisan serve
```

*Terminal 2 — Frontend Vite (Tailwind CSS):*
```bash
npm run dev
```

**8. Buka aplikasi di browser**
```
http://127.0.0.1:8000
```

---

## Akun Default (Setelah Seeding)

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@labtrack.com` | `password` |
| **Mahasiswa** | `student@labtrack.com` | `password` |

---

## Menjalankan Pengujian Otomatis

Proyek ini dilengkapi dengan 39 skenario pengujian otomatis menggunakan **Pest PHP** yang mencakup:
- Autentikasi & Autorisasi berbasis role
- CRUD Kategori, Barang, dan Mahasiswa
- Alur peminjaman & pengembalian secara end-to-end
- Sinkronisasi stok barang
- Pengunggahan gambar barang (`Item Image Upload`)
- Sistem kalkulasi denda keterlambatan pengembalian
- Ekspor laporan dalam format CSV

```bash
php artisan test
```

Hasil yang diharapkan: **39/39 tests PASSED**

---

## Struktur Direktori Utama

```
labtrack/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminStudentController.php
│   │   │   ├── BorrowingController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── FineController.php
│   │   │   ├── HistoryController.php
│   │   │   ├── ItemController.php
│   │   │   └── ReturnController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   └── Models/
│       ├── Borrowing.php
│       ├── Category.php
│       ├── Item.php
│       └── User.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/       ← Semua halaman panel Admin
│       ├── student/     ← Semua halaman panel Mahasiswa
│       ├── auth/        ← Halaman Login
│       └── layouts/     ← Layout utama (Sidebar + Navbar)
├── routes/
│   └── web.php
└── tests/
    └── Feature/
        └── LabTrackTest.php
```

---


## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
