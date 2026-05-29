<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$search = trim($_GET['q'] ?? '');
$katFilter = intval($_GET['kat'] ?? 0);

$sql = "SELECT w.*, k.nama AS kategori FROM wisata w JOIN kategori k ON w.kategori_id = k.id WHERE w.status = 'aktif'";
$params = [];

if ($search) {
    $sql .= " AND (w.nama LIKE ? OR w.lokasi LIKE ? OR w.deskripsi LIKE ?)";
    $like = "%$search%";
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($katFilter > 0) {
    $sql .= " AND w.kategori_id = ?";
    $params[] = $katFilter;
}

$sql .= " ORDER BY w.rating DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$wisataList = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id")->fetchAll();

$totalWisata = $pdo->query("SELECT COUNT(*) FROM wisata WHERE status='aktif'")->fetchColumn();
$totalKat    = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();

$ip = $_SERVER['REMOTE_ADDR'];
$hari_ini = date('Y-m-d');
$pdo->prepare("INSERT IGNORE INTO pengunjung (ip, tanggal) VALUES (?, ?)")->execute([$ip, $hari_ini]);
$totalPengunjung = $pdo->query("SELECT COUNT(DISTINCT ip) FROM pengunjung")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pinarak Yogyakarta - Explore Kota Istimewa</title>
  <base href="https://wisatayogyakarta.free.nf/">
  <link rel="icon" type="image/jpeg" href="assets/images/logo.jpeg">
  <meta name="description" content="Temukan keindahan dan keajaiban wisata di Kota Yogyakarta - surga budaya, alam, dan kuliner.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header id="header">
  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo" style="background:none; padding:0; flex-shrink:0;">
        <img src="assets/images/logo.jpeg" 
             style="width:46px; height:46px; object-fit:cover; border-radius:50%; display:block;" 
             alt="Logo">
      </div>
      <div>
        <div class="brand-name">Pinarak Yogyakarta</div>
        <div class="brand-sub">Yogyakarta Kota Istimewa</div>
      </div>
    </div>

    <ul id="nav-menu">
      <li><a href="#hero"     class="nav-link active">Beranda</a></li>
      <li><a href="#wisata"   class="nav-link">Destinasi</a></li>
      <li><a href="#kategori" class="nav-link">Kategori</a></li>
      <li><a href="#tentang"  class="nav-link">Tentang</a></li>
      <li><a href="admin/login.php" class="nav-link btn-admin-link"><i class="fas fa-lock"></i> Admin</a></li>
    </ul>

    <div id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </div>
  </nav>
</header>

<section id="hero">
  <div class="hero-pattern"></div>
  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge">✨ Destinasi Terbaik Indonesia</div>
      <h1 class="hero-title">
        Jelajahi Pesona<br>
        <span class="accent">Yogyakarta</span>
      </h1>
      <p class="hero-desc">
        Di Yogyakarta—kota istimewa yang merangkai keajaiban budaya, alam, dan rasa. 
        Dari candi yang megah hingga pantai yang berbisik tenang, setiap sudutnya menyimpan cerita yang menunggu untuk ditemukan.
      </p>
      <div class="hero-cta">
        <a href="#wisata" class="btn-primary">
          <i class="fas fa-compass"></i> Jelajahi Sekarang
        </a>
        <a href="#tentang" class="btn-outline">
          <i class="fas fa-info-circle"></i> Pelajari Lebih
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num" data-count="<?= $totalWisata ?>"><?= $totalWisata ?>+</div>
          <div class="stat-label">Destinasi</div>
        </div>
        <div class="stat-item">
          <div class="stat-num" data-count="<?= $totalKat ?>"><?= $totalKat ?>+</div>
          <div class="stat-label">Kategori</div>
        </div>
        <div class="stat-item">
          <div class="stat-num" data-count="<?= $totalPengunjung ?>"><?= number_format($totalPengunjung) ?></div>
          <div class="stat-label">Pengunjung</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-img-wrap" style="padding:0; background:none; overflow:hidden;">
        <img src="uploads/wisata/hero.jpeg" 
             style="width:100%; height:100%; object-fit:cover; border-radius:20px;" 
             alt="Wisata Jogja">
      </div>
    </div>
  </div>
