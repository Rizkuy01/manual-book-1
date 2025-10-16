<?php
include './config.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    die('Invalid ID');
}

$stmt = $connMB->prepare("
    SELECT 
        b.id,
        cm.machine_name,
        d.dept_name AS department,
        s.name AS section,
        ss.name AS subsection,
        b.nama_file,
        b.file_path,
        b.uploaded_at
    FROM book_file b
    LEFT JOIN department d ON b.dept_id = d.id
    LEFT JOIN section s ON b.section_id = s.id
    LEFT JOIN subsection ss ON b.subsection_id = ss.id
    LEFT JOIN contoh_mesin cm ON b.machine_id = cm.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die('Data tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Machine</title>
  <link rel="stylesheet" href="../src/output.css">
</head>

<body class="bg-slate-100 min-h-screen p-6">
  <h1 class="text-2xl font-bold text-center text-red-700 mb-6">📘 Detail Machine Manual Book</h1>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- 🧩 DETAIL -->
    <div class="bg-white shadow-md rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4 text-slate-700">Machine Information</h2>
      <table class="w-full text-sm">
        <tr><td class="py-2 font-semibold text-slate-600">Machine Name</td><td>: <?= htmlspecialchars($data['machine_name']) ?></td></tr>
        <tr><td class="py-2 font-semibold text-slate-600">Department</td><td>: <?= htmlspecialchars($data['department']) ?></td></tr>
        <tr><td class="py-2 font-semibold text-slate-600">Section</td><td>: <?= htmlspecialchars($data['section']) ?></td></tr>
        <tr><td class="py-2 font-semibold text-slate-600">Subsection</td><td>: <?= htmlspecialchars($data['subsection']) ?></td></tr>
        <tr><td class="py-2 font-semibold text-slate-600">Nama File</td><td>: <?= htmlspecialchars($data['nama_file']) ?></td></tr>
        <tr><td class="py-2 font-semibold text-slate-600">Tanggal Upload</td><td>: <?= date('d M Y H:i', strtotime($data['uploaded_at'])) ?></td></tr>
      </table>

      <a href="list_machine.php" 
         class="mt-6 inline-block bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
         ← Kembali
      </a>
    </div>

    <!-- 🧩 PDF VIEWER -->
    <div class="bg-white shadow-md rounded-xl p-4">
      <h2 class="text-xl font-semibold mb-3 text-slate-700">Manual Book Viewer</h2>
      <?php if (!empty($data['file_path']) && file_exists($data['file_path'])): ?>
        <?php
// Ambil hanya nama file dari path (untuk pastikan tidak dobel direktori)
$filename = basename($data['file_path']);
$fileUrl = "../manual-book-files/" . $filename;
?>
<iframe src="<?= htmlspecialchars($fileUrl) ?>" width="100%" height="600" style="border:1px solid #ccc;"></iframe>

      <?php else: ?>
        <div class="text-center text-slate-500 italic py-10">
          File PDF tidak ditemukan.
        </div>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
