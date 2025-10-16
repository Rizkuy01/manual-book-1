<?php
include './config.php';

// Cek login
if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$result = $connMB->query("
    SELECT 
        b.id,
        d.dept_name AS department,
        s.name AS section,
        cm.machine_name AS machine_name
    FROM book_file b
    LEFT JOIN department d ON b.dept_id = d.id
    LEFT JOIN section s ON b.section_id = s.id
    LEFT JOIN contoh_mesin cm ON b.machine_id = cm.id
    ORDER BY d.dept_name, s.name, cm.machine_name
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>List Machine</title>
  <link rel="stylesheet" href="../src/output.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
  <script src="https://kit.fontawesome.com/a2e0e6ad6d.js" crossorigin="anonymous"></script> <!-- untuk icon -->
</head>

<body class="bg-slate-100 min-h-screen p-6">

  <div class="bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold text-center text-slate-800 mb-6">
      🧾 List Machine Manual Book
    </h1>

    <div class="overflow-x-auto">
      <table id="machineTable" class="w-full text-sm border border-slate-200">
        <thead class="bg-slate-100">
          <tr class="text-left text-slate-600 uppercase text-xs">
            <th class="px-4 py-3 border text-center">#</th>
            <th class="px-4 py-3 border">Machine Name</th>
            <th class="px-4 py-3 border">Section</th>
            <th class="px-4 py-3 border">Department</th>
            <th class="px-4 py-3 border text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-slate-50 transition">
              <td class="px-4 py-2 border text-center"><?= $no++ ?></td>
              <td class="px-4 py-2 border font-semibold text-red-600"><?= htmlspecialchars($row['machine_name'] ?? '-') ?></td>
              <td class="px-4 py-2 border"><?= htmlspecialchars($row['section'] ?? '-') ?></td>
              <td class="px-4 py-2 border"><?= htmlspecialchars($row['department'] ?? '-') ?></td>
              <td class="px-4 py-2 border text-center">
                <a href="index.php?page=detail_machine&id=<?= $row['id'] ?>"
                   class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                   <i class="fa-solid fa-circle-info text-lg"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $('#machineTable').DataTable({
        pageLength: 10,
        responsive: true,
        language: {
          search: "Cari:",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
          paginate: { previous: "‹", next: "›" },
          zeroRecords: "Tidak ada data ditemukan."
        }
      });
    });
  </script>

</body>
</html>
