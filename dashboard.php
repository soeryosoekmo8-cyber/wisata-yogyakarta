<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$totalWisata  = $pdo->query("SELECT COUNT(*) FROM wisata")->fetchColumn();
$aktifWisata  = $pdo->query("SELECT COUNT(*) FROM wisata WHERE status='aktif'")->fetchColumn();
$totalKat     = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalKomentar= $pdo->query("SELECT COUNT(*) FROM komentar")->fetchColumn();


$wisataTerbaru = $pdo->query("
    SELECT w.*, k.nama AS kategori 
    FROM wisata w JOIN kategori k ON w.kategori_id = k.id 
    ORDER BY w.created_at DESC LIMIT 5
")->fetchAll();

$pageTitle = 'Dashboard';
include 'includes/sidebar.php';
?>

<div class="stats-grid">
  <div class="stat-card green animate-on-scroll">
    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
    <div class="stat-info">
      <div class="stat-num"><?= $totalWisata ?></div>
      <div class="stat-label">Total Wisata</div>
    </div>
  </div>
  <div class="stat-card gold animate-on-scroll">
    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    <div class="stat-info">
      <div class="stat-num"><?= $aktifWisata ?></div>
      <div class="stat-label">Wisata Aktif</div>
    </div>
  </div>
  <div class="stat-card blue animate-on-scroll">
    <div class="stat-icon"><i class="fas fa-tags"></i></div>
    <div class="stat-info">
      <div class="stat-num"><?= $totalKat ?></div>
      <div class="stat-label">Kategori</div>
    </div>
  </div>
  <div class="stat-card orange animate-on-scroll">
    <div class="stat-icon"><i class="fas fa-comments"></i></div>
    <div class="stat-info">
      <div class="stat-num"><?= $totalKomentar ?></div>
      <div class="stat-label">Komentar</div>
    </div>
  </div>
</div>

<div class="card animate-on-scroll">
  <div class="card-header">
    <h3 class="card-header-title">📋 Wisata Terbaru</h3>
    <a href="wisata.php" class="btn btn-primary-adm">
      <i class="fas fa-list"></i> Lihat Semua
    </a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Gambar</th>
          <th>Nama Wisata</th>
          <th>Kategori</th>
          <th>Lokasi</th>
          <th>Rating</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($wisataTerbaru)): ?>
        <tr><td colspan="8" style="text-align:center; padding:30px; color:var(--text-lt);">Belum ada data wisata</td></tr>
        <?php else: ?>
        <?php foreach ($wisataTerbaru as $i => $w): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td>
            <?php $img = '../uploads/wisata/' . $w['gambar']; ?>
            <img src="<?= file_exists($img) ? $img : '../assets/images/default.jpg' ?>" 
                 class="td-img" alt="<?= htmlspecialchars($w['nama']) ?>">
          </td>
          <td><strong><?= htmlspecialchars($w['nama']) ?></strong></td>
          <td><?= htmlspecialchars($w['kategori']) ?></td>
          <td><?= htmlspecialchars($w['lokasi']) ?></td>
          <td>⭐ <?= $w['rating'] ?></td>
          <td>
            <span class="badge <?= $w['status'] === 'aktif' ? 'badge-success' : 'badge-danger' ?>">
              <?= ucfirst($w['status']) ?>
            </span>
          </td>
          <td class="actions">
            <a href="wisata_edit.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-warning">
              <i class="fas fa-edit"></i>
            </a>
            <a href="wisata.php?delete=<?= $w['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Yakin ingin menghapus wisata ini?')">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
