<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$adminId = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_nama') {
        $nama = trim($_POST['nama'] ?? '');
        if (empty($nama)) {
            $errors[] = 'Nama tidak boleh kosong';
        } else {
            $pdo->prepare("UPDATE admin SET nama = ? WHERE id = ?")->execute([$nama, $adminId]);
            $_SESSION['admin_nama'] = $nama;
            setFlash('success', 'Nama berhasil diperbarui!');
            header("Location: profil.php");
            exit();
        }
    }

    if ($action === 'ganti_password') {
        $oldPass = $_POST['old_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confPass= $_POST['confirm_password'] ?? '';

        if (!password_verify($oldPass, $admin['password'])) {
            $errors[] = 'Password lama tidak sesuai';
        } elseif (strlen($newPass) < 6) {
            $errors[] = 'Password baru minimal 6 karakter';
        } elseif ($newPass !== $confPass) {
            $errors[] = 'Konfirmasi password tidak cocok';
        } else {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?")->execute([$hash, $adminId]);
            setFlash('success', 'Password berhasil diubah!');
            header("Location: profil.php");
            exit();
        }
    }
}

$pageTitle = 'Profil Admin';
include 'includes/sidebar.php';
?>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

  <!-- Info Profil -->
  <div class="form-card animate-on-scroll">
    <h3 class="form-title"><i class="fas fa-user-circle" style="color:var(--info);"></i> Informasi Akun</h3>

    <?php if (!empty($errors)): ?>
    <div class="flash-msg flash-error" style="margin-bottom:16px;">
      <?php foreach ($errors as $e): ?><div>❌ <?= clean($e) ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Avatar besar -->
    <div style="text-align:center; margin-bottom:24px;">
      <div style="width:80px; height:80px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:2rem; font-weight:700; margin:0 auto 12px;">
        <?= strtoupper(substr($admin['nama'], 0, 1)) ?>
      </div>
      <div style="font-family:var(--font-head); font-size:1.2rem; color:var(--dark);"><?= htmlspecialchars($admin['nama']) ?></div>
      <div style="font-size:.83rem; color:var(--text-lt);">@<?= htmlspecialchars($admin['username']) ?></div>
      <div style="font-size:.78rem; color:var(--text-lt); margin-top:4px;">
        Bergabung: <?= date('d M Y', strtotime($admin['created_at'])) ?>
      </div>
    </div>

    <form method="POST">
      <input type="hidden" name="action" value="update_nama">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required>
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" value="<?= htmlspecialchars($admin['username']) ?>" disabled 
               style="background:#f0f0f0; cursor:not-allowed;">
        <div style="font-size:.75rem; color:var(--text-lt); margin-top:4px;">Username tidak dapat diubah</div>
      </div>
      <button type="submit" class="btn btn-primary-adm">
        <i class="fas fa-save"></i> Simpan Perubahan
      </button>
    </form>
  </div>

  <div class="form-card animate-on-scroll">
    <h3 class="form-title"><i class="fas fa-key" style="color:var(--warning);"></i> Ganti Password</h3>

    <form method="POST">
      <input type="hidden" name="action" value="ganti_password">
      <div class="form-group">
        <label>Password Lama</label>
        <input type="password" name="old_password" placeholder="Masukkan password lama" required>
      </div>
      <div class="form-group">
        <label>Password Baru</label>
        <input type="password" name="new_password" placeholder="Min. 6 karakter" required minlength="6">
      </div>
      <div class="form-group">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>
      </div>
      <button type="submit" class="btn btn-warning">
        <i class="fas fa-lock"></i> Ganti Password
      </button>
    </form>

    <div style="margin-top:24px; padding:16px; background:#fef3c7; border-radius:var(--radius); border-left:4px solid var(--warning);">
      <div style="font-size:.82rem; font-weight:700; color:#92400e; margin-bottom:6px;">⚠️ Tips Keamanan</div>
      <ul style="font-size:.8rem; color:#78350f; padding-left:16px;">
        <li>Gunakan password minimal 8 karakter</li>
        <li>Kombinasikan huruf, angka, dan simbol</li>
        <li>Jangan gunakan tanggal lahir atau nama</li>
        <li>Ganti password secara berkala</li>
      </ul>
    </div>
  </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
