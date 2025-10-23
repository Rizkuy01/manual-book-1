<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

$machine_id     = (int)($_POST['machine_id'] ?? 0);
$nama_file      = trim($_POST['nama_file'] ?? '');
$dept_id        = (int)($_POST['dept_id'] ?? 0);
$section_id     = (int)($_POST['section_id'] ?? 0);
$subsection_id  = (int)($_POST['subsection_id'] ?? 0);
$file           = $_FILES['pdf_file'] ?? null;

function showAlert($icon, $title, $text, $redirect = null) {
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
      <script>
        Swal.fire({
          icon: "' . $icon . '",
          title: "' . $title . '",
          text: "' . $text . '",
          confirmButtonColor: "#2563eb"
        }).then(() => {
          ' . ($redirect ? 'window.location.href="' . $redirect . '";' : 'history.back();') . '
        });
      </script>
    </body>
    </html>';
    exit;
}

if (!$machine_id || !$nama_file || empty($file['name'])) {
    showAlert('error', 'Data tidak lengkap', 'Semua field wajib diisi!');
}

// VALIDASI FILE UPLOAD
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
$ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($mime !== 'application/pdf' || $ext !== 'pdf') {
    showAlert('error', 'Format tidak valid', 'File harus berupa PDF.');
}

$uploadDir = 'C:/laragon/www/manual-book-files/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama_file));
$newName = $safeName . '_' . time() . '.pdf';
$targetPath = $uploadDir . $newName;
$filePath = 'manual-book-files/' . $newName;


// Pindahkan file ke direktori tujuan
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    showAlert('error', 'Upload gagal', 'Gagal menyimpan file ke server.');
}

// Hapus file lama dari server
$stmtOld = $connMB->prepare("SELECT file_path FROM book_file WHERE machine_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$stmtOld->bind_param("i", $machine_id);
$stmtOld->execute();
$old = $stmtOld->get_result()->fetch_assoc();
$stmtOld->close();

if ($old && !empty($old['file_path'])) {
    $oldPath = 'C:/laragon/www/' . str_replace('/', '\\', $old['file_path']);
    if (is_file($oldPath)) unlink($oldPath);
}

// Hapus data lama sebelum simpan yang baru
$stmtDel = $connMB->prepare("DELETE FROM book_file WHERE machine_id = ?");
$stmtDel->bind_param("i", $machine_id);
$stmtDel->execute();
$stmtDel->close();

$stmt = $connMB->prepare("
    INSERT INTO book_file (nama_file, file_path, dept_id, section_id, subsection_id, machine_id, uploaded_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("ssiiii", $nama_file, $filePath, $dept_id, $section_id, $subsection_id, $machine_id);

if ($stmt->execute()) {
    showAlert('success', 'Berhasil!', 'File manual book berhasil diperbarui.', 
        '../index.php?page=detail_machine&id=' . $machine_id . '&success=1');
} else {
    showAlert('error', 'Database Error', 'Gagal menyimpan ke database.');
}

$stmt->close();
?>
