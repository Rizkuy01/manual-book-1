<?php
include '../../config.php';

$deptFilter = $_GET['dept'] ?? '';
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Hitung total data
$countSql = "
    SELECT COUNT(*) AS total 
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
";
if ($deptFilter) $countSql .= " WHERE cm.dept_id = " . intval($deptFilter);
$totalResult = $connMB->query($countSql)->fetch_assoc();
$totalData = $totalResult['total'] ?? 0;
$totalPages = ceil($totalData / $limit);

// Ambil data sesuai halaman
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
      <td class="action-icon"><?= $icon ?></td>
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
  <?php if ($page > 1): ?>
    <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= $page-1 ?>">«</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?page=list_machine&dept=<?= $deptFilter ?>&p=<?= $page+1 ?>">»</a>
  <?php endif; ?>
</div>
<?php endif; ?>
