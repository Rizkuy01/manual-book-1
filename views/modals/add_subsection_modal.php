<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS','QA') ORDER BY dept_name");
?>

<div id="subsectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="closeModal('subsectionModal')" class="absolute top-2 right-2 text-gray-500 text-xl font-bold">✕</button>
        <h3 class="text-lg font-bold mb-4 text-center text-slate-800">Add Subsection</h3>

        <form method="POST" action="actions/save_subsection.php">
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
            <select name="dept_id" id="deptSelect" required class="w-full border rounded-md px-3 py-2">
            <option value="">-- Pilih Departemen --</option>
            <?php while ($d = $departments->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
            <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Section</label>
            <select name="section_id" id="sectionSelect" required class="w-full border rounded-md px-3 py-2">
            <option value="">-- Pilih Section --</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Subsection</label>
            <input type="text" name="subsection_name" required class="w-full border rounded-md px-3 py-2">
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeModal('subsectionModal')" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
        </form>
    </div>
</div>

<script>
$(function(){
  $('#deptSelect').on('change', function(){
    const deptId = $(this).val();
    const $sec = $('#sectionSelect');
    $sec.html('<option>Loading...</option>');

    if (!deptId) {
      $sec.html('<option value="">-- Pilih Section --</option>');
      return;
    }

    // Path ke actions
    $.post('actions/ajax/get_sections.php', { dept_id: deptId }, function(data){
      if (!Array.isArray(data)) {
        $sec.html('<option value="">Gagal memuat data</option>');
        console.error('unexpected response', data);
        return;
      }
      $sec.html('<option value="">-- Pilih Section --</option>');
      data.forEach(function(item){
        $sec.append(`<option value="${item.id}">${item.name}</option>`);
      });
    }, 'json').fail(function(xhr, status, err){
      console.error('AJAX fail', status, err, xhr.responseText);
      $sec.html('<option value="">Gagal memuat data</option>');
    });
  });
});
</script>
