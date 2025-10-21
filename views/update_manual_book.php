<?php
include './config.php';

$machine_id = $_GET['machine_id'] ?? 0;
if (!$machine_id) die('Machine ID tidak ditemukan.');

$stmt = $connMB->prepare("
    SELECT cm.id, cm.machine_name, d.dept_name AS department, 
           s.name AS section, ss.name AS subsection, 
           b.nama_file, b.file_path
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
    LEFT JOIN section s ON cm.section_id = s.id
    LEFT JOIN subsection ss ON cm.subsection_id = ss.id
    LEFT JOIN book_file b ON b.machine_id = cm.id
    WHERE cm.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $machine_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) die('Data mesin tidak ditemukan.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Update Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background: #f1f5f9; font-family: "Inter", sans-serif; color: #1e293b; }
    .container {
      max-width: 700px; margin: 60px auto; background: #fff; padding: 32px;
      border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    h1 { font-size: 1.5rem; font-weight: 700; color: #b91c1c; margin-bottom: 20px; text-align: center; }
    label { font-weight: 600; display: block; margin-bottom: 6px; color: #334155; }
    input[type="text"], input[type="file"] {
      width: 100%; border: 1px solid #cbd5e1; border-radius: 6px;
      padding: 8px 10px; margin-bottom: 14px;
    }
    .file-preview {
      background: #f8fafc; border: 1px dashed #cbd5e1; padding: 14px; border-radius: 8px;
      text-align: center; font-size: 0.9rem; color: #64748b; margin-bottom: 14px;
    }
    .btn-group { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
    .btn { padding: 8px 14px; border-radius: 6px; color: #fff; font-weight: 500; border: none; cursor: pointer; }
    .btn.cancel { background: #94a3b8; }
    .btn.cancel:hover { background: #64748b; }
    .btn.submit { background: #2563eb; }
    .btn.submit:hover { background: #1d4ed8; }
  </style>
</head>

<body>
  <div class="container">
    <h1>📁 Update Manual Book</h1>
    <form method="POST" action="actions/update_manual_book.php" enctype="multipart/form-data">
      <input type="hidden" name="machine_id" value="<?= $data['id'] ?>">

      <label>Machine Name</label>
      <input type="text" value="<?= htmlspecialchars($data['machine_name']) ?>" readonly>

      <label>Department</label>
      <input type="text" value="<?= htmlspecialchars($data['department'] ?? '-') ?>" readonly>

      <label>Section</label>
      <input type="text" value="<?= htmlspecialchars($data['section'] ?? '-') ?>" readonly>

      <label>Subsection</label>
      <input type="text" value="<?= htmlspecialchars($data['subsection'] ?? '-') ?>" readonly>

      <?php if (!empty($data['nama_file'])): ?>
        <div class="file-preview">
          File manual book saat ini: <br><strong><?= htmlspecialchars($data['nama_file']) ?></strong>
        </div>
      <?php else: ?>
        <div class="file-preview">Belum ada file manual book sebelumnya.</div>
      <?php endif; ?>

      <label>File Manual Book (PDF)</label>
      <input type="file" name="file" accept=".pdf" required>

      <div class="btn-group">
        <a href="index.php?page=detail_machine&id=<?= $data['id'] ?>" class="btn cancel">Batal</a>
        <button type="submit" class="btn submit">Simpan Perubahan</button>
      </div>
    </form>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'File manual book berhasil diperbarui.',
        confirmButtonColor: '#2563eb'
      });
    </script>
  <?php endif; ?>
</body>
</html>
