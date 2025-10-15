<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>
<div id="subsectionModal" class="modal-overlay">
  <div class="modal-box">
    <button onclick="closeModal('subsectionModal')" class="close-btn">✕</button>
    <h3 class="text-lg font-bold mb-4">Add Subsection</h3>

    <form method="POST" action="actions/save_subsection.php">
      <label>Departemen</label>
      <select name="dept_id" id="deptSelect" required>
        <option value="">-- Pilih Departemen --</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
          <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Section</label>
      <select name="section_id" id="sectionSelect" required>
        <option value="">-- Pilih Section --</option>
      </select>

      <label>Nama Subsection</label>
      <input type="text" name="subsection_name" required>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <button type="button" onclick="closeModal('subsectionModal')" style="background: #ccc;">Batal</button>
        <button type="submit" style="background: #2563eb; color: #fff;">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('deptSelect').addEventListener('change', function() {
  const deptId = this.value;
  const sectionSelect = document.getElementById('sectionSelect');
  sectionSelect.innerHTML = '<option>Loading...</option>';
  fetch('actions/ajax/get_sections.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'dept_id=' + deptId
  })
  .then(res => res.json())
  .then(data => {
    sectionSelect.innerHTML = '<option value="">-- Pilih Section --</option>';
    data.forEach(sec => {
      sectionSelect.innerHTML += `<option value="${sec.id}">${sec.name}</option>`;
    });
  })
  .catch(() => sectionSelect.innerHTML = '<option>Gagal memuat data</option>');
});
</script>
