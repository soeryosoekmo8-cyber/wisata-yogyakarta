<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

if (isset($_GET['delete'])) {
    $did = intval($_GET['delete']);
    $cek = $pdo->prepare("SELECT COUNT(*) FROM wisata WHERE kategori_id = ?");
    $cek->execute([$did]);
    if ($cek->fetchColumn() > 0) {
        setFlash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data wisata.');
    } else {
        $pdo->prepare("DELETE FROM kategori WHERE id = ?")->execute([$did]);
        setFlash('success', 'Kategori berhasil dihapus.');
    }
    header("Location: kategori.php");
    exit();
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editData = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $ikon = trim($_POST['ikon'] ?? 'fa-map-marker-alt');
    $editId = intval($_POST['edit_id'] ?? 0);

    if (empty($nama)) {
        setFlash('error', 'Nama kategori wajib diisi!');
    } else {
        if ($editId > 0) {
            $pdo->prepare("UPDATE kategori SET nama = ?, ikon = ? WHERE id = ?")->execute([$nama, $ikon, $editId]);
            setFlash('success', 'Kategori berhasil diperbarui!');
        } else {
            $pdo->prepare("INSERT INTO kategori (nama, ikon) VALUES (?, ?)")->execute([$nama, $ikon]);
            setFlash('success', 'Kategori baru berhasil ditambahkan!');
        }
        header("Location: kategori.php");
        exit();
    }
}

$kategoriList = $pdo->query("
    SELECT k.*, COUNT(w.id) AS jml_wisata 
    FROM kategori k 
    LEFT JOIN wisata w ON k.id = w.kategori_id 
    GROUP BY k.id ORDER BY k.nama
")->fetchAll();

$pageTitle = 'Kelola Kategori';
include 'includes/sidebar.php';
?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px; align-items:start;">

  <div class="form-card animate-on-scroll">
    <h3 class="form-title">
      <i class="fas <?= $editData ? 'fa-edit' : 'fa-plus-circle' ?>" style="color:var(--<?= $editData ? 'warning' : 'success' ?>);"></i>
      <?= $editData ? 'Edit Kategori' : 'Tambah Kategori' ?>
    </h3>

    <?= getFlash() ?>

    <form method="POST" action="kategori.php<?= $editData ? '?edit='.$editData['id'] : '' ?>">
      <?php if ($editData): ?>
      <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
      <?php endif; ?>

      <div class="form-group">
        <label>Nama Kategori <span style="color:red">*</span></label>
        <input type="text" name="nama" 
               placeholder="Cth: Wisata Alam" 
               value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Ikon Font Awesome</label>
        <input type="text" name="ikon" 
               placeholder="fa-mountain" 
               value="<?= htmlspecialchars($editData['ikon'] ?? 'fa-map-marker-alt') ?>">
        <div style="font-size:.75rem; color:var(--text-lt); margin-top:4px;">
          Lihat ikon di <a href="https://fontawesome.com/icons" target="_blank" style="color:var(--primary);">fontawesome.com</a>
        </div>
      </div>

      <div style="display:flex; gap:10px;">
        <button type="submit" class="btn <?= $editData ? 'btn-warning' : 'btn-success' ?>">
          <i class="fas fa-save"></i> <?= $editData ? 'Update' : 'Simpan' ?>
        </button>
        <?php if ($editData): ?>
        <a href="kategori.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="card animate-on-scroll">
    <div class="card-header">
      <h3 class="card-header-title">📂 Daftar Kategori</h3>
      <span style="font-size:.8rem; color:var(--text-lt);"><?= count($kategoriList) ?> kategori</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            <th>Ikon</th>
            <th>Jml Wisata</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($kategoriList)): ?>
          <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-lt);">Belum ada kategori</td></tr>
          <?php else: ?>
          <?php foreach ($kategoriList as $i => $k): ?>
          <tr <?= (isset($_GET['edit']) && $_GET['edit'] == $k['id']) ? 'style="background:#fef3c7;"' : '' ?>>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
            <td><i class="fas <?= htmlspecialchars($k['ikon']) ?>" style="color:var(--primary);"></i> <?= htmlspecialchars($k['ikon']) ?></td>
            <td>
              <span class="badge badge-success"><?= $k['jml_wisata'] ?> wisata</span>
            </td>
            <td>
              <div class="actions">
                <a href="kategori.php?edit=<?= $k['id'] ?>" class="btn btn-sm btn-warning">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="kategori.php?delete=<?= $k['id'] ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Hapus kategori ini? Pastikan tidak ada wisata yang menggunakan kategori ini.')">
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
  </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
