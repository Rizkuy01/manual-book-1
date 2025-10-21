<?php
include './config.php';

// === Ambil filter departemen ===
$deptFilter = $_GET['dept'] ?? '';

// === Pagination ===
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// === Ambil daftar departemen ===
$deptQuery = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");

// === Hitung total data untuk pagination ===
$countSql = "
    SELECT COUNT(*) AS total 
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
";
if ($deptFilter) $countSql .= " WHERE cm.dept_id = " . intval($deptFilter);
$totalResult = $connMB->query($countSql)->fetch_assoc();
$totalData = $totalResult['total'] ?? 0;
$totalPages = ceil($totalData / $limit);

// === Query utama (default view) ===
$sql = "
    SELECT 
        cm.id,
        cm.machine_name,
        d.dept_name AS department,
        s.name AS section,
        (
            SELECT COUNT(*) FROM book_file bf 
            WHERE bf.machine_id = cm.id
        ) AS has_manual
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
    LEFT JOIN section s ON cm.section_id = s.id
";
if ($deptFilter) $sql .= " WHERE cm.dept_id = " . intval($deptFilter);
$sql .= " ORDER BY d.dept_name, s.name, cm.machine_name
           LIMIT $limit OFFSET $offset";
$result = $connMB->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>List Machine</title>
  <link rel="stylesheet" href="../src/output.css">
  <script src="https://kit.fontawesome.com/a2e0e6ad6d.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      background-color: #f1f5f9;
      min-height: 100vh;
      font-family: "Inter", system-ui, sans-serif;
      color: #1e293b;
    }
    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .header-bar h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
    }
    .header-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .filter-btn {
      background: #dc2626;
      color: #fff;
      font-size: 1rem;
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .filter-btn:hover { background: #b91c1c; }

    /* Search */
    .search-bar {
      display: flex;
      align-items: center;
      background: #fff;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 4px 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .search-bar input {
      border: none;
      outline: none;
      font-size: 0.9rem;
      padding: 6px;
      width: 200px;
    }
    .search-bar i {
      color: #6b7280;
    }

    /* Table */
    .table-wrapper {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      padding: 20px;
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px 12px;
      border-bottom: 1px solid #e2e8f0;
      text-align: center;
    }
    th {
      background: #f8fafc;
      color: #475569;
      text-transform: uppercase;
      font-size: 0.85rem;
    }
    td { font-size: 0.95rem; }
    .machine-name { text-align: left; }
    .action-btn {
      color: #2563eb;
      font-size: 1.1rem;
      transition: 0.2s;
    }
    .action-btn:hover { color: #1e40af; }
    .action-icon {
      text-align: center;
      vertical-align: middle;
    }

    /* Pagination */
    .pagination {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 6px;
    }
    .pagination a {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      color: #1f2937;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.2s;
    }
    .pagination a.active {
      background: #dc2626;
      color: #fff;
      border-color: #dc2626;
    }
    .pagination a:hover:not(.active) { background: #f3f4f6; }

    /* Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .modal-box {
      background: #fff;
      border-radius: 10px;
      padding: 24px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
      position: relative;
      animation: fadeIn 0.2s ease-in-out;
    }
    .modal-box h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 16px;
    }
    .modal-box select {
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 8px 10px;
      font-size: 0.95rem;
    }
    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
    .btn-cancel, .btn-apply {
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      cursor: pointer;
      font-weight: 500;
    }
    .btn-cancel { background: #e2e8f0; }
    .btn-apply { background: #f59e0b; color: #fff; }
  </style>
</head>

<body>
  <div class="header-bar">
    <h1>🧾 List Machine Manual Book</h1>
    <div class="header-actions">
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Cari mesin...">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <button class="filter-btn" onclick="openModal()">
        <i class="fa-solid fa-filter"></i>
      </button>
    </div>
  </div>

  <div class="table-wrapper" id="tableContainer">
    <!-- Tabel utama dimuat lewat AJAX -->
    <div style="text-align:center; color:#64748b; padding:40px;">Memuat data...</div>
  </div>

  <!-- Modal Filter -->
  <div id="filterModal" class="modal-overlay">
    <div class="modal-box">
      <h3>Filter by Department</h3>
      <form method="GET" action="index.php">
        <input type="hidden" name="page" value="list_machine">
        <select name="dept">
          <option value="">-- Semua Departemen --</option>
          <?php while ($dept = $deptQuery->fetch_assoc()): ?>
            <option value="<?= $dept['id'] ?>" <?= $deptFilter == $dept['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($dept['dept_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
          <button type="submit" class="btn-apply">Terapkan</button>
        </div>
      </form>
    </div>
  </div>

<script>
  const modal = document.getElementById('filterModal');
  function openModal() { modal.style.display = 'flex'; }
  function closeModal() { modal.style.display = 'none'; }

  // === Load tabel (default: server pagination) ===
  function loadTable(search = '') {
    const dept = "<?= $deptFilter ?>";
    const page = "<?= $page ?>";

    if (search.trim() === '') {
      // Tidak ada pencarian → reload halaman penuh (pakai pagination)
      $('#tableContainer').load('actions/ajax/table_machine_paginated.php?dept=' + dept + '&p=' + page);
    } else {
      // Ada pencarian → AJAX tanpa pagination
      $('#tableContainer').html('<div style="text-align:center; color:#64748b; padding:40px;">Mencari...</div>');
      $.get('actions/ajax/search_machine.php', { dept: dept, search: search }, function(data) {
        $('#tableContainer').html(data);
      });
    }
  }

  $(document).ready(function() {
    // Load default paginated table
    loadTable();

    // Live search
    $('#searchInput').on('keyup', function() {
      const val = $(this).val();
      loadTable(val);
    });
  });
</script>

</body>
</html>
