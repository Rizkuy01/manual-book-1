<?php
include '../../config.php';

$deptFilter = $_GET['dept'] ?? '';
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/* =====================
   HITUNG TOTAL DATA
===================== */
$countSql = "SELECT COUNT(*) AS total FROM machine m";
if ($deptFilter) {
    $countSql .= " WHERE m.prod = '" . $connMB->real_escape_string($deptFilter) . "'";
}
$totalResult = $connMB->query($countSql)->fetch_assoc();
$totalData = $totalResult['total'] ?? 0;
$totalPages = ceil($totalData / $limit);

/* =====================
   QUERY UTAMA LIST
===================== */
$sql = "
    SELECT 
        m.id,
        m.machine AS machine_name,
        m.subline AS section,
        m.prod AS department,
        (SELECT COUNT(*) FROM book_file bf WHERE bf.machine_id = m.id) AS has_manual
    FROM machine m
";

if ($deptFilter) {
    $sql .= " WHERE m.prod = '" . $connMB->real_escape_string($deptFilter) . "'";
}

$sql .= " ORDER BY m.prod, m.subline, m.machine
          LIMIT $limit OFFSET $offset";

$result = $connMB->query($sql);
?>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Machine Name</th>
      <th>Section</th>
      <th>Department</th>
      <th>Manual Book</th>
      <th>Detail</th>
    </tr>
  </thead>

  <tbody>
    <?php 
    if ($result->num_rows > 0):
        $no = $offset + 1;
        while ($row = $result->fetch_assoc()):
            $icon = $row['has_manual'] > 0 
              ? '<i class="fa-solid fa-check text-green-700"></i>' 
              : '<i class="fa-solid fa-xmark text-red-600"></i>';
    ?>
      <tr>
        <td><?= $no++ ?></td>
        <td class="machine-name font-semibold"><?= htmlspecialchars($row['machine_name']) ?></td>
        <td><?= htmlspecialchars($row['section']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td><?= $icon ?></td>
        <td>
          <a href="index.php?page=detail_machine&id=<?= $row['id'] ?>" class="action-btn">
            <i class="fa-solid fa-eye"></i>
          </a>
        </td>
      </tr>

    <?php endwhile; else: ?>
      <tr><td colspan="6" class="text-center py-4 text-slate-500 italic">Tidak ada data ditemukan.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="pagination">

  <!-- Prev -->
  <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= max(1, $page - 1) ?>"
     class="<?= $page == 1 ? 'disabled' : '' ?>">« Prev</a>

  <?php
  // Jumlah halaman yang ditampilkan
  $show = 5;

  // Hitung range halaman
  $start = max(1, $page - floor($show / 2));
  $end = min($totalPages, $start + $show - 1);

  // Jika end mentok, geser start
  if ($end - $start < $show - 1) {
      $start = max(1, $end - $show + 1);
  }

  for ($i = $start; $i <= $end; $i++):
  ?>
      <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= $i ?>"
         class="<?= $i == $page ? 'active' : '' ?>">
         <?= $i ?>
      </a>
  <?php endfor; ?>

  <!-- Next -->
  <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= min($totalPages, $page + 1) ?>"
     class="<?= $page == $totalPages ? 'disabled' : '' ?>">Next »</a>

</div>
<?php endif; ?>


<style>
  .pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 6px;
}

.pagination a {
  padding: 6px 14px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
  background: #fff;
  color: #1f2937;
  text-decoration: none;
  font-size: 0.9rem;
  transition: 0.2s;
}

.pagination a:hover:not(.active):not(.disabled) {
  background: #f3f4f6;
}

.pagination a.active {
  background: #dc2626;
  color: #fff;
  border-color: #dc2626;
}

.pagination a.disabled {
  opacity: 0.4;
  pointer-events: none;
}

</style>
