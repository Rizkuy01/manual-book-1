<?php
require_once '../config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request'); window.history.back();</script>";
    exit;
}

$dept_id = $_POST['dept_id'] ?? '';
$section_id = $_POST['section_id'] ?? '';
$subsection_id = $_POST['subsection_id'] ?? '';
$machine_name = trim($_POST['machine_name'] ?? '');

if ($dept_id === '' || $section_id === '' || $subsection_id === '' || $machine_name === '') {
    echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
    exit;
}

// Ambil nama departemen
$stmt = $connMB->prepare("SELECT dept_name FROM department WHERE id = ?");
$stmt->bind_param('i', $dept_id);
$stmt->execute();
$stmt->bind_result($dept_name);
$stmt->fetch();
$stmt->close();

// Ambil nama section
$stmt = $connMB->prepare("SELECT name FROM section WHERE id = ?");
$stmt->bind_param('i', $section_id);
$stmt->execute();
$stmt->bind_result($section_name);
$stmt->fetch();
$stmt->close();

// Ambil nama subsection
$stmt = $connMB->prepare("SELECT name FROM subsection WHERE id = ?");
$stmt->bind_param('i', $subsection_id);
$stmt->execute();
$stmt->bind_result($subsection_name);
$stmt->fetch();
$stmt->close();

// Simpan ke tabel
$stmt = $connMB->prepare("INSERT INTO contoh_mesin (machine_name, subsection_name, section_name, dept_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $machine_name, $subsection_name, $section_name, $dept_name);
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
    text: 'Machine berhasil ditambahkan!',
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

