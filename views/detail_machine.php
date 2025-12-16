<?php
include './config.php';

$id = $_GET['id'] ?? 0;
if (!$id) die('Invalid ID');

$stmt = $connMB->prepare("
    SELECT 
        m.id,
        m.machine AS machine_name,
        m.linecode AS code_machine,
        m.fixedasset,
        m.maker,
        m.mcno AS user,
        m.category,
        m.prod,
        m.subline,
        m.linename,
        m.lineno,
        m.fixedassetnew,
        b.nama_file,
        b.file_path,
        b.uploaded_at
    FROM machine m
    LEFT JOIN book_file b ON b.machine_id = m.id
    WHERE m.id = ?
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
    .btn.upload {
        background: #2563eb;
      }
      .btn.upload:hover {
        background: #1d4ed8;
      }
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

    <!-- Machine Info -->
    <div class="card info">
      <h2>Machine Information</h2>
      <table class="info-table">
          <tr><td>Machine Name</td><td>: <?= htmlspecialchars($data['machine_name'] ?? '') ?></td></tr>
          <tr><td>Line Code</td><td>: <?= htmlspecialchars($data['code_machine'] ?? '') ?></td></tr>
          <tr><td>Fixed Asset</td><td>: <?= htmlspecialchars($data['fixedasset'] ?? '') ?></td></tr>
          <tr><td>Fixed Asset New</td><td>: <?= htmlspecialchars($data['fixedassetnew'] ?? '') ?></td></tr>
          <tr><td>Maker</td><td>: <?= htmlspecialchars($data['maker'] ?? '') ?></td></tr>
          <tr><td>MC No</td><td>: <?= htmlspecialchars($data['user'] ?? '') ?></td></tr>
          <tr><td>Category</td><td>: <?= htmlspecialchars($data['category'] ?? '') ?></td></tr>
          <tr><td>Prod</td><td>: <?= htmlspecialchars($data['prod'] ?? '') ?></td></tr>
          <tr><td>Subline</td><td>: <?= htmlspecialchars($data['subline'] ?? '') ?></td></tr>
          <tr><td>Line Name</td><td>: <?= htmlspecialchars($data['linename'] ?? '') ?></td></tr>
          <tr><td>Line No</td><td>: <?= htmlspecialchars($data['lineno'] ?? '') ?></td></tr>
      </table>

      <div class="btn-group">
        <a href="index.php?page=list_machine" class="btn back">← Kembali</a>
        <button class="btn edit" onclick="openEditModal()">Edit</button>
        <button class="btn upload" onclick="openUploadModal()">Update File</button>
      </div>
    </div>

    <!-- Viewer -->
    <div class="card viewer">
      <h2>Manual Book Viewer</h2>
      <?php
$filePath = $data['file_path'] ?? '';
$absolutePath = 'C:/laragon/www/' . str_replace('/', '\\', $filePath);
?>

<?php if (!empty($filePath) && file_exists($absolutePath)): ?>
  <iframe src="/<?= htmlspecialchars($filePath) ?>" width="100%" height="600px"></iframe>
<?php else: ?>
  <div class="pdf-placeholder">
    Belum memiliki file manual book.<br><br>
    <a href="#" class="upload-btn" onclick="openUploadModal()">+ Upload Manual Book</a>
  </div>
<?php endif; ?>

    </div>
  </div>

  <!-- MODAL EDIT MACHINE -->
  <div id="editModal" class="modal-overlay">
    <div class="modal-box machine">
      <button onclick="closeEditModal()" class="close-btn">✕</button>
      <h3 class="text-lg font-bold mb-4">✏️ Edit Machine</h3>

      <form method="POST" action="actions/update_machine.php">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <div class="form-row">
          <div>
              <label>Machine Name</label>
              <input type="text" name="machine_name" value="<?= htmlspecialchars($data['machine_name']) ?>">
          </div>
          <div>
              <label>Line Code</label>
              <input type="text" name="code_machine" value="<?= htmlspecialchars($data['code_machine']) ?>">
          </div>
          <div>
              <label>Category</label>
              <input type="text" name="category" value="<?= htmlspecialchars($data['category'] ?? '') ?>">
          </div>
          <div>
              <label>Prod</label>
              <input type="text" name="prod" value="<?= htmlspecialchars($data['prod']) ?>">
          </div>
          <div>
              <label>Subline</label>
              <input type="text" name="subline" value="<?= htmlspecialchars($data['subline']) ?>">
          </div>
          <div>
              <label>Line Name</label>
              <input type="text" name="linename" value="<?= htmlspecialchars($data['linename']) ?>">
          </div>
          <div>
              <label>Line No</label>
              <input type="number" name="lineno" value="<?= htmlspecialchars($data['lineno']) ?>">
          </div>
          <div>
              <label>Maker</label>
              <input type="text" name="maker" value="<?= htmlspecialchars($data['maker']) ?>">
          </div>
          <div>
              <label>MC No</label>
              <input type="text" name="user" value="<?= htmlspecialchars($data['user']) ?>">
          </div>
          <div>
              <label>Fixed Asset</label>
              <input type="text" name="fixedasset" value="<?= htmlspecialchars($data['fixedasset']) ?>">
          </div>
          <div>
              <label>Fixed Asset (New)</label>
              <input type="text" name="fixedassetnew" value="<?= htmlspecialchars($data['fixedassetnew']) ?>">
          </div>
      </div>

        <div class="form-actions">
          <button type="button" onclick="closeEditModal()" class="btn-cancel">Batal</button>
          <button type="submit" class="btn-save">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL UPDATE FILE -->
  <div id="uploadModal" class="modal-overlay">
    <div class="modal-box machine">
      <button onclick="closeUploadModal()" class="close-btn">✕</button>
      <h3 class="text-lg font-bold mb-4">📁 Update Manual Book</h3>

      <form method="POST" action="actions/update_file.php" enctype="multipart/form-data">
        <input type="hidden" name="machine_id" value="<?= $data['id'] ?>">
        <input type="hidden" name="dept_id" value="<?= htmlspecialchars($data['dept_id'] ?? '') ?>">
        <input type="hidden" name="section_id" value="<?= htmlspecialchars($data['section_id'] ?? '') ?>">
        <input type="hidden" name="subsection_id" value="<?= htmlspecialchars($data['subsection_id'] ?? '') ?>">

        <div class="form-row">
          <div>
            <label>Nama File Manual Book</label>
            <input type="text" name="nama_file" placeholder="Masukkan nama file manual..." required>
          </div>
          <div>
            <label>File (PDF)</label>
            <input type="file" name="pdf_file" accept=".pdf" required>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" onclick="closeUploadModal()" class="btn-cancel">Batal</button>
          <button type="submit" class="btn-save">Simpan File</button>
        </div>
      </form>
    </div>
  </div>


  <script>
    const modal = document.getElementById('editModal');
    function openEditModal() { modal.style.display = 'flex'; }
    function closeEditModal() { modal.style.display = 'none'; }

    const uploadModal = document.getElementById('uploadModal');
    function openUploadModal() { uploadModal.style.display = 'flex'; }
    function closeUploadModal() { uploadModal.style.display = 'none'; }

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
