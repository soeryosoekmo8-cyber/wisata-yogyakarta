<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$id     = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? 'get';

if ($id <= 0) {
    echo json_encode(['success' => false, 'msg' => 'ID tidak valid']);
    exit;
}

// ============================================
// POST: Kirim komentar baru
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'komentar') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $komentar = trim($_POST['komentar'] ?? '');
    $rating   = intval($_POST['rating'] ?? 5);

    // Validasi
    if (empty($nama) || empty($komentar)) {
        echo json_encode(['success' => false, 'msg' => 'Nama dan komentar wajib diisi.']);
        exit;
    }
    if (strlen($nama) > 100) {
        echo json_encode(['success' => false, 'msg' => 'Nama terlalu panjang.']);
        exit;
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'msg' => 'Format email tidak valid.']);
        exit;
    }
    if (strlen($komentar) > 1000) {
        echo json_encode(['success' => false, 'msg' => 'Komentar maksimal 1000 karakter.']);
        exit;
    }
    if ($rating < 1 || $rating > 5) {
        $rating = 5;
    }

    // Cek wisata ada
    $cek = $pdo->prepare("SELECT id FROM wisata WHERE id = ? AND status = 'aktif'");
    $cek->execute([$id]);
    if (!$cek->fetch()) {
        echo json_encode(['success' => false, 'msg' => 'Wisata tidak ditemukan.']);
        exit;
    }

    // Simpan komentar (status pending, dimoderasi admin)
    $stmt = $pdo->prepare("
        INSERT INTO komentar (wisata_id, nama, email, komentar, rating, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$id, $nama, $email ?: null, $komentar, $rating]);

    echo json_encode(['success' => true, 'msg' => 'Komentar berhasil dikirim dan menunggu moderasi. Terima kasih!']);
    exit;
}

// ============================================
// GET: Ambil data wisata + komentar
// ============================================
$stmt = $pdo->prepare("
    SELECT w.*, k.nama AS kategori 
    FROM wisata w 
    JOIN kategori k ON w.kategori_id = k.id 
    WHERE w.id = ? AND w.status = 'aktif'
");
$stmt->execute([$id]);
$wisata = $stmt->fetch();

if (!$wisata) {
    echo json_encode(['success' => false, 'msg' => 'Data tidak ditemukan']);
    exit;
}

// Ambil komentar yang sudah approved
$stmtK = $pdo->prepare("
    SELECT nama, komentar, rating, created_at
    FROM komentar
    WHERE wisata_id = ? AND status = 'approved'
    ORDER BY created_at DESC
    LIMIT 20
");
$stmtK->execute([$id]);
$komentar = $stmtK->fetchAll();

echo json_encode([
    'success'  => true,
    'wisata'   => $wisata,
    'komentar' => $komentar,
]);
?>