<?php
session_start();
include '../config.php';

if (!isset($_SESSION['otp_user'])) {
    header("Location: login.php");
    exit;
}

$input_otp = trim($_POST['otp']);
$npk = $_SESSION['otp_user']['npk'];

$sql = "SELECT * FROM otp WHERE npk = '$npk' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connMB, $sql);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "OTP tidak ditemukan!";
    header("Location: verify_otp.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

// Validasi OTP
$current_time = date("Y-m-d H:i:s");

if ($input_otp != $data['kode_otp']) {
    $_SESSION['error'] = "Kode OTP salah!";
    header("Location: verify_otp.php");
    exit;
}

if ($current_time > $data['expired_at']) {
    $_SESSION['error'] = "Kode OTP telah kedaluwarsa!";
    header("Location: verify_otp.php");
    exit;
}

// OTP valid → login selesai
$_SESSION['pending_user'] = [
    'npk' => $npk,
    'nama' => $_SESSION['otp_user']['nama']
];

// Bersihkan sesi sementara OTP
unset($_SESSION['otp_user']);

header("Location: ../index.php");
exit;
