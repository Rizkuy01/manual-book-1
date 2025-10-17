<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>

<div id="machineModal" class="modal-overlay">
  <div class="modal-box machine"> 
    <button onclick="closeModal('machineModal')" class="close-btn">✕</button>
    <h3 class="text-lg font-bold mb-4">Add Machine</h3>

    <form method="POST" action="actions/save_machine.php">
      <div class="form-row">

      <!-- DEPARTEMENT -->
        <div>
          <label>Departemen</label>
          <select name="dept_id" id="deptMachine" required>
            <option value="">-- Pilih Departemen --</option>
            <?php while ($d = $departments->fetch_assoc()): ?>
              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        
        <!-- CODE MESIN -->
        <div>
          <label>Code Mesin</label>
          <input type="text" name="code_machine" placeholder="Masukkan kode mesin" required>
        </div>

        <!-- SECTION -->
        <div>
          <label>Section</label>
          <select name="section_id" id="sectionMachine" required>
            <option value="">-- Pilih Section --</option>
          </select>
        </div>
        
        <!-- MAKER -->
        <div>
          <label>Maker</label>
          <input type="text" name="maker" placeholder="Masukkan nama maker" required>
        </div>

        <!-- SUBSECTION -->
        <div>
          <label>Subsection</label>
          <select name="subsection_id" id="subsectionMachine" required>
            <option value="">-- Pilih Subsection --</option>
          </select>
        </div>

        <!-- ID ASSET -->
        <div>
          <label>ID Asset</label>
          <input type="number" name="fixedasset" placeholder="Masukkan ID asset" required>
        </div>
        
        <!-- NAMA MESIN -->
        <div>
          <label>Nama Mesin</label>
          <input type="text" name="machine_name" placeholder="Masukkan nama mesin" required>
        </div>

        <!-- USER -->
        <div>
          <label>User</label>
          <input type="text" name="user" value="<?= htmlspecialchars($_SESSION['pending_user']['nama'] ?? '') ?>" readonly>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" onclick="closeModal('machineModal')" class="btn-cancel">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
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
  fetch('actions/ajax/get_subsection.php', {
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

<style>
.modal-box.machine {
  background: #fff;
  border-radius: 10px;
  padding: 24px;
  width: 90%;
  max-width: 850px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  position: relative;
  animation: fadeIn 0.2s ease-in-out;
}

/* Grid */
.modal-box.machine .form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 32px;
}

.modal-box.machine label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  display: block;
  margin-bottom: 4px;
}
.modal-box.machine input,
.modal-box.machine select {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.modal-box.machine .form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.modal-box.machine .btn-cancel {
  background: #ccc;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.modal-box.machine .btn-save {
  background: #f59e0b;
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* Responsif */
@media (max-width: 640px) {
  .modal-box.machine .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
