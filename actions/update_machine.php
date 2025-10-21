<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

$id = intval($_POST['id']);
$machine_name = trim($_POST['machine_name'] ?? '');
$code_machine = trim($_POST['code_machine'] ?? '');
$maker = trim($_POST['maker'] ?? '');
$user = trim($_POST['user'] ?? '');
$fixedasset = trim($_POST['fixedasset'] ?? '');

if (!$id) die('Invalid ID');

$stmt = $connMB->prepare("
    UPDATE contoh_mesin
    SET machine_name = ?, code_machine = ?, maker = ?, user = ?, fixedasset = ?
    WHERE id = ?
");
$stmt->bind_param("ssssii", $machine_name, $code_machine, $maker, $user, $fixedasset, $id);

if ($stmt->execute()) {
    header("Location: ../index.php?page=detail_machine&id=$id&success=1");
    exit;
} else {
    die('Gagal memperbarui data mesin.');
}
?>
