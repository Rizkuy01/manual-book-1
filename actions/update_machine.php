<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Akses tidak valid'); window.history.back();</script>";
    exit;
}

$id = $_POST['id'] ?? 0;
$machine_name = trim($_POST['machine_name'] ?? '');
$code_machine = trim($_POST['code_machine'] ?? '');
$fixedasset = trim($_POST['fixedasset'] ?? '');
$maker = trim($_POST['maker'] ?? '');
$user = trim($_POST['user'] ?? '');
$dept_id = $_POST['dept_id'] ?? '';
$section_id = $_POST['section_id'] ?? '';
$subsection_id = $_POST['subsection_id'] ?? '';

if (!$id || !$machine_name) {
    echo "<script>alert('Data tidak lengkap'); window.history.back();</script>";
    exit;
}

$stmt = $connMB->prepare("
    UPDATE contoh_mesin 
    SET machine_name=?, code_machine=?, fixedasset=?, maker=?, user=?, dept_id=?, section_id=?, subsection_id=?
    WHERE id=?
");
$stmt->bind_param("ssssssiii", $machine_name, $code_machine, $fixedasset, $maker, $user, $dept_id, $section_id, $subsection_id, $id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data mesin berhasil diperbarui.',
            confirmButtonColor: '#dc2626'
        }).then(() => {
            window.location = '../views/index.php?page=detail_machine&id={$id}';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menyimpan.',
            confirmButtonColor: '#dc2626'
        }).then(() => window.history.back());
    </script>";
}
?>
