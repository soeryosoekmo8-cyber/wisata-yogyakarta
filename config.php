<?php

define('DB_HOST',   'sql209.infinityfree.com');
define('DB_USER',   'if0_41759454');
define('DB_PASS',   'yogyakartainfo1');
define('DB_NAME',   'if0_41759454_wisata_yogyakarta');
define('DB_CHARSET','utf8mb4');

define('BASE_URL', 'https://wisatayogyakarta.free.nf');
define('UPLOAD_PATH', __DIR__ . '/../uploads/wisata/');
define('UPLOAD_URL',  BASE_URL . '/uploads/wisata/');

define('SESSION_NAME', 'wisata_admin_session');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;border-radius:8px;margin:20px">
        <h2>❌ Koneksi Database Gagal</h2>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
        <p><strong>Solusi:</strong> Pastikan XAMPP berjalan dan database sudah diimport via phpMyAdmin.</p>
    </div>');
}
?>
