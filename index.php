<?php
session_start();
if (!isset($_SESSION['pending_user'])) {
    header("Location: auth/login.php");
    exit;
}

$page = $_GET['page'] ?? 'home';
include 'views/layout.php';
