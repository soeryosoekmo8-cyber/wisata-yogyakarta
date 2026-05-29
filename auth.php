<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin(string $redirectTo = '../admin/login.php'): void {
    if (!isAdminLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
}

function loginAdmin(PDO $pdo, string $username, string $password): bool {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_user'] = $admin['username'];
        return true;
    }
    return false;
}

function logoutAdmin(): void {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

function clean(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function uploadGambar(array $file, string $oldGambar = ''): string|false {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > $maxSize) return false;
    if (!in_array($file['type'], $allowedTypes)) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $namaFile = 'wisata_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);

    if (move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $namaFile)) {
        // Hapus gambar lama jika ada
        if ($oldGambar && $oldGambar !== 'default.jpg' && file_exists(UPLOAD_PATH . $oldGambar)) {
            unlink(UPLOAD_PATH . $oldGambar);
        }
        return $namaFile;
    }
    return false;
}

function formatRupiah(string $str): string {
    return $str;
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): string {
    if (!isset($_SESSION['flash'])) return '';
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $icon = $f['type'] === 'success' ? '✅' : ($f['type'] === 'error' ? '❌' : 'ℹ️');
    $cls  = 'flash-' . $f['type'];
    return "<div class='flash-msg $cls'>$icon " . clean($f['msg']) . "</div>";
}
?>
