<?php
require_once '../config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("<script>alert('Invalid request.'); window.history.back();</script>");
}

$dept_id = $_POST['dept_id'] ?? '';
$section_name = trim($_POST['section_name'] ?? '');

if ($dept_id === '' || $section_name === '') {
    exit("<script>alert('Semua field wajib diisi.'); window.history.back();</script>");
}

$stmt = $connMB->prepare("INSERT INTO section (name, dept_id) VALUES (?, ?)");
$stmt->bind_param('si', $section_name, $dept_id);
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
    text: 'Section berhasil ditambahkan!',
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
