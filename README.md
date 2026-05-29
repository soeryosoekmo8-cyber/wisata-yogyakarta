# 🏛️ Website Pariwisata Yogyakarta
**Sistem Informasi Wisata Kota Istimewa Yogyakarta**

---

## 📁 Struktur Folder

```
wisata_yogyakarta/
│
├── index.php                  ← Halaman utama pengunjung
├── database.sql               ← File database (import via phpMyAdmin)
│
├── includes/
│   ├── config.php             ← Konfigurasi koneksi database
│   └── auth.php               ← Fungsi login, logout, helper
│
├── admin/
│   ├── login.php              ← Halaman login admin
│   ├── logout.php             ← Proses logout
│   ├── dashboard.php          ← Dashboard statistik admin
│   ├── wisata.php             ← CRUD list & hapus wisata
│   ├── wisata_tambah.php      ← Form tambah wisata
│   ├── wisata_edit.php        ← Form edit wisata
│   ├── kategori.php           ← CRUD kategori
│   ├── komentar.php           ← Kelola komentar
│   ├── profil.php             ← Profil & ganti password
│   └── includes/
│       ├── sidebar.php        ← Template sidebar admin
│       └── footer_admin.php   ← Penutup template admin
│
├── ajax/
│   └── get_wisata.php         ← Endpoint detail wisata (modal)
│
├── assets/
│   ├── css/
│   │   ├── style.css          ← CSS halaman pengunjung
│   │   └── admin.css          ← CSS halaman admin
│   ├── js/
│   │   ├── main.js            ← JS halaman pengunjung
│   │   └── admin.js           ← JS halaman admin
│   └── images/
│       └── default.jpg        ← Gambar placeholder default
│
└── uploads/
    └── wisata/                ← Folder upload gambar wisata
        └── .htaccess          ← (Keamanan: tolak eksekusi PHP)
```

---

## 🚀 Cara Instalasi di XAMPP (Server Sekolah)

### Langkah 1 — Persiapkan XAMPP
1. Pastikan **XAMPP** sudah terinstall dan berjalan
2. Start **Apache** dan **MySQL** dari XAMPP Control Panel

### Langkah 2 — Copy File ke htdocs
1. Copy seluruh folder `wisata_yogyakarta` ke:
   ```
   C:\xampp\htdocs\wisata_yogyakarta\
   ```

### Langkah 3 — Import Database via phpMyAdmin
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik **"New"** (buat database baru)
3. Nama database: `wisata_yogyakarta`, klik **Create**
4. Pilih database tersebut, klik tab **Import**
5. Klik **Choose File**, pilih file `database.sql`
6. Klik tombol **Import** / **Go**
7. Tunggu hingga muncul pesan sukses ✅

### Langkah 4 — Sesuaikan Konfigurasi
Edit file `includes/config.php` jika perlu:
```php
define('DB_HOST', 'localhost');   // Host database
define('DB_USER', 'root');        // Username MySQL (default: root)
define('DB_PASS', '');            // Password MySQL (default: kosong)
define('DB_NAME', 'wisata_yogyakarta');
define('BASE_URL', 'http://localhost/wisata_yogyakarta');
```

### Langkah 5 — Akses Website
- **Halaman Pengunjung:** `http://localhost/wisata_yogyakarta/`
- **Halaman Admin:** `http://localhost/wisata_yogyakarta/admin/login.php`

---

## 🔐 Akun Login Admin Default

| Field    | Nilai        |
|----------|-------------|
| Username | `admin`      |
| Password | `password`   |

> ⚠️ **PENTING:** Segera ganti password setelah pertama kali login melalui menu **Profil Admin**!

---

## 🎯 Fitur Website

### Halaman Pengunjung (Publik)
- ✅ Tampilan hero section yang menarik dengan animasi
- ✅ Grid kartu destinasi wisata
- ✅ Filter wisata berdasarkan kategori
- ✅ Pencarian realtime nama/lokasi wisata
- ✅ Modal popup detail wisata (AJAX)
- ✅ Halaman tentang Yogyakarta
- ✅ Footer lengkap dengan info kontak
- ✅ Responsif (mobile-friendly)

### Panel Admin (Login Diperlukan)
- ✅ Dashboard statistik (total wisata, kategori, komentar)
- ✅ **CREATE** — Tambah wisata baru + upload gambar
- ✅ **READ** — Lihat semua data wisata dengan tabel
- ✅ **UPDATE** — Edit data & ganti gambar wisata
- ✅ **DELETE** — Hapus wisata (gambar otomatis ikut terhapus)
- ✅ Pencarian & filter data wisata
- ✅ Pagination (10 data per halaman)
- ✅ Toggle status aktif/nonaktif wisata
- ✅ CRUD kategori wisata
- ✅ Kelola komentar pengunjung
- ✅ Profil admin & ganti password

---

## 🛡️ Keamanan
- Password di-hash dengan `password_hash()` (bcrypt)
- Prepared Statement PDO (anti SQL Injection)
- Session management yang aman
- Validasi tipe & ukuran file upload
- Sanitasi input dengan `htmlspecialchars()`
- Session regenerate setelah login

---

## 🔧 Troubleshooting

**❌ "Koneksi Database Gagal"**
→ Pastikan XAMPP MySQL sudah aktif dan database sudah diimport

**❌ Upload gambar tidak berhasil**
→ Pastikan folder `uploads/wisata/` memiliki permission write (755)
→ Di XAMPP Windows: biasanya sudah otomatis OK

**❌ Halaman tidak ditemukan (404)**
→ Pastikan folder bernama persis `wisata_yogyakarta` di `htdocs`
→ Periksa BASE_URL di `includes/config.php`

**❌ Modal detail tidak muncul**
→ Buka DevTools browser (F12), cek tab Console untuk error
→ Pastikan file `ajax/get_wisata.php` ada

---

## 💡 Teknologi yang Digunakan
- **Backend:** PHP 7.4+ dengan PDO
- **Database:** MySQL 5.7+ / MariaDB 10+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Ikon:** Font Awesome 6
- **Font:** Google Fonts (Playfair Display + Nunito)
- **Server:** Apache (XAMPP)

---

*Dibuat untuk tugas Website Pariwisata Yogyakarta*
*© 2024 — Sistem Informasi Wisata Kota Istimewa*
