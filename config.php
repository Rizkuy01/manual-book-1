<?php
date_default_timezone_set('Asia/Jakarta');

// AUTHENTICATEION DATABASE
// DB USER
$host = "localhost";
$user = "root";
$pass = "";
$db   = "lembur1";

$connUser = mysqli_connect($host, $user, $pass, $db);
if (!$connUser) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// MAIN DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "manual_book"; 

$connMB = mysqli_connect($host, $user, $pass, $db);
if (!$connMB) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ISD
$host_isd = 'localhost';
$user_isd = 'root';
$pass_isd = '';
$db_isd   = 'isd';

$connISD = new mysqli($host_isd, $user_isd, $pass_isd, $db_isd);
if ($connISD->connect_error) {
    die("Koneksi ke DB ISD gagal: " . $connISD->connect_error);
}

?>
