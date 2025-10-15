<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>

<div id="machineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeModal('machineModal')" class="absolute top-2 right-2 text-gray-500 text-xl font-bold">✕</button>
    <h3 class="text-lg font-bold mb-4 text-center text-slate-800">Add Machine</h3>

    <form method="POST" action="actions/save_machine.php" id="formAddMachine">
      <!-- Departemen -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
        <select name="dept_id" id="deptMachine" required class="w-full border rounded-md px-3 py-2">
          <option value="">-- Pilih Departemen --</option>
          <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Section -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Section</label>
        <select name="section_id" id="sectionMachine" required class="w-full border rounded-md px-3 py-2">
          <option value="">-- Pilih Section --</option>
        </select>
      </div>

      <!-- Subsection -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Subsection</label>
        <select name="subsection_id" id="subsectionMachine" required class="w-full border rounded-md px-3 py-2">
          <option value="">-- Pilih Subsection --</option>
        </select>
      </div>

      <!-- Nama Mesin -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mesin</label>
        <input type="text" name="machine_name" required class="w-full border rounded-md px-3 py-2">
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModal('machineModal')" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function($){
    const urlGetSections = 'actions/ajax/get_sections.php';
    const urlGetSubsections = 'actions/ajax/get_subsection.php';

    // Load section
    $('#deptMachine').on('change', function(){
      const deptId = $(this).val();
      const $section = $('#sectionMachine');
      const $sub = $('#subsectionMachine');
      $section.html('<option>Loading...</option>');
      $sub.html('<option value="">-- Pilih Subsection --</option>');

      if (!deptId) {
        $section.html('<option value="">-- Pilih Section --</option>');
        return;
      }

      $.post(urlGetSections, { dept_id: deptId }, function(data){
        $section.html('<option value="">-- Pilih Section --</option>');
        if (!Array.isArray(data) || data.length === 0) {
          $section.html('<option value="">Tidak ada section</option>');
          return;
        }
        data.forEach(function(item){
          $section.append(`<option value="${item.id}">${item.name}</option>`);
        });
      }, 'json').fail(function(){ $section.html('<option value="">Gagal memuat data</option>'); });
    });

    // Load subsection
    $('#sectionMachine').on('change', function(){
      const sectionId = $(this).val();
      const $sub = $('#subsectionMachine');
      $sub.html('<option>Loading...</option>');

      if (!sectionId) {
        $sub.html('<option value="">-- Pilih Subsection --</option>');
        return;
      }

      $.post(urlGetSubsections, { section_id: sectionId }, function(data){
        $sub.html('<option value="">-- Pilih Subsection --</option>');
        if (!Array.isArray(data) || data.length === 0) {
          $sub.html('<option value="">Tidak ada subsection</option>');
          return;
        }
        data.forEach(function(item){
          $sub.append(`<option value="${item.id}">${item.name}</option>`);
        });
      }, 'json').fail(function(){ $sub.html('<option value="">Gagal memuat data</option>'); });
    });
  })(jQuery);
</script>
