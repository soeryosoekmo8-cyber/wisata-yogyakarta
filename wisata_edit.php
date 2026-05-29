<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlash('error', 'ID tidak valid');
    header("Location: wisata.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM wisata WHERE id = ?");
$stmt->execute([$id]);
$wisata = $stmt->fetch();

if (!$wisata) {
    setFlash('error', 'Data wisata tidak ditemukan');
    header("Location: wisata.php");
    exit();
}

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama'        => trim($_POST['nama'] ?? ''),
        'kategori_id' => intval($_POST['kategori_id'] ?? 0),
        'lokasi'      => trim($_POST['lokasi'] ?? ''),
        'deskripsi'   => trim($_POST['deskripsi'] ?? ''),
        'harga_tiket' => trim($_POST['harga_tiket'] ?? 'Gratis'),
        'jam_buka'    => trim($_POST['jam_buka'] ?? '24 Jam'),
        'maps_url'    => trim($_POST['maps_url'] ?? ''),
        'rating'      => min(5, max(1, floatval($_POST['rating'] ?? 4.5))),
        'status'      => in_array($_POST['status'], ['aktif','nonaktif']) ? $_POST['status'] : 'aktif',
    ];

    if (empty($data['nama']))       $errors[] = 'Nama wisata wajib diisi';
    if ($data['kategori_id'] <= 0)  $errors[] = 'Pilih kategori wisata';
    if (empty($data['lokasi']))     $errors[] = 'Lokasi wajib diisi';
    if (empty($data['deskripsi']))  $errors[] = 'Deskripsi wajib diisi';

    // Cek upload gambar baru
    $namaGambar = $wisata['gambar']; // default: gambar lama
    if (!empty($_FILES['gambar']['name'])) {
        $upload = uploadGambar($_FILES['gambar'], $wisata['gambar']);
        if ($upload === false) {
            $errors[] = 'Gambar gagal diupload. Format JPG/PNG/WEBP, max 5MB';
        } else {
            $namaGambar = $upload;
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE wisata SET 
                nama = ?, kategori_id = ?, lokasi = ?, deskripsi = ?,
                harga_tiket = ?, jam_buka = ?, gambar = ?, maps_url = ?, 
                rating = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['nama'], $data['kategori_id'], $data['lokasi'], $data['deskripsi'],
            $data['harga_tiket'], $data['jam_buka'], $namaGambar,
            $data['maps_url'] ?: null, $data['rating'], $data['status'], $id
        ]);
        setFlash('success', 'Wisata "' . $data['nama'] . '" berhasil diperbarui!');
        header("Location: wisata.php");
        exit();
    }

    $wisata = array_merge($wisata, $data);
}

$pageTitle = 'Edit Wisata';
include 'includes/sidebar.php';
?>

<div class="form-page">
  <div style="display:flex; align-items:center; gap:8px; font-size:.85rem; color:var(--text-lt); margin-bottom:20px;">
    <a href="dashboard.php" style="color:var(--primary);">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size:.7rem;"></i>
    <a href="wisata.php" style="color:var(--primary);">Data Wisata</a>
    <i class="fas fa-chevron-right" style="font-size:.7rem;"></i>
    <span>Edit Wisata</span>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="flash-msg flash-error" style="margin-bottom:16px;">
    <strong>❌ Terdapat kesalahan:</strong>
    <ul style="margin:8px 0 0 20px;">
      <?php foreach ($errors as $e): ?><li><?= clean($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="form-card animate-on-scroll">
    <h3 class="form-title"><i class="fas fa-edit" style="color:var(--warning);"></i> Edit Data Wisata</h3>

    <?php $imgCurrent = '../uploads/wisata/' . $wisata['gambar']; ?>
    <?php if ($wisata['gambar'] && file_exists($imgCurrent)): ?>
    <div style="margin-bottom:20px; padding:14px; background:var(--bg); border-radius:var(--radius);">
      <div style="font-size:.82rem; font-weight:600; color:var(--text-lt); margin-bottom:8px;">📷 Gambar Saat Ini:</div>
      <img src="<?= $imgCurrent ?>" alt="" style="max-width:200px; border-radius:10px; border:2px solid var(--border);">
    </div>
    <?php endif; ?>

    <form method="POST" action="wisata_edit.php?id=<?= $id ?>" enctype="multipart/form-data">

      <div class="form-row">
        <div class="form-group">
          <label>Nama Wisata <span style="color:red">*</span></label>
          <input type="text" name="nama" value="<?= htmlspecialchars($wisata['nama']) ?>" required>
        </div>
        <div class="form-group">
          <label>Kategori <span style="color:red">*</span></label>
          <select name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $wisata['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Lokasi <span style="color:red">*</span></label>
        <input type="text" name="lokasi" value="<?= htmlspecialchars($wisata['lokasi']) ?>" required>
      </div>

      <div class="form-group">
        <label>Deskripsi <span style="color:red">*</span></label>
        <textarea name="deskripsi" rows="5"><?= htmlspecialchars($wisata['deskripsi']) ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Harga Tiket</label>
          <input type="text" name="harga_tiket" value="<?= htmlspecialchars($wisata['harga_tiket']) ?>">
        </div>
        <div class="form-group">
          <label>Jam Buka</label>
          <input type="text" name="jam_buka" value="<?= htmlspecialchars($wisata['jam_buka']) ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Rating (1-5)</label>
          <input type="number" name="rating" min="1" max="5" step="0.1" value="<?= $wisata['rating'] ?>">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="aktif"     <?= $wisata['status'] === 'aktif'     ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif"  <?= $wisata['status'] === 'nonaktif'  ? 'selected' : '' ?>>Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Link Google Maps</label>
        <input type="url" name="maps_url" value="<?= htmlspecialchars($wisata['maps_url'] ?? '') ?>" placeholder="https://maps.google.com/...">
      </div>

      <div class="form-group">
        <label>Ganti Gambar <span style="font-size:.78rem; color:var(--text-lt); font-weight:400;">(kosongkan jika tidak ingin mengubah)</span></label>
        <input type="file" name="gambar" id="gambar-input" accept="image/jpeg,image/png,image/webp"
               onchange="previewGambar(this)">
        <div style="font-size:.77rem; color:var(--text-lt); margin-top:4px;">Format: JPG, PNG, WEBP. Max 5MB</div>
        <div class="img-preview-wrap" id="preview-wrap" style="display:none; margin-top:12px;">
          <div style="font-size:.8rem; color:var(--text-lt); margin-bottom:6px;">Preview Gambar Baru:</div>
          <img id="img-preview" src="" alt="" style="max-width:200px; border-radius:10px; border:2px solid var(--border);">
        </div>
      </div>

      <div style="display:flex; gap:12px; padding-top:8px;">
        <button type="submit" class="btn btn-warning">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
        <a href="wisata.php" class="btn btn-secondary">
          <i class="fas fa-times"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>

<script>
function previewGambar(input) {
  const wrap = document.getElementById('preview-wrap');
  const img  = document.getElementById('img-preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php include 'includes/footer_admin.php'; ?>
