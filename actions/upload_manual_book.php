<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_file = trim($_POST['nama_file']);
    $file = $_FILES['pdf_file'];

    if (empty($nama_file) || empty($file['name'])) {
        $_SESSION['error'] = "Nama file dan file PDF wajib diisi.";
        header("Location: ../views/input_manual_book.php");
        exit;
    }

    // folder penyimpanan
    $uploadDir = '../uploads/manual_book/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // validasi file PDF
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $_SESSION['error'] = "Hanya file PDF yang diizinkan.";
        header("Location: ../views/input_manual_book.php");
        exit;
    }

    // buat nama baru unik
    $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', basename($file['name']));
    $targetPath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $stmt = $connMB->prepare("INSERT INTO book_file (nama_file, file_path, uploaded_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $nama_file, $targetPath);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "File berhasil diupload.";
    } else {
        $_SESSION['error'] = "Gagal mengupload file.";
    }

    header("Location: ../views/input_manual_book.php");
    exit;
} else {
    header("Location: ../views/input_manual_book.php");
    exit;
}
