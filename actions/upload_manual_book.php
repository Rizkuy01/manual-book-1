<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Metode tidak valid.']));
}

$nama_file = trim($_POST['nama_file'] ?? '');
$dept_id = (int)($_POST['dept_id'] ?? 0);
$section_id = (int)($_POST['section_id'] ?? 0);
$subsection_id = (int)($_POST['subsection_id'] ?? 0);
$machine_id = (int)($_POST['machine_id'] ?? 0);
$file = $_FILES['pdf_file'] ?? null;

if (!$nama_file || !$dept_id || !$section_id || !$subsection_id || !$machine_id || empty($file['name'])) {
    exit(json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']));
}

// Folder tujuan luar project (C:/laragon/www/manual-book-files/)
$uploadDir = 'C:/laragon/www/manual-book-files/';

// Jika folder belum ada, buat
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Nama file baru aman
$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama_file));
$newName = $safeName . '_' . uniqid() . '.pdf';

// Path absolut dan relatif
$targetPath = $uploadDir . $newName;
$relativePath = 'manual-book-files/' . $newName; // ini yang masuk DB

// Upload file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    exit(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file di server.']));
}

// Simpan ke database
$stmt = $connMB->prepare("
  INSERT INTO book_file (nama_file, file_path, dept_id, section_id, subsection_id, machine_id, uploaded_at)
  VALUES (?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("ssiiii", $nama_file, $relativePath, $dept_id, $section_id, $subsection_id, $machine_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Upload berhasil']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database.']);
}

