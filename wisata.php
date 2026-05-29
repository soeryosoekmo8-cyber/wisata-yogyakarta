<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

// === DELETE ===
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    // Ambil nama gambar sebelum hapus
    $stmt = $pdo->prepare("SELECT gambar FROM wisata WHERE id = ?");
    $stmt->execute([$delId]);
    $row = $stmt->fetch();

    if ($row) {
        $pdo->prepare("DELETE FROM wisata WHERE id = ?")->execute([$delId]);
        // Hapus file gambar jika ada
        if ($row['gambar'] && $row['gambar'] !== 'default.jpg') {
            $filePath = '../uploads/wisata/' . $row['gambar'];
            if (file_exists($filePath)) unlink($filePath);
        }
        setFlash('success', 'Data wisata berhasil dihapus.');
    } else {
        setFlash('error', 'Data tidak ditemukan.');
    }
    header("Location: wisata.php");
    exit();
}

if (isset($_GET['toggle'])) {
    $tid = intval($_GET['toggle']);
    $pdo->prepare("UPDATE wisata SET status = IF(status='aktif','nonaktif','aktif') WHERE id = ?")->execute([$tid]);
    setFlash('info', 'Status wisata berhasil diubah.');
    header("Location: wisata.php");
    exit();
}


$search  = trim($_GET['q'] ?? '');
$katId   = intval($_GET['kat'] ?? 0);
$perPage = 10;
$page    = max(1, intval($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;

$where  = "WHERE 1=1";
$params = [];

if ($search) {
    $where   .= " AND (w.nama LIKE ? OR w.lokasi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($katId > 0) {
    $where   .= " AND w.kategori_id = ?";
    $params[] = $katId;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM wisata w $where");
$countStmt->execute($params);
$totalData = $countStmt->fetchColumn();
$totalPage = ceil($totalData / $perPage);

$stmt = $pdo->prepare("
    SELECT w.*, k.nama AS kategori_nama 
    FROM wisata w 
    JOIN kategori k ON w.kategori_id = k.id 
    $where 
    ORDER BY w.id DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$wisataList = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$pageTitle = 'Kelola Wisata';
include 'includes/sidebar.php';
?>

<div class="card animate-on-scroll" style="margin-bottom:20px;">
  <div class="card-header">
    <h3 class="card-header-title">
      <i class="fas fa-map-marked-alt"></i> Data Wisata 
      <span style="font-size:.8rem; color:var(--text-lt); font-weight:400;">(<?= $totalData ?> data)</span>
    </h3>
    <a href="wisata_tambah.php" class="btn btn-success">
      <i class="fas fa-plus"></i> Tambah Wisata
    </a>
  </div>

  <!-- Filter Form -->
  <div style="padding:16px 22px; border-bottom:1px solid var(--border); background:var(--bg);">
    <form method="GET" action="wisata.php" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
      <div>
        <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:4px;">Cari Wisata</label>
        <div class="search-bar-admin">
          <input type="text" name="q" placeholder="Nama atau lokasi..." value="<?= htmlspecialchars($search) ?>">
        </div>
      </div>
      <div>
        <label style="font-size:.78rem; font-weight:600; display:block; margin-bottom:4px;">Kategori</label>
        <select name="kat" style="padding:7px 12px; border:2px solid var(--border); border-radius:8px; font-family:var(--font-body); font-size:.85rem; outline:none;">
          <option value="0">Semua Kategori</option>
          <?php foreach ($kategoriList as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $katId == $k['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary-adm">
        <i class="fas fa-search"></i> Cari
      </button>
      <?php if ($search || $katId): ?>
      <a href="wisata.php" class="btn btn-secondary">
        <i class="fas fa-times"></i> Reset
      </a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Table -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:40px">No</th>
          <th style="width:70px">Gambar</th>
          <th>Nama Wisata</th>
          <th>Kategori</th>
          <th>Lokasi</th>
          <th>Harga</th>
          <th>Rating</th>
          <th>Status</th>
          <th style="width:150px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($wisataList)): ?>
        <tr>
          <td colspan="9" style="text-align:center; padding:40px; color:var(--text-lt);">
            <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
            Tidak ada data wisata<?= $search ? " untuk pencarian \"$search\"" : '' ?>
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($wisataList as $no => $w): ?>
        <tr>
          <td><?= $offset + $no + 1 ?></td>
          <td>
            <?php $img = '../uploads/wisata/' . $w['gambar']; ?>
            <img src="<?= file_exists($img) ? $img : '../assets/images/default.jpg' ?>"
                 class="td-img" alt="">
          </td>
          <td>
            <strong><?= htmlspecialchars($w['nama']) ?></strong>
            <div style="font-size:.75rem; color:var(--text-lt);">ID: <?= $w['id'] ?></div>
          </td>
          <td><?= htmlspecialchars($w['kategori_nama']) ?></td>
          <td style="max-width:150px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            <?= htmlspecialchars($w['lokasi']) ?>
          </td>
          <td style="font-size:.82rem;"><?= htmlspecialchars($w['harga_tiket']) ?></td>
          <td>⭐ <?= $w['rating'] ?></td>
          <td>
            <a href="wisata.php?toggle=<?= $w['id'] ?>&q=<?= urlencode($search) ?>&kat=<?= $katId ?>&p=<?= $page ?>"
               class="badge <?= $w['status'] === 'aktif' ? 'badge-success' : 'badge-danger' ?>"
               title="Klik untuk toggle status" style="cursor:pointer;">
              <?= ucfirst($w['status']) ?>
            </a>
          </td>
          <td>
            <div class="actions">
              <a href="wisata_edit.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
              <a href="wisata.php?delete=<?= $w['id'] ?>&q=<?= urlencode($search) ?>&kat=<?= $katId ?>&p=<?= $page ?>" 
                 class="btn btn-sm btn-danger" title="Hapus"
                 onclick="return confirm('Yakin menghapus wisata ini? Gambar juga akan dihapus!')">
                <i class="fas fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPage > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
    <a href="?q=<?= urlencode($search) ?>&kat=<?= $katId ?>&p=<?= $page-1 ?>" class="page-btn">
      <i class="fas fa-chevron-left"></i>
    </a>
    <?php endif; ?>

    <?php for ($i = max(1, $page-2); $i <= min($totalPage, $page+2); $i++): ?>
    <a href="?q=<?= urlencode($search) ?>&kat=<?= $katId ?>&p=<?= $i ?>" 
       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPage): ?>
    <a href="?q=<?= urlencode($search) ?>&kat=<?= $katId ?>&p=<?= $page+1 ?>" class="page-btn">
      <i class="fas fa-chevron-right"></i>
    </a>
    <?php endif; ?>

    <span style="font-size:.8rem; color:var(--text-lt); margin-left:8px;">
      Hal <?= $page ?> dari <?= $totalPage ?>
    </span>
  </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer_admin.php'; ?>
