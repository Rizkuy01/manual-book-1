<?php
session_start();
if (isset($_SESSION['pending_user'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - Manual Book System</title>
  <link rel="stylesheet" href="../src/output.css">
</head>

<body class="relative flex items-center justify-center min-h-screen overflow-hidden bg-gray-900">
  <div class="absolute inset-0">
    <img src="../src/img/bg.png" alt="Background" 
         class="w-full h-full object-cover brightness-50 opacity-95">
  </div>
  <div class="absolute inset-0 bg-black/30"></div>

  <!-- Login Card -->
  <div class="relative z-10 bg-white border border-slate-200 rounded-xl shadow-lg 
              w-full max-w-md px-8 py-7 text-center">

    <!-- Logo -->
    <div class="flex justify-center mb-3 mt-4">
      <img src="../src/img/kyb.png" alt="KYB Logo" class="w-32">
    </div>

    <!-- Title -->
    <h1 class="text-xl font-bold text-slate-800">LOGIN</h1>
    <p class="text-sm font-semibold text-slate-600 mb-6 tracking-widest">
      MANUAL BOOK SYSTEM
    </p>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="mb-4 text-red-700 bg-red-50 border border-red-300 px-4 py-2 rounded-md text-sm">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="login_handler.php" method="POST" class="space-y-4">
      <input type="text" name="npk" placeholder="NPK"
             class="w-full px-4 py-3 border border-slate-300 rounded-md 
                    focus:outline-none focus:ring-2 focus:ring-red-400 placeholder-gray-700" required>

      <input type="password" name="password" placeholder="Password"
             class="w-full px-4 py-3 border border-slate-300 rounded-md 
                    focus:outline-none focus:ring-2 focus:ring-red-400 placeholder-gray-700" required>

      <div class="flex items-center gap-3">
        <img src="captcha.php" alt="Captcha" class="h-12 border border-slate-300 rounded-md">
        <input type="text" name="captcha" placeholder="Masukkan kode Captcha"
               class="flex-1 px-4 py-3 border border-slate-300 rounded-md 
                      focus:outline-none focus:ring-2 focus:ring-red-400 placeholder-gray-700" required>
      </div>

      <button type="submit"
              class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold 
                     py-3 mb-6 rounded-md transition">
        LOGIN
      </button>
    </form>
  </div>

  <script>
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll("input");

    inputs.forEach((input, index) => {
      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          const nextInput = inputs[index + 1];
          if (nextInput) {
            nextInput.focus();
          } else {
            form.submit();
          }
        }
      });
    });
  </script>

</body>
</html>
