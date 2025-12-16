<?php
include '../../config.php';

$dept = $_GET['dept'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = [];

if ($dept) {
    $where[] = "m.prod = '" . $connMB->real_escape_string($dept) . "'";
}

if ($search) {
    $where[] = "m.machine LIKE '%" . $connMB->real_escape_string($search) . "%'";
}

$sql = "
    SELECT 
        m.id,
        m.machine AS machine_name,
        m.subline AS section,
        m.prod AS department,
        (SELECT COUNT(*) FROM book_file bf WHERE bf.machine_id = m.id) AS has_manual
    FROM machine m
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY m.prod, m.subline, m.machine LIMIT 50";

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
      <tr><td colspan="6" class="text-center text-slate-500 py-4">Tidak ada data ditemukan.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
