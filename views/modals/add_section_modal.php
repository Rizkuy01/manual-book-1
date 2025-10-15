<?php
require_once __DIR__ . '/../../config.php';
$departments = $connMB->query("SELECT id, dept_name FROM department ORDER BY dept_name");
?>
<div id="sectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="closeModal('sectionModal')" class="absolute top-2 right-2 text-gray-500">✕</button>
        <h3 class="text-lg font-bold mb-4">Add Section</h3>

        <form method="POST" action="actions/save_section.php">

            <div class="mb-4">
                <label class="block text-sm font-medium">Departemen</label>
                <select name="dept_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Departemen --</option>
                    <?php while ($d = $departments->fetch_assoc()): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Section</label>
                <input type="text" name="section_name" required class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('sectionModal')" class="bg-slate-100 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
