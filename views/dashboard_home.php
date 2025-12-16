<?php
if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include './config.php';

// === Statistik Umum ===
$total_books = $connMB->query("SELECT COUNT(*) AS total FROM book_file")->fetch_assoc()['total'];
$total_machine = $connMB->query("SELECT COUNT(*) AS total FROM contoh_mesin")->fetch_assoc()['total'];
$upload_today = $connMB->query("SELECT COUNT(*) AS total FROM book_file WHERE DATE(uploaded_at) = CURDATE()")->fetch_assoc()['total'];

// === Data Grafik (Distribusi Manual Book per Departemen) ===
$dept_data = $connMB->query("
    SELECT d.dept_name AS name, COUNT(b.id) AS total
    FROM department d
    LEFT JOIN book_file b ON b.dept_id = d.id
    WHERE d.dept_name NOT IN ('MIS', 'QA')
    GROUP BY d.id
    ORDER BY d.dept_name ASC
");

$departments_name = [];
$departments_count = [];
while ($row = $dept_data->fetch_assoc()) {
    $departments_name[] = $row['name'];
    $departments_count[] = (int)$row['total'];
}

// === Upload Terbaru ===
$uploads = $connMB->query("
    SELECT b.nama_file, d.dept_name, b.uploaded_at 
    FROM book_file b
    LEFT JOIN department d ON d.id = b.dept_id
    ORDER BY b.uploaded_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* === Warna Manual Badge Departemen === */
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      color: #fff;
      text-transform: capitalize;
    }
    .badge-prod1 { background-color: #ef4444; }   /* merah */
    .badge-prod2 { background-color: #0284c7; }   /* biru muda */
    .badge-prod3 { background-color: #1e3a8a; }   /* biru tua */
    .badge-prod4 { background-color: #ca8a04; }   /* kuning keemasan */
    .badge-prod5 { background-color: #16a34a; }   /* hijau */
    .badge-default { background-color: #94a3b8; } /* abu */

    /* ====== STATISTIC CARDS ====== */
    .stats-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-card {
      flex: 1;
      min-width: 250px;
      border-radius: 12px;
      padding: 20px 25px;
      color: white;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .stat-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .stat-info h4 {
      font-size: 15px;
      font-weight: 500;
      margin: 0;
      opacity: 0.9;
    }

    .stat-info h2 {
      font-size: 36px;
      font-weight: 700;
      margin: 8px 0;
    }

    .stat-info p {
      font-size: 13px;
      opacity: 0.9;
      margin: 0;
    }

    .icon {
      font-size: 38px;
      opacity: 0.8;
    }

    /* ====== GRADIENT COLORS ====== */
    .gradient-blue {
      background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }

    .gradient-green {
      background: linear-gradient(135deg, #10b981, #34d399);
    }

    .gradient-orange {
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .stats-container {
        flex-direction: column;
      }
    }
  </style>
</head>

<body class="bg-slate-100 min-h-screen p-6">

  <!-- Header Sambutan -->
  <div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-2xl font-bold text-slate-800 mb-2">
      Selamat datang, <span class="text-red-700"><?= htmlspecialchars($_SESSION['pending_user']['nama'] ?? '') ?></span> 👋
    </h2>
    <p class="text-slate-500">Berikut ringkasan aktivitas Manual Book System Anda.</p>
    <div id="clock" class="text-sm text-slate-400 mt-1"></div>
  </div>

    <!-- Statistik Cards -->
  <div class="stats-container">
    <div class="stat-card gradient-blue">
      <div class="stat-content">
        <div class="icon">
          <i class="fa fa-book"></i>
        </div>
        <div class="stat-info">
          <h4>Total Manual Book</h4>
          <h2><?= $total_books ?></h2>
          <p>Total File</p>
        </div>
      </div>
    </div>

    <div class="stat-card gradient-green">
      <div class="stat-content">
        <div class="icon">
          <i class="fa fa-cogs"></i>
        </div>
        <div class="stat-info">
          <h4>Total Machine</h4>
          <h2><?= $total_machine ?></h2>
          <p>Mesin Terdaftar</p>
        </div>
      </div>
    </div>

    <div class="stat-card gradient-orange">
      <div class="stat-content">
        <div class="icon">
          <i class="fa fa-sync"></i>
        </div>
        <div class="stat-info">
          <h4>Upload Hari Ini</h4>
          <h2><?= $upload_today ?></h2>
          <p>Tanggal <?= date('d M Y') ?></p>
        </div>
      </div>
    </div>
  </div>


  <!-- Grafik & Upload Terbaru -->
  <div class="flex flex-wrap gap-3 mb-6">
    
    <!-- Grafik -->
    <div class="bg-white rounded-xl shadow p-6 flex-1 min-w-[350px]" style="max-width: 55%;">
      <h3 class="font-semibold text-slate-700 mb-4">
        📊 Distribusi Manual Book per Departemen
      </h3>
      <div style="height: 250px;">
        <canvas id="deptChart"></canvas>
      </div>
    </div>

    <!-- Upload Terbaru -->
    <div class="bg-white rounded-xl shadow p-6 flex-1 min-w-[350px]" style="max-width: 50%;">
      <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-slate-700">🕒 Upload Terbaru</h3>
        <a href="?page=input_manual_book" 
           class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md shadow">
          + Upload Manual Book
        </a>
      </div>

      <table class="w-full text-sm border-t border-slate-200">
        <thead>
          <tr class="text-left text-slate-500 border-b">
            <th class="py-2">Nama File</th>
            <th>Departemen</th>
            <th>Tanggal Upload</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($uploads->num_rows > 0): ?>
            <?php while ($row = $uploads->fetch_assoc()): ?>
              <?php
                $dept = $row['dept_name'] ?? 'Unknown';
                $badgeClass = match ($dept) {
                    'Production 1' => 'badge-prod1',
                    'Production 2' => 'badge-prod2',
                    'Production 3' => 'badge-prod3',
                    'Production 4' => 'badge-prod4',
                    'Production 5' => 'badge-prod5',
                    default => 'badge-default',
                };
              ?>
              <tr class="border-b hover:bg-slate-50 transition">
                <td class="py-2"><?= htmlspecialchars($row['nama_file'] ?? '') ?></td>
                <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($dept) ?></span></td>
                <td class="text-center"><?= date('d M Y H:i', strtotime($row['uploaded_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-center text-slate-400 py-4">Belum ada data upload.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Clock -->
  <script>
    function updateTime() {
      const now = new Date();
      document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateTime, 1000);
    window.onload = updateTime;
  </script>

  <!-- Chart -->
  <script>
    const ctx = document.getElementById('deptChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($departments_name) ?>,
        datasets: [{
          label: 'Jumlah Manual Book',
          data: <?= json_encode($departments_count) ?>,
          backgroundColor: '#dc2626'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => ctx.parsed.y + ' File'
            }
          }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>

</body>
</html>
