<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$errors = [];
$data   = [];

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi
    $data['nama']        = trim($_POST['nama'] ?? '');
    $data['kategori_id'] = intval($_POST['kategori_id'] ?? 0);
    $data['lokasi']      = trim($_POST['lokasi'] ?? '');
    $data['deskripsi']   = trim($_POST['deskripsi'] ?? '');
    $data['harga_tiket'] = trim($_POST['harga_tiket'] ?? 'Gratis');
    $data['jam_buka']    = trim($_POST['jam_buka'] ?? '24 Jam');
    $data['maps_url']    = trim($_POST['maps_url'] ?? '');
    $data['rating']      = min(5, max(1, floatval($_POST['rating'] ?? 4.5)));
    $data['status']      = in_array($_POST['status'], ['aktif','nonaktif']) ? $_POST['status'] : 'aktif';

    if (empty($data['nama']))        $errors[] = 'Nama wisata wajib diisi';
    if ($data['kategori_id'] <= 0)   $errors[] = 'Pilih kategori wisata';
    if (empty($data['lokasi']))      $errors[] = 'Lokasi wajib diisi';
    if (empty($data['deskripsi']))   $errors[] = 'Deskripsi wajib diisi';

    $namaGambar = 'default.jpg';
    if (!empty($_FILES['gambar']['name'])) {
        $upload = uploadGambar($_FILES['gambar']);
        if ($upload === false) {
            $errors[] = 'Gambar gagal diupload. Pastikan format JPG/PNG/WEBP dan ukuran max 5MB';
        } else {
            $namaGambar = $upload;
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO wisata (nama, kategori_id, lokasi, deskripsi, harga_tiket, jam_buka, gambar, maps_url, rating, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nama'], $data['kategori_id'], $data['lokasi'], $data['deskripsi'],
            $data['harga_tiket'], $data['jam_buka'], $namaGambar, 
            $data['maps_url'] ?: null, $data['rating'], $data['status']
        ]);
        setFlash('success', 'Wisata "' . $data['nama'] . '" berhasil ditambahkan!');
        header("Location: wisata.php");
        exit();
    }
}

$pageTitle = 'Tambah Wisata';
include 'includes/sidebar.php';
?>

<div class="form-page">
  <!-- Breadcrumb -->
  <div style="display:flex; align-items:center; gap:8px; font-size:.85rem; color:var(--text-lt); margin-bottom:20px;">
    <a href="dashboard.php" style="color:var(--primary);">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size:.7rem;"></i>
    <a href="wisata.php" style="color:var(--primary);">Data Wisata</a>
    <i class="fas fa-chevron-right" style="font-size:.7rem;"></i>
    <span>Tambah Baru</span>
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
    <h3 class="form-title"><i class="fas fa-plus-circle" style="color:var(--success);"></i> Tambah Data Wisata</h3>

    <form method="POST" action="wisata_tambah.php" enctype="multipart/form-data">

      <div class="form-row">
        <div class="form-group">
          <label>Nama Wisata <span style="color:red">*</span></label>
          <input type="text" name="nama" placeholder="Cth: Candi Borobudur" 
                 value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Kategori <span style="color:red">*</span></label>
          <select name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($data['kategori_id'] ?? 0) == $k['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Lokasi <span style="color:red">*</span></label>
        <input type="text" name="lokasi" placeholder="Cth: Sleman, Yogyakarta" 
               value="<?= htmlspecialchars($data['lokasi'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Deskripsi <span style="color:red">*</span></label>
        <textarea name="deskripsi" rows="5" 
                  placeholder="Tulis deskripsi lengkap tentang tempat wisata ini..."><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Harga Tiket</label>
          <input type="text" name="harga_tiket" placeholder="Cth: Rp 25.000 atau Gratis" 
                 value="<?= htmlspecialchars($data['harga_tiket'] ?? 'Gratis') ?>">
        </div>
        <div class="form-group">
          <label>Jam Buka</label>
          <input type="text" name="jam_buka" placeholder="Cth: 08.00 - 17.00 WIB" 
                 value="<?= htmlspecialchars($data['jam_buka'] ?? '24 Jam') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Rating (1-5)</label>
          <input type="number" name="rating" min="1" max="5" step="0.1" 
                 value="<?= $data['rating'] ?? 4.5 ?>">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="aktif" <?= ($data['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif (Tampil)</option>
            <option value="nonaktif" <?= ($data['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Link Google Maps</label>
        <input type="url" name="maps_url" placeholder="https://maps.google.com/..." 
               value="<?= htmlspecialchars($data['maps_url'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Gambar Wisata</label>
        <input type="file" name="gambar" id="gambar-input" accept="image/jpeg,image/png,image/webp"
               onchange="previewGambar(this)">
        <div style="font-size:.77rem; color:var(--text-lt); margin-top:4px;">
          Format: JPG, PNG, WEBP. Ukuran max: 5MB
        </div>
        <div class="img-preview-wrap" id="preview-wrap" style="display:none; margin-top:12px;">
          <img id="img-preview" src="" alt="Preview" 
               style="max-width:200px; border-radius:10px; border:2px solid var(--border);">
        </div>
      </div>

      <div style="display:flex; gap:12px; padding-top:8px;">
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save"></i> Simpan Wisata
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
  const wrap    = document.getElementById('preview-wrap');
  const preview = document.getElementById('img-preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php include 'includes/footer_admin.php'; ?>
