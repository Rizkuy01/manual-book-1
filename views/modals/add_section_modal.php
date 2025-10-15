<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>
<div id="sectionModal" class="modal-overlay">
  <div class="modal-box">
    <button onclick="closeModal('sectionModal')" class="close-btn">✕</button>
    <h3 class="text-lg font-bold mb-4">Add Section</h3>

    <form method="POST" action="actions/save_section.php">
      <label>Departemen</label>
      <select name="dept_id" required>
        <option value="">-- Pilih Departemen --</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
          <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Nama Section</label>
      <input type="text" name="section_name" required>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <button type="button" onclick="closeModal('sectionModal')" style="background: #ccc;">Batal</button>
        <button type="submit" style="background: #dc2626; color: #fff;">Simpan</button>
      </div>
    </form>
  </div>
</div>
