<?php
session_start();
$captcha_image = "captcha.php?" . time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - Sistem Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-md mx-auto p-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
      <h2 class="text-2xl font-semibold text-center mb-4">Login Sistem</h2>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <form action="login_handler.php" method="POST" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">NPK</label>
          <input type="text" name="npk" required
                 class="w-full border border-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <input type="password" name="password" required
                 class="w-full border border-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400" />
        </div>

        <div class="flex items-center gap-3">
          <div class="flex-shrink-0">
            <img src="<?= $captcha_image ?>" alt="captcha" class="h-12 w-32 object-cover border rounded">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-slate-700 mb-1">Captcha</label>
            <input type="text" name="captcha" placeholder="Masukkan kode" required
                   class="w-full border border-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400" />
          </div>
        </div>

        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white py-2 rounded-md font-semibold">
          Login
        </button>
      </form>
    </div>
  </div>
</body>
</html>
