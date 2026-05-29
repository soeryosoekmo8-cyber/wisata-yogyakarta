<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (isAdminLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

session_start();
$error = '';

if (empty($_SESSION['captcha'])) {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha'] = $a + $b;
    $_SESSION['captcha_soal'] = "$a + $b";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $captcha  = trim($_POST['captcha'] ?? '');

    if ((int)$captcha !== (int)$_SESSION['captcha']) {
        $error = 'Jawaban captcha salah!';
        $a = rand(1, 9);
        $b = rand(1, 9);
        $_SESSION['captcha'] = $a + $b;
        $_SESSION['captcha_soal'] = "$a + $b";
    } elseif (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } elseif (loginAdmin($pdo, $username, $password)) {
        unset($_SESSION['captcha'], $_SESSION['captcha_soal']);
        setFlash('success', 'Selamat datang, ' . $_SESSION['admin_nama'] . '!');
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Pinarak Yogyakarta</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-page">
  <div class="login-bg-pattern"></div>

  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon" style="background:none; padding:0; width:70px; height:70px; border-radius:50%; overflow:hidden; margin:0 auto;">
        <img src="../assets/images/logo.jpeg" 
             style="width:70px; height:70px; object-fit:cover; border-radius:50%; display:block;" 
             alt="Logo">
      </div>
      <h1 class="login-title">Panel Admin</h1>
      <p class="login-sub">Pinarak Yogyakarta — Kota Istimewa</p>
    </div>

    <?php if ($error): ?>
    <div class="flash-msg flash-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="username"><i class="fas fa-user"></i> Username</label>
        <input type="text" id="username" name="username" 
               placeholder="Masukkan username" 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               autocomplete="username" required>
      </div>

      <div class="form-group">
        <label for="password"><i class="fas fa-lock"></i> Password</label>
        <input type="password" id="password" name="password" 
               placeholder="Masukkan password"
               autocomplete="current-password" required>
      </div>

      <div class="form-group">
        <label><i class="fas fa-shield-alt"></i> Verifikasi: Berapa hasil dari <strong><?= $_SESSION['captcha_soal'] ?></strong> ?</label>
        <input type="number" name="captcha" placeholder="Masukkan jawaban" required>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-sign-in-alt"></i> Masuk ke Panel Admin
      </button>
    </form>

    <p class="login-back">
      <a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
    </p>
  </div>
</div>
</body>
</html>