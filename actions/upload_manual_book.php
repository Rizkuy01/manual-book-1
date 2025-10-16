<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid.']);
    exit;
}

$nama_file = trim($_POST['nama_file'] ?? '');
$dept_id = $_POST['dept_id'] ?? null;
$section_id = $_POST['section_id'] ?? null;
$subsection_id = $_POST['subsection_id'] ?? null;
$machine_id = $_POST['machine_id'] ?? null;
$file = $_FILES['pdf_file'] ?? null;

// Validasi input
if (empty($nama_file) || empty($file['name']) || !$dept_id || !$section_id || !$subsection_id || !$machine_id) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi (termasuk Departemen, Section, Subsection, dan Machine).']);
    exit;
}

// Folder penyimpanan
$uploadDir = 'C:/laragon/www/manual-book-files/';

// Cek folder
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Validasi ekstensi file
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya file PDF yang diizinkan.']);
    exit;
}

// Buat nama file aman
$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama_file));
$newName = $safeName . '_' . time() . '.pdf';
$targetPath = $uploadDir . $newName;

// URL public
$fileURL = 'C:\laragon\www\manual-book-files/' . $newName;

// Upload file ke folder
if (move_uploaded_file($file['tmp_name'], $targetPath)) {

    // Simpan ke database
    $stmt = $connMB->prepare("
        INSERT INTO book_file (nama_file, file_path, dept_id, section_id, subsection_id, machine_id, uploaded_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssiiii", $nama_file, $fileURL, $dept_id, $section_id, $subsection_id, $machine_id);

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
