<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>
<div id="machineModal" class="modal-overlay">
  <div class="modal-box">
    <button onclick="closeModal('machineModal')" class="close-btn">✕</button>
    <h3 class="text-lg font-bold mb-4">Add Machine</h3>

    <form method="POST" action="actions/save_machine.php">
      <label>Departemen</label>
      <select name="dept_id" id="deptMachine" required>
        <option value="">-- Pilih Departemen --</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
          <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Section</label>
      <select name="section_id" id="sectionMachine" required>
        <option value="">-- Pilih Section --</option>
      </select>

      <label>Subsection</label>
      <select name="subsection_id" id="subsectionMachine" required>
        <option value="">-- Pilih Subsection --</option>
      </select>

      <label>Nama Mesin</label>
      <input type="text" name="machine_name" required>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <button type="button" onclick="closeModal('machineModal')" style="background: #ccc;">Batal</button>
        <button type="submit" style="background: #f59e0b; color: #fff;">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('deptMachine').addEventListener('change', function() {
  const deptId = this.value;
  const sectionSelect = document.getElementById('sectionMachine');
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

document.getElementById('sectionMachine').addEventListener('change', function() {
  const sectionId = this.value;
  const subsectionSelect = document.getElementById('subsectionMachine');
  subsectionSelect.innerHTML = '<option>Loading...</option>';
  fetch('actions/ajax/get_subsections.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'section_id=' + sectionId
  })
  .then(res => res.json())
  .then(data => {
    subsectionSelect.innerHTML = '<option value="">-- Pilih Subsection --</option>';
    data.forEach(sub => {
      subsectionSelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
    });
  })
  .catch(() => subsectionSelect.innerHTML = '<option>Gagal memuat data</option>');
});
</script>
