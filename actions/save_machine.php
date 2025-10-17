<?php
require_once '../config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request'); window.history.back();</script>";
    exit;
}

// Ambil data dari form
$dept_id       = $_POST['dept_id'] ?? '';
$section_id    = $_POST['section_id'] ?? '';
$subsection_id = $_POST['subsection_id'] ?? '';
$machine_name  = trim($_POST['machine_name'] ?? '');
$code_machine  = trim($_POST['code_machine'] ?? '');
$fixedasset    = trim($_POST['fixedasset'] ?? '');
$maker         = trim($_POST['maker'] ?? '');
$user          = trim($_POST['user'] ?? '');

// Validasi data wajib
if (
    $dept_id === '' || 
    $section_id === '' || 
    $subsection_id === '' || 
    $machine_name === '' || 
    $code_machine === '' || 
    $fixedasset === '' || 
    $maker === '' || 
    $user === ''
) {
    echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
    exit;
}

// Simpan data ke tabel contoh_mesin
$stmt = $connMB->prepare("
    INSERT INTO contoh_mesin 
    (machine_name, subsection_id, section_id, dept_id, code_machine, user, fixedasset, maker, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

if (!$stmt) {
    die("Prepare failed: " . $connMB->error);
}

$stmt->bind_param(
    'siiissii', 
    $machine_name, 
    $subsection_id, 
    $section_id, 
    $dept_id, 
    $code_machine, 
    $user, 
    $fixedasset, 
    $maker
);

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
    text: 'Data machine berhasil ditambahkan.',
    confirmButtonColor: '#dc2626'
}).then(() => {
    window.location = '../index.php?page=system';
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
