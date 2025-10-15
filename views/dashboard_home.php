<?php
if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include './config.php';

// === Statistik Umum ===
$total_books = $connMB->query("SELECT COUNT(*) AS total FROM book_file")->fetch_assoc()['total'];
$total_dept = $connMB->query("SELECT COUNT(*) AS total FROM department WHERE dept_name NOT IN ('MIS', 'QA')")->fetch_assoc()['total'];
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-100 min-h-screen p-6">

  <!-- Header Sambutan -->
  <div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-2xl font-bold text-slate-800 mb-2">
      Selamat datang, <span class="text-red-700"><?= htmlspecialchars($_SESSION['pending_user']['nama']) ?></span> 👋
    </h2>
    <p class="text-slate-500">Berikut ringkasan aktivitas Manual Book System Anda.</p>
    <div id="clock" class="text-sm text-slate-400 mt-1"></div>
  </div>

  <!-- Statistik Cards -->
<div class="flex flex-wrap justify-between py-5 gap-4 mb-8" style="gap: 20px;">
  <div class="flex-1 min-w-[200px] border-l-4 bg-red-600 rounded-lg shadow p-4 text-center hover:shadow-md transition">
    <p class="text-sm text-white">Total Manual Book</p>
    <h3 class="text-3xl font-bold text-white"><?= $total_books ?></h3>
  </div>
  <div class="flex-1 min-w-[200px] border-l-4 bg-sky-600 rounded-lg shadow p-4 text-center hover:shadow-md transition">
    <p class="text-sm text-white">Total Departemen</p>
    <h3 class="text-3xl font-bold text-white"><?= $total_dept ?></h3>
  </div>
  <div class="flex-1 min-w-[200px] border-l-4 bg-yellow-600 rounded-lg shadow p-4 text-center hover:shadow-md transition">
    <p class="text-sm text-white">Upload Hari Ini</p>
    <h3 class="text-3xl font-bold text-white"><?= $upload_today ?></h3>
  </div>
</div>

<!-- Grafik dan Tabel Sampingan -->
<div class="flex flex-wrap gap-3 mb-6">

  <!-- Grafik di Kiri -->
  <div class="bg-white rounded-xl  border-red-600 shadow p-6 flex-1 min-w-[350px]" style="max-width: 55%;">
    <h3 class="font-semibold text-slate-700 mb-4">
      📊 Distribusi Manual Book per Departemen
    </h3>
    <div style="height: 250px;">
      <canvas id="deptChart"></canvas>
    </div>
  </div>

  <!-- Upload Terbaru di Kanan -->
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
            <tr class="border-b hover:bg-slate-50 transition">
              <td class="py-2"><?= htmlspecialchars($row['nama_file']) ?></td>
              <td>
                <?php
                $dept = $row['dept_name'];
                $colors = [
                    'Production 1' => 'bg-red-500 text-white',
                    'Production 2' => 'bg-sky-600 text-white',
                    'Production 3' => 'bg-blue-900 text-white',
                    'Production 4' => 'bg-yellow-600 text-white',
                    'Production 5' => 'bg-green-600 text-white',
                ];
                $colorClass = $colors[$dept] ?? 'bg-slate-400 text-white';
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                  <?= htmlspecialchars($dept) ?>
                </span>
              </td>
              <td><?= date('d M Y H:i', strtotime($row['uploaded_at'])) ?></td>
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


  <!-- Script Jam -->
  <script>
  function updateTime() {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour12: false });
      document.getElementById('clock').textContent = time;
    }
    setInterval(updateTime, 1000);
    window.onload = updateTime;
  </script>

  <!-- Chart JS -->
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
            label: function(context) {
            return context.parsed.y + ' File';
            }
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
