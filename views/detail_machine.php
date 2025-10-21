<?php
include './config.php';

$id = $_GET['id'] ?? 0;
if (!$id) die('Invalid ID');

$stmt = $connMB->prepare("
    SELECT 
        cm.id,
        cm.machine_name,
        cm.code_machine,
        cm.fixedasset,
        cm.maker,
        cm.user,
        cm.created_at,
        d.dept_name AS department,
        s.name AS section,
        ss.name AS subsection,
        b.nama_file,
        b.file_path,
        b.uploaded_at
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
    LEFT JOIN section s ON cm.section_id = s.id
    LEFT JOIN subsection ss ON cm.subsection_id = ss.id
    LEFT JOIN book_file b ON b.machine_id = cm.id
    WHERE cm.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) die('Data mesin tidak ditemukan.');

$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Machine Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background-color: #f1f5f9; font-family: "Inter", sans-serif; color: #1e293b; }
    .page-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #b91c1c; margin-bottom: 20px; }
    .container { display: flex; gap: 24px; flex-wrap: wrap; justify-content: space-between; }
    .card {
      background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 24px;
    }
    .card.info { flex: 1 1 40%; min-width: 360px; }
    .card.viewer { flex: 1 1 55%; min-width: 400px; }
    .card h2 { font-size: 1.25rem; color: #334155; font-weight: 600; margin-bottom: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
    .info-table td { padding: 6px 4px; font-size: 0.95rem; vertical-align: top; }
    .info-table td:first-child { font-weight: 600; width: 160px; color: #475569; }
    .btn-group { display: flex; gap: 8px; margin-top: 16px; }
    .btn {
      padding: 8px 14px; border-radius: 6px; color: #fff; font-weight: 500;
      text-decoration: none; font-size: 0.9rem; transition: 0.2s; border: none; cursor: pointer;
    }
    .btn.back { background: #dc2626; }
    .btn.back:hover { background: #b91c1c; }
    .btn.edit { background: #f59e0b; }
    .btn.edit:hover { background: #d97706; }
    iframe { width: 100%; min-height: 600px; border-radius: 8px; border: 1px solid #cbd5e1; }
    .pdf-placeholder {
      text-align: center; color: #64748b; font-style: italic;
      padding: 80px 20px; border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;
    }
    .upload-btn {
      background: #dc2626; color: #fff; padding: 8px 14px;
      border-radius: 6px; text-decoration: none; transition: 0.2s;
    }
    .upload-btn:hover { background: #b91c1c; }
    @media (max-width: 900px) { .container { flex-direction: column; } }

    /* Modal styling */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.4);
      display: none; justify-content: center; align-items: center; z-index: 9999;
    }
    .modal-box.machine {
      background: #fff; border-radius: 10px; padding: 24px;
      width: 90%; max-width: 850px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }
    .modal-box.machine .form-row {
      display: grid; grid-template-columns: 1fr 1fr; gap: 16px 32px;
    }
    .modal-box.machine input {
      width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px;
    }
    .modal-box.machine .form-actions {
      display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;
    }
    .btn-cancel {
      background: #ccc; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer;
    }
    .btn-save {
      background: #f59e0b; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;
    }
  </style>
</head>

<body>
  <h1 class="page-title">📘 Detail Machine Manual Book</h1>

  <div class="container">
    <!-- LEFT: Machine Info -->
    <div class="card info">
      <h2>Machine Information</h2>
      <table class="info-table">
        <tr><td>Machine Name</td><td>: <?= htmlspecialchars($data['machine_name'] ?? '-') ?></td></tr>
        <tr><td>Code Machine</td><td>: <?= htmlspecialchars($data['code_machine'] ?? '-') ?></td></tr>
        <tr><td>Fixed Asset</td><td>: <?= htmlspecialchars($data['fixedasset'] ?? '-') ?></td></tr>
        <tr><td>Maker</td><td>: <?= htmlspecialchars($data['maker'] ?? '-') ?></td></tr>
        <tr><td>User</td><td>: <?= htmlspecialchars($data['user'] ?? '-') ?></td></tr>
        <tr><td>Department</td><td>: <?= htmlspecialchars($data['department'] ?? '-') ?></td></tr>
        <tr><td>Section</td><td>: <?= htmlspecialchars($data['section'] ?? '-') ?></td></tr>
        <tr><td>Subsection</td><td>: <?= htmlspecialchars($data['subsection'] ?? '-') ?></td></tr>
        <tr><td>Created At</td><td>: <?= !empty($data['created_at']) ? date('d M Y H:i', strtotime($data['created_at'])) : '-' ?></td></tr>
      </table>

      <div class="btn-group">
        <a href="index.php?page=list_machine" class="btn back">← Kembali</a>
        <button class="btn edit" onclick="openEditModal()">✏️ Edit</button>
      </div>
    </div>

    <!-- RIGHT: Viewer -->
    <div class="card viewer">
      <h2>Manual Book Viewer</h2>
      <?php if (!empty($data['file_path']) && file_exists($data['file_path'])): ?>
        <iframe src="../manual-book-files/<?= rawurlencode(basename($data['file_path'])) ?>"></iframe>
      <?php else: ?>
        <div class="pdf-placeholder">
          Belum memiliki file manual book.<br><br>
          <a href="index.php?page=input_manual_book&machine_id=<?= $data['id'] ?>" class="upload-btn">+ Upload Manual Book</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 🟡 MODAL EDIT MACHINE -->
  <div id="editModal" class="modal-overlay">
    <div class="modal-box machine">
      <button onclick="closeEditModal()" class="close-btn">✕</button>
      <h3 class="text-lg font-bold mb-4">✏️ Edit Machine</h3>

      <form method="POST" action="actions/update_machine.php">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <div class="form-row">
          <div><label>Departemen</label>
            <input type="text" readonly value="<?= htmlspecialchars($data['department'] ?? '-') ?>">
          </div>
          <div><label>Code Machine</label>
            <input type="text" name="code_machine" value="<?= htmlspecialchars($data['code_machine'] ?? '') ?>">
          </div>
          <div><label>Section</label>
            <input type="text" readonly value="<?= htmlspecialchars($data['section'] ?? '-') ?>">
          </div>
          <div><label>ID Asset</label>
            <input type="number" name="fixedasset" value="<?= htmlspecialchars($data['fixedasset'] ?? '') ?>">
          </div>
          <div><label>Subsection</label>
            <input type="text" readonly value="<?= htmlspecialchars($data['subsection'] ?? '-') ?>">
          </div>
          <div><label>Maker</label>
            <input type="text" name="maker" value="<?= htmlspecialchars($data['maker'] ?? '') ?>">
          </div>
          <div><label>Nama Mesin</label>
            <input type="text" name="machine_name" value="<?= htmlspecialchars($data['machine_name'] ?? '') ?>">
          </div>
          <div><label>User</label>
            <input type="text" name="user" value="<?= htmlspecialchars($data['user'] ?? '') ?>">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" onclick="closeEditModal()" class="btn-cancel">Batal</button>
          <button type="submit" class="btn-save">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('editModal');
    function openEditModal() { modal.style.display = 'flex'; }
    function closeEditModal() { modal.style.display = 'none'; }

    <?php if ($success): ?>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data mesin berhasil diperbarui.',
        confirmButtonColor: '#dc2626'
      });
    <?php endif; ?>
  </script>
</body>
</html>