</section>

<section id="wisata" class="section">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <div class="section-badge">✈️ Destinasi Wisata</div>
      <h2 class="section-title">Tempat Wisata <span class="accent">Terpopuler</span></h2>
      <p class="section-desc">Deretan destinasi wisata terbaik yang siap memanjakan mata dan jiwa Anda di Kota Yogyakarta.</p>
    </div>

    <form method="GET" action="index.php#wisata" class="search-wrap animate-on-scroll">
      <input type="text" id="search-input" name="q" 
             placeholder="🔍 Cari nama tempat wisata..."
             value="<?= htmlspecialchars($search) ?>">
      <button type="submit" id="search-btn">
        <i class="fas fa-search"></i>
      </button>
    </form>

    <div class="kategori-filter animate-on-scroll" id="kategori">
      <a href="index.php#wisata" class="filter-btn <?= $katFilter === 0 ? 'active' : '' ?>" data-kategori="semua">
        🌟 Semua
      </a>
      <?php foreach ($kategoriList as $kat): ?>
      <a href="?kat=<?= $kat['id'] ?>#wisata" 
         class="filter-btn <?= $katFilter === (int)$kat['id'] ? 'active' : '' ?>"
         data-kategori="<?= $kat['id'] ?>">
        <i class="fas <?= htmlspecialchars($kat['ikon']) ?>"></i>
        <?= htmlspecialchars($kat['nama']) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($wisataList)): ?>
    <div style="text-align:center; padding:60px 20px; color: var(--text-lt);">
      <div style="font-size:4rem; margin-bottom:16px;">😔</div>
      <h3>Tidak ada hasil ditemukan</h3>
      <p>Coba kata kunci lain atau <a href="index.php" style="color:var(--primary)">reset pencarian</a></p>
    </div>
    <?php else: ?>
    <div class="wisata-grid">
      <?php foreach ($wisataList as $w): ?>
      <article class="card-wisata animate-on-scroll" data-kategori="<?= $w['kategori_id'] ?>">
        <div class="card-img">
          <?php
            $imgPath = 'uploads/wisata/' . $w['gambar'];
            $imgSrc  = file_exists($imgPath) ? $imgPath : 'assets/images/default.jpg';
          ?>
          <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($w['nama']) ?>" loading="lazy">
          <span class="card-badge"><?= htmlspecialchars($w['kategori']) ?></span>
          <span class="card-rating"><span class="star">⭐</span> <?= $w['rating'] ?></span>
        </div>
        <div class="card-body">
          <h3 class="card-title"><?= htmlspecialchars($w['nama']) ?></h3>
          <p class="card-lokasi"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($w['lokasi']) ?></p>
          <p class="card-desc"><?= htmlspecialchars($w['deskripsi']) ?></p>
          <div class="card-footer">
            <div>
              <div class="card-harga"><i class="fas fa-ticket-alt"></i> <?= htmlspecialchars($w['harga_tiket']) ?></div>
              <div class="card-jam"><i class="fas fa-clock"></i> <?= htmlspecialchars($w['jam_buka']) ?></div>
            </div>
            <button class="btn-detail" data-open-modal="<?= $w['id'] ?>">Lihat Detail</button>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<section id="tentang" class="section section-alt">
  <div class="container">

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center; margin-bottom:60px;">
      <div class="animate-on-scroll">
        <div class="section-badge">📖 Tentang Yogyakarta</div>
        <h2 class="section-title" style="text-align:left; margin-top:12px;">Kota <span class="accent">Budaya</span><br>yang Tak Terlupakan</h2>
        <p style="color:var(--text-lt); margin:20px 0; line-height:1.9;">
          Yogyakarta atau yang akrab disapa "Jogja" adalah kota istimewa yang terletak di Pulau Jawa, Indonesia. 
          Dikenal sebagai pusat seni, budaya, dan pendidikan, Yogyakarta menawarkan perpaduan unik antara 
          warisan keraton Jawa yang kaya, alam yang memesona, dan modernitas yang dinamis.
        </p>
        <p style="color:var(--text-lt); line-height:1.9;">
          Dari megahnya Candi Prambanan, keindahan Pantai Parangtritis, hingga kesibukan 
          Malioboro yang ikonik — setiap sudut Jogja menyimpan cerita dan keindahan tersendiri.
        </p>
        <div style="display:flex; gap:24px; margin-top:28px; flex-wrap:wrap;">
          <div style="text-align:center;">
            <div style="font-family:var(--font-head); font-size:2rem; font-weight:700; color:var(--primary);">2 UNESCO</div>
            <div style="font-size:.8rem; color:var(--text-lt);">World Heritage Sites</div>
          </div>
          <div style="text-align:center;">
            <div style="font-family:var(--font-head); font-size:2rem; font-weight:700; color:var(--primary);">5+</div>
            <div style="font-size:.8rem; color:var(--text-lt);">Kategori Wisata</div>
          </div>
          <div style="text-align:center;">
            <div style="font-family:var(--font-head); font-size:2rem; font-weight:700; color:var(--primary);">3.6 Juta</div>
            <div style="font-size:.8rem; color:var(--text-lt);">Wisatawan/Tahun</div>
          </div>
        </div>
      </div>
      <div class="animate-on-scroll" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <?php
        $icons = ['🏛️','🏔️','🌊','🍜'];
        $labels = ['Budaya & Sejarah','Wisata Alam','Pantai Eksotis','Kuliner Khas'];
        foreach ($icons as $i => $ic): ?>
        <div style="background:var(--bg-card); border-radius:var(--radius); padding:24px; text-align:center; box-shadow:var(--shadow); transition:.3s;" 
             onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform=''">
          <div style="font-size:2.5rem; margin-bottom:10px;"><?= $ic ?></div>
          <div style="font-size:.85rem; font-weight:600; color:var(--dark);"><?= $labels[$i] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="tentang-kami" style="display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start;">

      <div class="animate-on-scroll" style="background:var(--bg-card); border-radius:var(--radius-lg); padding:32px; box-shadow:var(--shadow);">
        <div class="section-badge" style="margin-bottom:14px;">🌐 Tentang Website</div>
        <h3 style="font-family:var(--font-head); font-size:1.3rem; color:var(--dark); margin-bottom:16px;">
          Pinarak Yogyakarta
        </h3>
        <p style="color:var(--text-lt); line-height:1.9; margin-bottom:16px;">
          <strong>Pinarak Yogyakarta</strong> adalah portal informasi wisata yang dibuat untuk memudahkan 
          wisatawan menemukan dan menjelajahi destinasi terbaik di Kota Yogyakarta.
        </p>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px; font-size:.88rem;">
            <span style="color:var(--primary); font-size:1rem;">✅</span>
            <span>Informasi wisata terlengkap & terpercaya</span>
          </div>
          <div style="display:flex; align-items:center; gap:10px; font-size:.88rem;">
            <span style="color:var(--primary); font-size:1rem;">✅</span>
            <span>Data selalu diperbarui oleh admin</span>
          </div>
          <div style="display:flex; align-items:center; gap:10px; font-size:.88rem;">
            <span style="color:var(--primary); font-size:1rem;">✅</span>
            <span>Gratis & mudah diakses semua kalangan</span>
          </div>
          <div style="display:flex; align-items:center; gap:10px; font-size:.88rem;">
            <span style="color:var(--primary); font-size:1rem;">✅</span>
            <span>Tersedia fitur pencarian & filter kategori</span>
          </div>
        </div>
        <div style="margin-top:20px; padding:16px; background:var(--bg); border-radius:var(--radius); border-left:4px solid var(--primary);">
          <div style="font-size:.78rem; color:var(--text-lt); margin-bottom:4px;">📚 Mata Pelajaran</div>
          <div style="font-size:.92rem; font-weight:700; color:var(--dark);">PKDK</div>
          <div style="font-size:.78rem; color:var(--text-lt); margin-top:8px; margin-bottom:4px;">🏫 Sekolah</div>
          <div style="font-size:.92rem; font-weight:700; color:var(--dark);">SMK Negeri 1 Seyegan</div>
          <div style="font-size:.78rem; color:var(--text-lt); margin-top:8px; margin-bottom:4px;">📅 Tahun Ajaran</div>
          <div style="font-size:.92rem; font-weight:700; color:var(--dark);">2026/2027</div>
        </div>
      </div>

      <div class="animate-on-scroll" style="background:var(--bg-card); border-radius:var(--radius-lg); padding:32px; box-shadow:var(--shadow);">
        <div class="section-badge" style="margin-bottom:14px;">👥 Tim Pembuat</div>
        <h3 style="font-family:var(--font-head); font-size:1.3rem; color:var(--dark); margin-bottom:6px;">
          Kelompok XI - TKJ 1
        </h3>
        <p style="font-size:.83rem; color:var(--text-lt); margin-bottom:20px;">SMK Negeri 1 Seyegan</p>

        <?php
        $anggota = [
          ['nama' => 'Alexander Bintang Nugraha', 'inisial' => 'AB'],
          ['nama' => 'Desvian Angga Saputra',     'inisial' => 'DA'],
          ['nama' => 'Rafiq Maulana',              'inisial' => 'RM'],
          ['nama' => 'Soeryo Soekmo Seto S.G',     'inisial' => 'SS'],
        ];
        foreach ($anggota as $no => $a): ?>
        <div style="display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid var(--border);">
          <div style="width:44px; height:44px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:.9rem; flex-shrink:0;">
            <?= $a['inisial'] ?>
          </div>
          <div>
            <div style="font-size:.88rem; font-weight:700; color:var(--dark);"><?= $a['nama'] ?></div>
            <div style="font-size:.75rem; color:var(--text-lt);">Anggota <?= $no+1 ?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <div style="margin-top:16px; text-align:center; padding:12px; background:rgba(26,71,42,.06); border-radius:var(--radius);">
          <div style="font-size:.82rem; color:var(--primary); font-weight:600;">
            🎓 Tugas Mata Pelajaran PKDK
          </div>
          <div style="font-size:.78rem; color:var(--text-lt); margin-top:4px;">
            SMK Negeri 1 Seyegan · XI TKJ 1 · 2026/2027
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal-overlay" id="modal-wisata">
  <div class="modal-box">
    <div class="modal-img">
      <img id="modal-img" src="" alt="">
    </div>
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2 class="modal-title" id="modal-nama"></h2>
          <span style="color:var(--text-lt); font-size:.85rem;" id="modal-kategori"></span>
        </div>
        <button class="modal-close" id="modal-close">✕</button>
      </div>
      <div class="modal-meta">
        <div class="meta-item"><i class="fas fa-map-marker-alt meta-icon"></i> <span id="modal-lokasi"></span></div>
        <div class="meta-item"><i class="fas fa-ticket-alt meta-icon"></i> <span id="modal-harga"></span></div>
        <div class="meta-item"><i class="fas fa-clock meta-icon"></i> <span id="modal-jam"></span></div>
        <div class="meta-item"><i class="fas fa-star meta-icon"></i> <span id="modal-rating"></span></div>
      </div>
      <p id="modal-desc" style="font-size:.9rem; color:var(--text); line-height:1.8;"></p>
      <div style="margin-top:20px;">
        <a id="modal-maps" href="#" target="_blank" 
           style="display:inline-flex; align-items:center; gap:8px; background:var(--primary); color:#fff; padding:10px 22px; border-radius:30px; font-size:.87rem; font-weight:600; transition:.3s;">
          <i class="fas fa-map"></i> Buka di Google Maps
        </a>
      </div>
      <div style="margin-top:32px; border-top:1px solid var(--border); padding-top:24px;">
        <h3 style="font-size:1rem; font-weight:700; margin-bottom:16px;">💬 Komentar Pengunjung</h3>
        <div id="modal-komentar-list" style="display:flex; flex-direction:column; gap:12px; margin-bottom:24px; max-height:240px; overflow-y:auto;"></div>
        <div style="background:var(--bg); border-radius:12px; padding:16px;">
          <h4 style="font-size:.9rem; font-weight:700; margin-bottom:12px; color:var(--dark);">✍️ Tulis Komentar</h4>
          <div id="komentar-alert" style="display:none; padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:12px;"></div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:4px;">Nama <span style="color:red">*</span></label>
              <input id="k-nama" type="text" placeholder="Nama kamu" maxlength="100"
                style="width:100%; padding:8px 12px; border:1.5px solid var(--border); border-radius:8px; font-size:.85rem; font-family:inherit; outline:none; box-sizing:border-box;">
            </div>
            <div>
              <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:4px;">Email <span style="color:var(--text-lt); font-weight:400;">(opsional)</span></label>
              <input id="k-email" type="email" placeholder="email@contoh.com" maxlength="150"
                style="width:100%; padding:8px 12px; border:1.5px solid var(--border); border-radius:8px; font-size:.85rem; font-family:inherit; outline:none; box-sizing:border-box;">
            </div>
          </div>
          <div style="margin-bottom:10px;">
            <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:6px;">Rating</label>
            <div id="star-rating" style="display:flex; gap:6px; cursor:pointer; font-size:1.4rem;">
              <span data-val="1">⭐</span><span data-val="2">⭐</span><span data-val="3">⭐</span>
              <span data-val="4">⭐</span><span data-val="5">⭐</span>
            </div>
            <input type="hidden" id="k-rating" value="5">
          </div>
          <div style="margin-bottom:12px;">
            <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:4px;">Komentar <span style="color:red">*</span></label>
            <textarea id="k-isi" placeholder="Bagikan pengalamanmu..." maxlength="1000" rows="3"
              style="width:100%; padding:8px 12px; border:1.5px solid var(--border); border-radius:8px; font-size:.85rem; font-family:inherit; resize:vertical; outline:none; box-sizing:border-box;"></textarea>
          </div>
          <button id="btn-kirim-komentar"
            style="background:var(--primary); color:#fff; border:none; padding:9px 22px; border-radius:30px; font-size:.87rem; font-weight:600; cursor:pointer; transition:.3s;">
            <i class="fas fa-paper-plane"></i> Kirim Komentar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-kebijakan">
  <div class="modal-box" style="max-width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">🔒 Kebijakan Privasi</h2>
        <button class="modal-close" onclick="document.getElementById('modal-kebijakan').classList.remove('active'); document.body.style.overflow=''">✕</button>
      </div>
      <div style="font-size:.88rem; color:var(--text); line-height:1.9;">
        <p style="margin-bottom:14px;">Selamat datang di <strong>Pinarak Yogyakarta</strong>. Kami menghargai privasi Anda dan berkomitmen untuk melindungi informasi pribadi yang Anda berikan.</p>
        
        <h4 style="color:var(--primary); margin-bottom:8px;">📋 Informasi yang Kami Kumpulkan</h4>
        <p style="margin-bottom:14px;">Kami mengumpulkan nama dan email yang Anda isi pada form komentar. Informasi ini digunakan semata-mata untuk keperluan identifikasi komentar.</p>
        
        <h4 style="color:var(--primary); margin-bottom:8px;">🔐 Keamanan Data</h4>
        <p style="margin-bottom:14px;">Data Anda disimpan dengan aman dan tidak akan dijual, diperdagangkan, atau diserahkan kepada pihak ketiga tanpa persetujuan Anda.</p>
        
        <h4 style="color:var(--primary); margin-bottom:8px;">🍪 Cookie</h4>
        <p style="margin-bottom:14px;">Website ini menggunakan session cookie untuk keperluan teknis. Cookie tidak menyimpan informasi pribadi Anda.</p>
        
        <h4 style="color:var(--primary); margin-bottom:8px;">📧 Hubungi Kami</h4>
        <p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami di <strong>pinarakyogyakarta@gmail.com</strong></p>
      </div>
      <div style="margin-top:20px; text-align:right;">
        <button onclick="document.getElementById('modal-kebijakan').classList.remove('active'); document.body.style.overflow=''"
          style="background:var(--primary); color:#fff; border:none; padding:10px 24px; border-radius:30px; font-size:.87rem; font-weight:600; cursor:pointer;">
          Mengerti ✓
        </button>
      </div>
    </div>
  </div>
