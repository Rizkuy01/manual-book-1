<?php
include '../../config.php';

$dept = $_GET['dept'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = [];
if ($dept) $where[] = "cm.dept_id = " . intval($dept);
if ($search) $where[] = "cm.machine_name LIKE '%" . $connMB->real_escape_string($search) . "%'";

$sql = "
    SELECT 
        cm.id,
        cm.machine_name,
        d.dept_name AS department,
        s.name AS section,
        (SELECT COUNT(*) FROM book_file bf WHERE bf.machine_id = cm.id) AS has_manual
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
    LEFT JOIN section s ON cm.section_id = s.id
";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY d.dept_name, s.name, cm.machine_name LIMIT 50";

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
      $no = 1;
      while ($row = $result->fetch_assoc()):
        $icon = $row['has_manual'] > 0 
          ? '<i class="fa-solid fa-check text-green-600"></i>' 
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
