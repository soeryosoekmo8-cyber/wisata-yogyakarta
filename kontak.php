<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$sukses = '';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($pesan)) {
        $error = 'Nama, email, dan pesan wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
      $stmt = $pdo->prepare("INSERT INTO pesan_kontak (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)");
      $stmt->execute([$nama, $email, $subjek, $pesan]);
      $sukses = 'Pesan kamu berhasil dikirim! Kami akan segera menghubungi kamu. 😊';
    }
}

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontak - Pinarak Yogyakarta</title>
  <base href="https://wisatayogyakarta.free.nf/">
  <link rel="icon" type="image/jpeg" href="assets/images/logo.jpeg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .kontak-hero {
      background: linear-gradient(135deg, #0a1a0f 0%, #1a472a 50%, #0d2b1a 100%);
      padding: 80px 0 60px;
      text-align: center;
    }
    .kontak-hero h1 { color: #fff; font-size: 2.5rem; margin-bottom: 12px; }
    .kontak-hero p  { color: rgba(255,255,255,.7); font-size: 1rem; }
    .kontak-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; align-items: start; }
    .kontak-info-card { background: var(--bg-card); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow); }
    .kontak-info-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--border); }
    .kontak-info-item:last-child { border-bottom: none; }
    .kontak-info-icon { width: 42px; height: 42px; background: rgba(26,71,42,.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1rem; flex-shrink: 0; }
    .kontak-info-label { font-size: .78rem; color: var(--text-lt); margin-bottom: 4px; }
    .kontak-info-value { font-size: .9rem; font-weight: 600; color: var(--dark); }
    .form-kontak-card { background: var(--bg-card); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow); }
    .form-kontak-title { font-family: var(--font-head); font-size: 1.3rem; color: var(--dark); margin-bottom: 24px; padding-bottom: 14px; border-bottom: 2px solid var(--border); }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: .83rem; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%; padding: 11px 16px; border: 2px solid var(--border); border-radius: var(--radius);
      font-family: var(--font-body); font-size: .9rem; color: var(--text); background: var(--bg); outline: none; transition: border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--primary); background: #fff; }
    .form-group textarea { resize: vertical; min-height: 130px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .btn-kirim {
      width: 100%; padding: 13px; background: var(--primary); color: #fff; border: none;
      border-radius: var(--radius); font-family: var(--font-body); font-size: 1rem; font-weight: 700;
      cursor: pointer; transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-kirim:hover { background: var(--primary-lt); transform: translateY(-2px); }
    @media (max-width: 768px) {
      .kontak-grid { grid-template-columns: 1fr; }
      .form-row-2  { grid-template-columns: 1fr; }
      .kontak-hero h1 { font-size: 1.8rem; }
    }
  </style>
</head>
<body>

<header id="header">
  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo" style="background:none; padding:0; flex-shrink:0;">
        <img src="assets/images/logo.jpeg" style="width:46px;height:46px;object-fit:cover;border-radius:50%;display:block;" alt="Logo">
      </div>
      <div>
        <div class="brand-name">Pinarak Yogyakarta</div>
        <div class="brand-sub">Yogyakarta Kota Istimewa</div>
      </div>
    </div>
    <ul id="nav-menu">
      <li><a href="index.php" class="nav-link">Beranda</a></li>
      <li><a href="index.php#wisata" class="nav-link">Destinasi</a></li>
      <li><a href="index.php#tentang" class="nav-link">Tentang</a></li>
      <li><a href="kontak.php" class="nav-link active">Kontak</a></li>
      <li><a href="admin/login.php" class="nav-link btn-admin-link"><i class="fas fa-lock"></i> Admin</a></li>
    </ul>
    <div id="hamburger"><span></span><span></span><span></span></div>
  </nav>
</header>

<div class="kontak-hero">
  <div class="container">
    <div class="hero-badge" style="justify-content:center; margin-bottom:16px;">📬 Hubungi Kami</div>
    <h1>Ada Pertanyaan?</h1>
    <p>Kami siap membantu Anda menemukan destinasi wisata terbaik di Yogyakarta</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="kontak-grid">

      <div class="kontak-info-card animate-on-scroll">
        <h3 style="font-family:var(--font-head); font-size:1.1rem; color:var(--dark); margin-bottom:6px;">Informasi Kontak</h3>
        <p style="font-size:.83rem; color:var(--text-lt); margin-bottom:4px;">Jangan ragu untuk menghubungi kami</p>

        <div class="kontak-info-item">
          <div class="kontak-info-icon"><i class="fas fa-envelope"></i></div>
          <div>
            <div class="kontak-info-label">Email</div>
            <div class="kontak-info-value">pinarakyogyakarta@gmail.com</div>
          </div>
        </div>

        <div class="kontak-info-item">
          <div class="kontak-info-icon"><i class="fas fa-phone"></i></div>
          <div>
            <div class="kontak-info-label">Telepon / WhatsApp</div>
            <div class="kontak-info-value">+62-85177295881</div>
            <div class="kontak-info-value">+62-8994157318</div>
            <div class="kontak-info-value">+62-81290489849</div>
            <div class="kontak-info-value">+62-895336751785</div>
          </div>
        </div>

        <div class="kontak-info-item">
          <div class="kontak-info-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div>
            <div class="kontak-info-label">Alamat</div>
            <div class="kontak-info-value">Kota Yogyakarta, DIY, Indonesia</div>
          </div>
        </div>

        <div class="kontak-info-item">
          <div class="kontak-info-icon"><i class="fas fa-clock"></i></div>
          <div>
            <div class="kontak-info-label">Jam Operasional</div>
            <div class="kontak-info-value">Senin - Jumat: 08.00 - 15.00</div>
            <div class="kontak-info-value">Sabtu: 08.00 - 13.00</div>
          </div>
        </div>

        <div style="margin-top:20px;">
          <div style="font-size:.83rem; font-weight:600; color:var(--text); margin-bottom:12px;">Ikuti Kami</div>
          <div style="display:flex; gap:10px;">
            <a href="#" style="width:38px;height:38px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" style="width:38px;height:38px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" style="width:38px;height:38px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;">
              <i class="fab fa-tiktok"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="form-kontak-card animate-on-scroll">
        <h3 class="form-kontak-title">✉️ Kirim Pesan</h3>

        <?php if ($sukses): ?>
        <div style="background:#d1fae5;color:#065f46;border-left:4px solid #10b981;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-weight:600;">
          ✅ <?= htmlspecialchars($sukses) ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div style="background:#fee2e2;color:#991b1b;border-left:4px solid #ef4444;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-weight:600;">
          ❌ <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="kontak.php">
          <div class="form-row-2">
            <div class="form-group">
              <label>Nama Lengkap <span style="color:red">*</span></label>
              <input type="text" name="nama" placeholder="Nama kamu" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Email <span style="color:red">*</span></label>
              <input type="email" name="email" placeholder="email@contoh.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label>Subjek</label>
            <select name="subjek">
              <option value="Informasi Wisata">Informasi Wisata</option>
              <option value="Kerjasama">Kerjasama</option>
              <option value="Laporan Masalah">Laporan Masalah</option>
              <option value="Saran & Masukan">Saran & Masukan</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div class="form-group">
            <label>Pesan <span style="color:red">*</span></label>
            <textarea name="pesan" placeholder="Tulis pesanmu di sini..."><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn-kirim">
            <i class="fas fa-paper-plane"></i> Kirim Pesan
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<footer id="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
          <img src="assets/images/logo.jpeg" style="width:38px;height:38px;object-fit:cover;border-radius:50%;display:block;flex-shrink:0;" alt="Logo">
          <div class="brand-name">Pinarak Yogyakarta</div>
        </div>
        <p class="footer-desc">Portal resmi informasi wisata Kota Yogyakarta. Temukan destinasi terbaik dan buat kenangan indah bersama orang-orang tersayang.</p>
      </div>
      <div>
        <h4 class="footer-heading">Navigasi</h4>
        <ul class="footer-links">
          <li><a href="index.php">Beranda</a></li>
          <li><a href="index.php#wisata">Destinasi</a></li>
          <li><a href="index.php#tentang">Tentang Kami</a></li>
          <li><a href="kontak.php">Kontak</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer-heading">Kategori Wisata</h4>
        <ul class="footer-links">
          <?php foreach ($kategoriList as $kat): ?>
          <li><a href="index.php?kat=<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="footer-heading">Hubungi Kami</h4>
        <ul class="footer-links">
          <li><a href="mailto:pinarakyogyakarta@gmail.com">pinarakyogyakarta@gmail.com</a></li>
          <li><a href="tel:+6285177295881">+62-85177295881</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <span>© <?= date('Y') ?> Pinarak Yogyakarta. Dibuat dengan ❤️ untuk Kota Istimewa.</span>
      <span>Hak Cipta Dilindungi | Dinas Pariwisata Yogyakarta</span>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