</div>

<footer id="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
          <img src="assets/images/logo.jpeg"
               style="width:38px; height:38px; object-fit:cover; border-radius:50%; display:block; flex-shrink:0;"
               alt="Logo">
          <div class="brand-name">Pinarak Yogyakarta</div>
        </div>
        <p class="footer-desc">Portal resmi informasi wisata Kota Yogyakarta. Temukan destinasi terbaik dan buat kenangan indah bersama orang-orang tersayang.</p>
        <div class="social-links">
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
          <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
        </div>
      </div>
      <div>
        <h4 class="footer-heading">Destinasi Populer</h4>
        <ul class="footer-links">
          <li><a href="#">Malioboro</a></li>
          <li><a href="#">Candi Prambanan</a></li>
          <li><a href="#">Pantai Parangtritis</a></li>
          <li><a href="#">Keraton Yogyakarta</a></li>
          <li><a href="#">Gunung Merapi</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer-heading">Kategori Wisata</h4>
        <ul class="footer-links">
          <?php foreach ($kategoriList as $kat): ?>
          <li><a href="?kat=<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="footer-heading">Info & Bantuan</h4>
        <ul class="footer-links">
          <li><a href="#tentang-kami">Tentang Kami</a></li>
          <li><a href="kontak.php">Kontak</a></li>
          <li>
            <a href="#" onclick="document.getElementById('modal-kebijakan').classList.add('active'); document.body.style.overflow='hidden'; return false;">
              Kebijakan Privasi
            </a>
          </li>
          <li><a href="admin/login.php"><i class="fas fa-lock"></i> Login Admin</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
  <div class="footer-bottom">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
      <span>© <?= date('Y') ?> Pinarak Yogyakarta. Dibuat dengan ❤️ untuk Kota Istimewa.</span>
      <span>Hak Cipta Dilindungi | Dinas Pariwisata Yogyakarta</span>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
