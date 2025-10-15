<?php
require_once '../config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request.']));
}

$section_id = $_POST['section_id'] ?? '';
$subsection_name = trim($_POST['subsection_name'] ?? '');

if ($section_id === '' || $subsection_name === '') {
    die(json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']));
}

$stmt = $connMB->prepare("INSERT INTO subsection (section_id, name) VALUES (?, ?)");
$stmt->bind_param('is', $section_id, $subsection_name);
$success = $stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if ($success): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Subsection berhasil ditambahkan!',
    confirmButtonColor: '#dc2626'
}).then(() => {
    window.location = '../views/dashboard_home.php';
});
<?php else: ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: 'Terjadi kesalahan saat menyimpan data.',
    confirmButtonColor: '#dc2626'
}).then(() => {
    window.history.back();
});
<?php endif; ?>
</script>
</body>
</html>

