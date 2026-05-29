# 🏛️ Pinarak Yogyakarta
### Website Pariwisata Kota Istimewa Yogyakarta

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

> Portal informasi wisata Kota Yogyakarta yang menampilkan destinasi terbaik lengkap dengan sistem manajemen konten berbasis PHP & MySQL.

🌐 **Live Demo:** [wisatayogyakarta.free.nf](https://wisatayogyakarta.free.nf)

---

## 📸 Preview

| Halaman Utama | Panel Admin |
|---|---|
| Hero section dengan foto Tugu Jogja | Dashboard statistik & CRUD |
| Grid destinasi wisata | Kelola data wisata & kategori |
| Form komentar & rating | Manajemen komentar pengunjung |

---

## ✨ Fitur Utama

### 👥 Halaman Pengunjung
- 🏠 **Hero Section** — Foto Tugu Jogja dengan animasi floating card
- 🔍 **Pencarian Realtime** — Cari wisata berdasarkan nama & lokasi
- 🏷️ **Filter Kategori** — Filter wisata berdasarkan kategori
- 📋 **Modal Detail** — Popup detail wisata dengan peta Google Maps
- 💬 **Komentar & Rating** — Pengunjung bisa beri komentar & rating bintang
- 📱 **Responsive** — Tampilan optimal di HP, tablet, dan desktop
- 📬 **Halaman Kontak** — Form kontak lengkap dengan info tim
- 🔒 **Kebijakan Privasi** — Modal popup kebijakan privasi

### 🔐 Panel Admin
- 🔑 **Login Aman** — Autentikasi dengan password bcrypt
- 📊 **Dashboard** — Statistik total wisata, kategori & komentar
- ➕ **Tambah Wisata** — Input data + upload gambar
- ✏️ **Edit Wisata** — Update data & ganti gambar
- 🗑️ **Hapus Wisata** — Delete data + otomatis hapus gambar
- 🔎 **Pencarian & Filter** — Cari data di panel admin
- 📄 **Pagination** — Tampilan data per halaman
- 🔄 **Toggle Status** — Aktif/nonaktif wisata dengan 1 klik
- 🏷️ **Kelola Kategori** — CRUD kategori wisata
- 💬 **Kelola Komentar** — Moderasi komentar pengunjung
- 👤 **Profil Admin** — Update nama & ganti password

---

## 🗂️ Struktur Folder

```
wisata_yogyakarta/
│
├── index.php                  ← Halaman utama pengunjung
├── kontak.php                 ← Halaman kontak
├── database.sql               ← File database (import via phpMyAdmin)
│
├── includes/
│   ├── config.php             ← Konfigurasi koneksi database
│   └── auth.php               ← Fungsi autentikasi & helper
│
├── admin/
│   ├── login.php              ← Login admin
│   ├── logout.php             ← Logout
│   ├── dashboard.php          ← Dashboard statistik
│   ├── wisata.php             ← List & hapus wisata
│   ├── wisata_tambah.php      ← Form tambah wisata
│   ├── wisata_edit.php        ← Form edit wisata
│   ├── kategori.php           ← CRUD kategori
│   ├── komentar.php           ← Kelola komentar
│   ├── profil.php             ← Profil admin
│   └── includes/
│       ├── sidebar.php        ← Template sidebar
│       └── footer_admin.php   ← Penutup template
│
├── ajax/
│   └── get_wisata.php         ← Endpoint detail wisata (AJAX)
│
├── assets/
│   ├── css/
│   │   ├── style.css          ← CSS halaman pengunjung
│   │   └── admin.css          ← CSS panel admin
│   ├── js/
│   │   ├── main.js            ← JS halaman pengunjung
│   │   └── admin.js           ← JS panel admin
│   └── images/
│       └── default.jpg        ← Gambar placeholder
│
└── uploads/
    └── wisata/                ← Folder upload gambar wisata
```

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| **PHP 7.4+** | Backend & server-side logic |
| **MySQL / MariaDB** | Database |
| **PDO** | Koneksi database (anti SQL Injection) |
| **HTML5** | Struktur halaman |
| **CSS3** | Styling & animasi |
| **Vanilla JavaScript** | Interaktivitas & AJAX |
| **Font Awesome 6** | Ikon |
| **Google Fonts** | Tipografi (Playfair Display + Nunito) |
| **Apache (XAMPP)** | Web server lokal |

---

## 🚀 Cara Instalasi (XAMPP)

### 1. Clone Repository
```bash
git clone https://github.com/soeryosoekmo8-cyber/wisata-yogyakarta.git
```

### 2. Copy ke htdocs
```
C:\xampp\htdocs\wisata_yogyakarta\
```

### 3. Import Database
- Buka `http://localhost/phpmyadmin`
- Buat database baru: `wisata_yogyakarta`
- Import file `database.sql`

### 4. Sesuaikan Config
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wisata_yogyakarta');
define('BASE_URL', 'http://localhost/wisata_yogyakarta');
```

### 5. Akses Website
- **Pengunjung:** `http://localhost/wisata_yogyakarta/`
- **Admin:** `http://localhost/wisata_yogyakarta/admin/login.php`

---

## 🔐 Akun Demo

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `password` |

> ⚠️ Ganti password setelah pertama kali login!

---

## 🔒 Keamanan

- ✅ Password di-hash dengan **bcrypt** (`password_hash`)
- ✅ **Prepared Statement PDO** — anti SQL Injection
- ✅ **Session management** yang aman
- ✅ Validasi tipe & ukuran file upload
- ✅ Sanitasi input dengan `htmlspecialchars()`
- ✅ Session regenerate setelah login
- ✅ `.htaccess` memblokir eksekusi PHP di folder uploads

---

## 👥 Tim Pembuat

| No | Nama | Role |
|---|---|---|
| 1 | **Alexander Bintang Nugraha** | Developer |
| 2 | **Desvian Angga Saputra** | Developer |
| 3 | **Rafiq Maulana** | Developer |
| 4 | **Soeryo Soekmo Seto S.G** | Developer |

**Kelas:** XI TKJ 1  
**Sekolah:** SMK Negeri 1 Seyegan  
**Mata Pelajaran:** PKDK  
**Tahun Ajaran:** 2025/2026  

---

## 📄 Lisensi

Project ini dibuat untuk keperluan tugas sekolah. Bebas digunakan sebagai referensi pembelajaran.

---

<div align="center">
  <p>Dibuat dengan ❤️ untuk Kota Istimewa Yogyakarta</p>
  <p>© 2026 Pinarak Yogyakarta — SMK Negeri 1 Seyegan</p>
</div>
