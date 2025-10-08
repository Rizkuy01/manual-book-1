<?php
session_start();
if (!isset($_SESSION['otp_user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verifikasi OTP</title>
  <link rel="stylesheet" href="../src/output.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md text-center">
    <h2 class="text-2xl font-semibold mb-4">Verifikasi OTP</h2>

    <?php if (isset($_SESSION['info_otp'])): ?>
      <div class="mb-4 text-yellow-700 bg-yellow-50 p-3 rounded">
        <?= $_SESSION['info_otp']; unset($_SESSION['info_otp']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="mb-4 text-yellow-600 bg-yellow-50 p-3 rounded">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="proses_otp.php" method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Masukkan Kode OTP</label>
        <input type="text" name="otp" maxlength="6" required
               class="w-full border border-slate-200 rounded-md px-3 py-2 text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-red-400" />
      </div>

      <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md font-semibold">
        Verifikasi
      </button>
    </form>
  </div>
</body>
</html>
