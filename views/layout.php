<?php $user = $_SESSION['pending_user']; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard</title>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="src/output.css">
  <link rel="stylesheet" href="src/fontawesome/css/all.min.css">
  <script>
    function updateTime() {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour12: false });
      document.getElementById('clock').textContent = time;
    }
    setInterval(updateTime, 1000);
    window.onload = updateTime;
  </script>
  <style>
/* Overlay (latar belakang gelap) */
.modal-overlay {
  display: none; /* default disembunyikan */
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* Box isi modal */
.modal-box {
  background: #fff;
  border-radius: 10px;
  padding: 24px;
  width: 100%;
  max-width: 450px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  position: relative;
  animation: fadeIn 0.2s ease-in-out;
}

/* Tombol close */
.close-btn {
  position: absolute;
  top: 10px;
  right: 12px;
  font-size: 18px;
  color: #555;
  cursor: pointer;
  border: none;
  background: none;
}

/* Animasi */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Input & select style ringan */
.modal-box input,
.modal-box select {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-top: 4px;
  margin-bottom: 10px;
}

.modal-box label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.modal-box button {
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  font-weight: 600;
  cursor: pointer;
}
</style>
</head>
<body class="flex h-screen bg-slate-100 text-slate-800">
  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
    <div class="p-6 border-b border-slate-200">
      <img src="src/img/kyb.png" alt="Kayaba Logo" class="mx-auto max-w-[120px]">
    </div>

    <nav class="flex-1 p-4 space-y-1">
      <a href="index.php?page=home" class="flex items-center p-2 rounded-lg hover:bg-slate-100 <?= ($page=='home')?'active-link':'' ?>">
        <i class="fa-solid fa-globe pr-2"></i> Dashboard
      </a>
      <a href="index.php?page=list_machine" class="flex items-center p-2 rounded-lg hover:bg-slate-100 <?= ($page=='list_machine')?'active-link':'' ?>">
        <i class="fa-solid fa-list-ul pr-2"></i> List Machine
      </a>
      <a href="index.php?page=input_manual_book" class="flex items-center p-2 rounded-lg hover:bg-slate-100 <?= ($page=='input_manual_book')?'active-link':'' ?>">
        <i class="fa-solid fa-file-upload pr-2"></i> Upload Manual Book
      </a>

      <!-- Add Machine ambil dari portal asset management -->
      <!-- <a href="index.php?page=system" class="flex items-center p-2 rounded-lg hover:bg-slate-100 <?= ($page=='system')?'active-link':'' ?>">
        <i class="fa-solid fa-square-plus pr-2"></i> Add System Data
      </a> -->
      
    </nav>
  </aside>

  <!-- Main -->
  <div class="flex-1 flex flex-col">
    <!-- Navbar -->
    <header class="bg-[#142348] text-yellow-500 flex items-center justify-between px-6 h-16 shadow">
      <h1 class="text-lg font-semibold">Manual Book System</h1>
      <div id="clock" class="text-lg font-semibold"></div>
      <div class="relative">
        <button id="profileBtn" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#1f2d55]">
          <span class="font-medium text-white"><?= htmlspecialchars($user['nama'] ?? 'User') ?></span>
          <div class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#142348]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
        </button>
        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md border">
          <a href="auth/logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
        </div>
      </div>
    </header>

    <!-- Content area -->
    <main class="flex-1 p-6 overflow-auto bg-slate-100">
      <?php
      switch ($page) {
        case 'input_manual_book':
          include 'views/input_manual_book.php';
          break;
        case 'system':
          include 'views/system.php';
          break;
        case 'list_machine':
          include 'views/list_machine.php';
          break;
        case 'detail_machine':
          include 'views/detail_machine.php';
          break;
        default:
          include 'views/dashboard_home.php';
      }
      ?>
    </main>
  </div>

  <style>
    .active-link {
      background-color: #fee2e2;
      color: #b91c1c;
      font-weight: 600;
    }
  </style>

  <script>
    const btn = document.getElementById('profileBtn');
    const menu = document.getElementById('profileMenu');
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });
    document.addEventListener('click', () => menu.classList.add('hidden'));
  </script>
</body>
</html>
