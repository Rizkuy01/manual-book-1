<?php
session_start();
include '../config.php';

$npk      = trim($_POST['npk']);
$password = trim($_POST['password']);
$captcha  = trim($_POST['captcha']);

// Validasi captcha
if ($captcha !== $_SESSION['captcha']) {
    $_SESSION['error'] = "Captcha salah!";
    header("Location: login.php");
    exit;
}

// Ambil user dari database lembur1
$sql = "SELECT * FROM ct_users WHERE npk = '$npk' LIMIT 1";
$result = mysqli_query($connUser, $sql);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "NPK tidak ditemukan!";
    header("Location: login.php");
    exit;
}

$user = mysqli_fetch_assoc($result);

// Cek password (hash)
if (!password_verify($password, $user['pwd'])) {
    $_SESSION['error'] = "Password salah!";
    header("Location: login.php");
    exit;
}

// === Ambil nomor HP dari database ISD ===
$qHp = mysqli_query($connISD, "SELECT no_hp FROM hp WHERE npk = '$npk' LIMIT 1");
$hpData = mysqli_fetch_assoc($qHp);

if (!$hpData || empty($hpData['no_hp'])) {
    $_SESSION['error'] = "Nomor HP tidak ditemukan untuk NPK ini.";
    header("Location: login.php");
    exit;
}

$no_hp = $hpData['no_hp'];

// === Generate OTP ===
$otp = rand(100000, 999999);
$created = date("Y-m-d H:i:s");
$expired = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Simpan OTP ke DB manual-book.otp
mysqli_query($connMB, "
    INSERT INTO otp (npk, no_hp, kode_otp, created_at, expired_at)
    VALUES ('$npk', '$no_hp', '$otp', '$created', '$expired')
");

// Bersihkan OTP lama
mysqli_query($connMB, "DELETE FROM otp WHERE expired_at < NOW()");

// Simpan ke session sementara untuk verifikasi OTP
$_SESSION['otp_user'] = [
    'npk' => $user['npk'],
    'nama' => $user['full_name'],
    'no_hp' => $no_hp
];

// (Opsional) tampilkan kode OTP di session untuk uji lokal
$_SESSION['info_otp'] = "Kode OTP kamu: <b>$otp</b> (berlaku 5 menit).";

// Arahkan ke halaman verifikasi OTP
header("Location: verify_otp.php");
exit;
