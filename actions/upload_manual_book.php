<?php
session_start();
include '../config.php';

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid.']);
    exit;
}

$nama_file = trim($_POST['nama_file'] ?? '');
$dept_id = $_POST['dept_id'] ?? null;
$section_id = $_POST['section_id'] ?? null;
$subsection_id = $_POST['subsection_id'] ?? null;
$file = $_FILES['pdf_file'] ?? null;

// Validasi input
if (empty($nama_file) || empty($file['name']) || !$dept_id || !$section_id || !$subsection_id) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi (termasuk Departemen, Section, dan Subsection).']);
    exit;
}

// Folder penyimpanan
$uploadDir = 'C:/Users/rizky/OneDrive/Pictures/manualbook/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Validasi ekstensi
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya file PDF yang diizinkan.']);
    exit;
}

// Sanitasi nama file
$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama_file));
$newName = $safeName . '_' . time() . '.pdf';
$targetPath = $uploadDir . $newName;

// Upload file
if (move_uploaded_file($file['tmp_name'], $targetPath)) {

    $stmt = $connMB->prepare("
        INSERT INTO book_file (nama_file, file_path, dept_id, section_id, subsection_id, uploaded_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssiii", $nama_file, $targetPath, $dept_id, $section_id, $subsection_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => "File <strong>{$newName}</strong> berhasil diupload."
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data ke database.'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengupload file ke server.'
    ]);
}
exit;
