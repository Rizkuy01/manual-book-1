<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request.']));
}

$dept_id = $_POST['dept_id'] ?? '';
$section_name = trim($_POST['section_name'] ?? '');

if ($dept_id === '' || $section_name === '') {
    die(json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']));
}

$stmt = $connMB->prepare("INSERT INTO section (dept_id, name) VALUES (?, ?)");
$stmt->bind_param('is', $dept_id, $section_name);

if ($stmt->execute()) {
    echo "<script>alert('Section berhasil ditambahkan!'); window.location='../views/system.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan section.'); window.history.back();</script>";
}
$stmt->close();
