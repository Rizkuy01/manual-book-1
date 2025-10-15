<?php
include './config.php';

if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<div class="bg-white rounded-lg shadow p-6">
  <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
      ⚙️ System Management
  </h1>

  <div class="space-y-4">
      <!-- Add Section -->
      <div onclick="openModal('sectionModal')" 
          class="cursor-pointer bg-white border border-red-200 rounded-lg shadow hover:shadow-lg 
                 flex flex-col items-center justify-center p-6 transition transform hover:scale-105 hover:bg-gray-100">
              <i class="fa-solid fa-diagram-project text-2xl"></i>
          <h2 class="text-lg font-semibold text-gray-800 mt-3">Add Section</h2>
      </div>

      <!-- Add Subsection -->
      <div onclick="openModal('subsectionModal')" 
          class="cursor-pointer bg-white border border-red-200 rounded-lg shadow hover:shadow-lg 
                 flex flex-col items-center justify-center p-6 transition transform hover:scale-105 hover:bg-gray-100">
              <i class="fa-solid fa-gears text-2xl"></i>
          <h2 class="text-lg font-semibold text-gray-800 mt-3">Add Subsection</h2>
      </div>

      <!-- Add Machine -->
      <div onclick="openModal('machineModal')" 
          class="cursor-pointer bg-white border border-red-200 rounded-lg shadow hover:shadow-lg 
                 flex flex-col items-center justify-center p-6 transition transform hover:scale-105 hover:bg-gray-100">
              <i class="fa-solid fa-gear text-2xl"></i>
          <h2 class="text-lg font-semibold text-gray-800 mt-3">Add Machine</h2>
      </div>
  </div>
</div>

<?php 
include __DIR__ . '/modals/add_section_modal.php';
include __DIR__ . '/modals/add_subsection_modal.php';
include __DIR__ . '/modals/add_machine_modal.php';
?>

<script>
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.style.display = 'flex'; // tampilkan modal
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.style.display = 'none'; // sembunyikan modal
  }
}
</script>