<script>
(function () {
  const overlay  = document.getElementById('modal-wisata');
  const btnClose = document.getElementById('modal-close');
  let currentId  = null;

  document.querySelectorAll('[data-open-modal]').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.dataset.openModal));
  });

  function openModal(id) {
    currentId = id;
    resetForm();
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    fetch(`ajax/get_wisata.php?id=${id}`)
      .then(r => r.json())
      .then(data => {
        if (!data.success) return;
        const w = data.wisata;
        document.getElementById('modal-img').src              = `uploads/wisata/${w.gambar}`;
        document.getElementById('modal-nama').textContent     = w.nama;
        document.getElementById('modal-kategori').textContent = w.kategori;
        document.getElementById('modal-lokasi').textContent   = w.lokasi;
        document.getElementById('modal-harga').textContent    = w.harga_tiket;
        document.getElementById('modal-jam').textContent      = w.jam_buka;
        document.getElementById('modal-rating').textContent   = `${w.rating} / 5`;
        document.getElementById('modal-desc').textContent     = w.deskripsi;
        document.getElementById('modal-maps').href            = w.maps_url || '#';
        renderKomentar(data.komentar);
      })
      .catch(() => alert('Gagal memuat data. Coba lagi.'));
  }

  btnClose.addEventListener('click', closeModal);
  overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  function closeModal() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    currentId = null;
  }

  function renderKomentar(list) {
    const el = document.getElementById('modal-komentar-list');
    if (!list || list.length === 0) {
      el.innerHTML = `<p style="color:var(--text-lt); font-size:.85rem; text-align:center; padding:16px 0;">Belum ada komentar. Jadilah yang pertama! 😊</p>`;
      return;
    }
    el.innerHTML = list.map(k => `
      <div style="background:var(--bg-card); border-radius:10px; padding:12px 14px; box-shadow:var(--shadow);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
          <span style="font-weight:700; font-size:.88rem; color:var(--primary);">👤 ${esc(k.nama)}</span>
          <span style="font-size:.75rem; color:var(--text-lt);">${formatTgl(k.created_at)}</span>
        </div>
        <div style="font-size:.78rem; margin-bottom:6px;">${'⭐'.repeat(k.rating)}</div>
        <p style="font-size:.85rem; color:var(--text); line-height:1.6; margin:0;">${esc(k.komentar)}</p>
      </div>
    `).join('');
  }

  const stars = document.querySelectorAll('#star-rating span');
  const inputRating = document.getElementById('k-rating');
  let selectedRating = 5;

  function highlightStars(val) {
    stars.forEach(s => s.style.opacity = s.dataset.val <= val ? '1' : '0.3');
  }
  highlightStars(5);

  stars.forEach(s => {
    s.addEventListener('mouseenter', () => highlightStars(s.dataset.val));
    s.addEventListener('mouseleave', () => highlightStars(selectedRating));
    s.addEventListener('click', () => {
      selectedRating = s.dataset.val;
      inputRating.value = selectedRating;
      highlightStars(selectedRating);
    });
  });

  document.getElementById('btn-kirim-komentar').addEventListener('click', function () {
    const nama   = document.getElementById('k-nama').value.trim();
    const email  = document.getElementById('k-email').value.trim();
    const isi    = document.getElementById('k-isi').value.trim();
    const rating = inputRating.value;

    if (!nama || !isi) { showAlert('Nama dan komentar wajib diisi.', 'error'); return; }

    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    const body = new FormData();
    body.append('nama', nama);
    body.append('email', email);
    body.append('komentar', isi);
    body.append('rating', rating);

    fetch(`ajax/get_wisata.php?id=${currentId}&action=komentar`, { method: 'POST', body })
      .then(r => r.json())
      .then(data => {
        showAlert(data.msg, data.success ? 'success' : 'error');
        if (data.success) resetForm();
      })
      .catch(() => showAlert('Gagal mengirim komentar. Coba lagi.', 'error'))
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Komentar';
      });
  });

  function showAlert(msg, type) {
    const el = document.getElementById('komentar-alert');
    el.style.display = 'block';
    el.style.background = type === 'success' ? '#f0fff4' : '#fff5f5';
    el.style.color = type === 'success' ? '#276749' : '#c53030';
    el.style.border = `1px solid ${type === 'success' ? '#9ae6b4' : '#feb2b2'}`;
    el.textContent = msg;
  }

  function resetForm() {
    ['k-nama','k-email','k-isi'].forEach(id => document.getElementById(id).value = '');
    selectedRating = 5;
    inputRating.value = 5;
    highlightStars(5);
    const al = document.getElementById('komentar-alert');
    al.style.display = 'none';
    al.textContent = '';
  }

  function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function formatTgl(str) {
    const d = new Date(str);
    return d.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
  }
})();
</script>
</body>
</html>