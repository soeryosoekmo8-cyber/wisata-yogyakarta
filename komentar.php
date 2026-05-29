<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

// === HAPUS ===
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM komentar WHERE id = ?")->execute([intval($_GET['delete'])]);
    setFlash('success', 'Komentar berhasil dihapus.');
    header("Location: komentar.php");
    exit();
}

// === APPROVE ===
if (isset($_GET['approve'])) {
    $pdo->prepare("UPDATE komentar SET status = 'approved' WHERE id = ?")->execute([intval($_GET['approve'])]);
    setFlash('success', 'Komentar berhasil disetujui.');
    header("Location: komentar.php");
    exit();
}

// === REJECT ===
if (isset($_GET['reject'])) {
    $pdo->prepare("UPDATE komentar SET status = 'rejected' WHERE id = ?")->execute([intval($_GET['reject'])]);
    setFlash('info', 'Komentar ditolak.');
    header("Location: komentar.php");
    exit();
}

$search  = trim($_GET['q'] ?? '');
$status  = $_GET['status'] ?? 'semua';
$perPage = 15;
$page    = max(1, intval($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;

$where  = "WHERE 1=1";
$params = [];

if ($search) {
    $where   .= " AND (k.nama LIKE ? OR k.komentar LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($status !== 'semua') {
    $where   .= " AND k.status = ?";
    $params[] = $status;
}

$total = $pdo->prepare("SELECT COUNT(*) FROM komentar k $where");
$total->execute($params);
$totalData = $total->fetchColumn();
$totalPage = ceil($totalData / $perPage);

$stmt = $pdo->prepare("
    SELECT k.*, w.nama AS wisata_nama 
    FROM komentar k JOIN wisata w ON k.wisata_id = w.id 
    $where ORDER BY k.created_at DESC LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$komentarList = $stmt->fetchAll();

// Hitung badge pending
$totalPending = $pdo->query("SELECT COUNT(*) FROM komentar WHERE status = 'pending'")->fetchColumn();

$pageTitle = 'Kelola Komentar';
include 'includes/sidebar.php';
?>

<div class="card animate-on-scroll">
  <div class="card-header">
    <h3 class="card-header-title">
      <i class="fas fa-comments"></i> Daftar Komentar (<?= $totalData ?>)
      <?php if ($totalPending > 0): ?>
        <span style="background:#e53e3e; color:#fff; font-size:.72rem; padding:2px 8px; border-radius:20px; margin-left:6px;">
          <?= $totalPending ?> pending
        </span>
      <?php endif; ?>
    </h3>
    <form method="GET" class="search-bar-admin" style="display:flex; gap:8px; align-items:center;">
      <input type="text" name="q" placeholder="Cari nama / komentar..." value="<?= htmlspecialchars($search) ?>">
      <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
      <button type="submit" class="btn btn-primary-adm btn-sm"><i class="fas fa-search"></i></button>
      <?php if ($search): ?>
        <a href="komentar.php?status=<?= $status ?>" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Filter status -->
  <div style="padding:12px 22px; border-bottom:1px solid var(--border); display:flex; gap:8px; flex-wrap:wrap;">
    <?php
    $statusList = ['semua' => 'Semua', 'pending' => '⏳ Pending', 'approved' => '✅ Approved', 'rejected' => '❌ Ditolak'];
    foreach ($statusList as $val => $label):
    ?>
    <a href="komentar.php?status=<?= $val ?>&q=<?= urlencode($search) ?>"
       style="padding:5px 14px; border-radius:20px; font-size:.8rem; font-weight:600; text-decoration:none;
              background:<?= $status === $val ? 'var(--primary)' : 'var(--bg)' ?>;
              color:<?= $status === $val ? '#fff' : 'var(--text)' ?>;
              border:1px solid <?= $status === $val ? 'var(--primary)' : 'var(--border)' ?>;">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Pengunjung</th>
          <th>Tempat Wisata</th>
          <th>Komentar</th>
          <th>Rating</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($komentarList)): ?>
        <tr>
          <td colspan="8" style="text-align:center; padding:30px; color:var(--text-lt);">
            Belum ada komentar
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($komentarList as $i => $k): ?>
        <tr style="<?= $k['status'] === 'pending' ? 'background:rgba(237,137,54,.06);' : '' ?>">
          <td><?= $offset + $i + 1 ?></td>
          <td>
            <strong><?= htmlspecialchars($k['nama']) ?></strong>
            <?php if ($k['email']): ?>
            <div style="font-size:.75rem; color:var(--text-lt);"><?= htmlspecialchars($k['email']) ?></div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($k['wisata_nama']) ?></td>
          <td style="max-width:250px; font-size:.85rem;">
            <?= htmlspecialchars(mb_substr($k['komentar'], 0, 120)) ?><?= strlen($k['komentar']) > 120 ? '...' : '' ?>
          </td>
          <td><?= str_repeat('⭐', $k['rating']) ?></td>
          <td>
            <?php
            $badgeColor = match($k['status']) {
              'approved' => '#28a745',
              'rejected' => '#dc3545',
              default    => '#fd7e14',
            };
            $badgeLabel = match($k['status']) {
              'approved' => '✅ Approved',
              'rejected' => '❌ Ditolak',
              default    => '⏳ Pending',
            };
            ?>
            <span style="background:<?= $badgeColor ?>; color:#fff; font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:600;">
              <?= $badgeLabel ?>
            </span>
          </td>
          <td style="font-size:.8rem;"><?= date('d M Y H:i', strtotime($k['created_at'])) ?></td>
          <td>
            <div class="actions" style="display:flex; gap:4px; flex-wrap:wrap;">
              <?php if ($k['status'] !== 'approved'): ?>
              <a href="komentar.php?approve=<?= $k['id'] ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>&p=<?= $page ?>"
                 class="btn btn-sm btn-success" title="Approve"
                 onclick="return confirm('Setujui komentar ini?')">
                <i class="fas fa-check"></i>
              </a>
              <?php endif; ?>
              <?php if ($k['status'] !== 'rejected'): ?>
              <a href="komentar.php?reject=<?= $k['id'] ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>&p=<?= $page ?>"
                 class="btn btn-sm btn-warning" title="Tolak"
                 onclick="return confirm('Tolak komentar ini?')">
                <i class="fas fa-times"></i>
              </a>
              <?php endif; ?>
              <a href="komentar.php?delete=<?= $k['id'] ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>&p=<?= $page ?>"
                 class="btn btn-sm btn-danger" title="Hapus"
                 onclick="return confirm('Hapus komentar ini?')">
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
    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
    <a href="?q=<?= urlencode($search) ?>&status=<?= $status ?>&p=<?= $i ?>"
       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer_admin.php'; ?>