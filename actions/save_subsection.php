<?php
require_once '../config.php';
session_start();

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

if ($stmt->execute()) {
    echo "<script>alert('Subsection berhasil ditambahkan!'); window.location='../views/system.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan subsection.'); window.history.back();</script>";
}
$stmt->close();
?>
