<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

if (isset($_GET['baca'])) {
    $pdo->prepare("UPDATE pesan_kontak SET status='sudah_dibaca' WHERE id=?")->execute([$_GET['baca']]);
    header("Location: pesan.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM pesan_kontak WHERE id=?")->execute([$_GET['hapus']]);
    header("Location: pesan.php");
    exit();
}

$pesanList = $pdo->query("SELECT * FROM pesan_kontak ORDER BY created_at DESC")->fetchAll();
$belumDibaca = $pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE status='belum_dibaca'")->fetchColumn();

$pageTitle = 'Pesan Masuk';
include 'includes/sidebar.php';
?>

<div class="card">
  <div class="card-header">
    <div class="card-header-title">
      📬 Pesan Masuk
      <?php if ($belumDibaca > 0): ?>
      <span class="badge badge-danger" style="margin-left:8px;"><?= $belumDibaca ?> Baru</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="table-wrap">
    <?php if (empty($pesanList)): ?>
    <div style="text-align:center; padding:40px; color:var(--text-lt);">
      <div style="font-size:3rem; margin-bottom:12px;">📭</div>
      <p>Belum ada pesan masuk</p>
    </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Subjek</th>
          <th>Pesan</th>
          <th>Waktu</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pesanList as $p): ?>
        <tr style="<?= $p['status'] === 'belum_dibaca' ? 'background:#fffbeb;' : '' ?>">
          <td style="font-weight:600;"><?= htmlspecialchars($p['nama']) ?></td>
          <td style="font-size:.82rem;"><?= htmlspecialchars($p['email']) ?></td>
          <td><?= htmlspecialchars($p['subjek']) ?></td>
          <td style="max-width:200px; font-size:.82rem; color:var(--text-lt);">
            <?= htmlspecialchars(substr($p['pesan'], 0, 80)) ?>...
          </td>
          <td style="font-size:.78rem; color:var(--text-lt);">
            <?= date('d M Y H:i', strtotime($p['created_at'])) ?>
          </td>
          <td>
            <?php if ($p['status'] === 'belum_dibaca'): ?>
            <span class="badge badge-danger">Baru</span>
            <?php else: ?>
            <span class="badge badge-success">Dibaca</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="actions">
              <?php if ($p['status'] === 'belum_dibaca'): ?>
              <a href="?baca=<?= $p['id'] ?>" class="btn btn-sm btn-info" title="Tandai dibaca">
                <i class="fas fa-check"></i>
              </a>
              <?php endif; ?>
              <button onclick="lihatPesan(`<?= htmlspecialchars($p['nama']) ?>`, `<?= htmlspecialchars($p['email']) ?>`, `<?= htmlspecialchars($p['subjek']) ?>`, `<?= htmlspecialchars($p['pesan']) ?>`)"
                class="btn btn-sm btn-primary-adm">
                <i class="fas fa-eye"></i>
              </button>
              <a href="?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-danger" 
                 onclick="return confirm('Hapus pesan ini?')">
                <i class="fas fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<div id="modal-pesan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:16px; padding:32px; max-width:500px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.3);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 style="font-family:var(--font-head); color:var(--dark);">📨 Detail Pesan</h3>
      <button onclick="document.getElementById('modal-pesan').style.display='none'"
        style="background:#f3f4f6; border:none; width:32px; height:32px; border-radius:50%; cursor:pointer; font-size:1rem;">✕</button>
    </div>
    <div style="display:flex; flex-direction:column; gap:12px; font-size:.88rem;">
      <div><strong>Nama:</strong> <span id="detail-nama"></span></div>
      <div><strong>Email:</strong> <span id="detail-email"></span></div>
      <div><strong>Subjek:</strong> <span id="detail-subjek"></span></div>
      <div style="border-top:1px solid var(--border); padding-top:12px;">
        <strong>Pesan:</strong>
        <p id="detail-pesan" style="margin-top:8px; line-height:1.8; color:var(--text);"></p>
      </div>
    </div>
  </div>
</div>

<script>
function lihatPesan(nama, email, subjek, pesan) {
  document.getElementById('detail-nama').textContent = nama;
  document.getElementById('detail-email').textContent = email;
  document.getElementById('detail-subjek').textContent = subjek;
  document.getElementById('detail-pesan').textContent = pesan;
  document.getElementById('modal-pesan').style.display = 'flex';
}
</script>

<?php include 'includes/footer_admin.php'; ?>