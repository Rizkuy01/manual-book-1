<?php
require_once '../config.php';
session_start();

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

if ($stmt->execute()) {
    echo "<script>alert('Data mesin berhasil disimpan!'); window.location='../views/system.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data mesin!'); window.history.back();</script>";
}
$stmt->close();
