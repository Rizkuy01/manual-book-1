<?php
session_start();

// Hapus semua data sesi
$_SESSION = [];
session_unset();
session_destroy();

// Arahkan ke halaman login
header("Location: login.php");
exit;
