<?php
if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include './config.php';

// Cek jika ada parameter machine_id
$machine_id = $_GET['machine_id'] ?? null;
$prefill = null;

if ($machine_id) {
    $stmt = $connMB->prepare("
        SELECT 
            cm.id AS machine_id,
            cm.machine_name,
            cm.dept_id,
            cm.section_id,
            cm.subsection_id,
            d.dept_name,
            s.name AS section_name,
            ss.name AS subsection_name
        FROM contoh_mesin cm
        LEFT JOIN department d ON d.id = cm.dept_id
        LEFT JOIN section s ON s.id = cm.section_id
        LEFT JOIN subsection ss ON ss.id = cm.subsection_id
        WHERE cm.id = ?
    ");
    $stmt->bind_param("i", $machine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prefill = $result->fetch_assoc();
    $stmt->close();
}

$departments = $connMB->query("SELECT id, dept_name FROM department WHERE dept_name NOT IN ('MIS', 'QA') ORDER BY dept_name");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Upload Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .center-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      background-color: #f1f5f9; 
    }
  </style>
</head>

<body class="bg-slate-100">

  <div class="center-wrapper">
    <div class="bg-white shadow-md rounded-xl w-full max-w-lg px-8 py-5">
      <h2 class="text-2xl font-bold mb-6 text-center text-red-700">Upload Manual Book (PDF)</h2>

      <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
        <!-- Dropdown Department -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
          <?php if ($prefill): ?>
            <input type="hidden" name="dept_id" value="<?= $prefill['dept_id'] ?>">
            <input type="text" value="<?= htmlspecialchars($prefill['dept_name']) ?>" 
                  class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-100" readonly>
          <?php else: ?>
            <select id="dept" name="dept_id" required
                    class="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
              <option value="">-- Pilih Departemen --</option>
              <?php while ($row = $departments->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['dept_name']) ?></option>
              <?php endwhile; ?>
            </select>
          <?php endif; ?>
        </div>

        <!-- Dropdown Section -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Section</label>
          <?php if ($prefill): ?>
            <input type="hidden" name="section_id" value="<?= $prefill['section_id'] ?>">
            <input type="text" value="<?= htmlspecialchars($prefill['section_name']) ?>" 
                  class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-100" readonly>
          <?php else: ?>
            <select id="section" name="section_id" required
                    class="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
              <option value="">-- Pilih Section --</option>
            </select>
          <?php endif; ?>
        </div>

        <!-- Dropdown Subsection -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Subsection</label>
          <?php if ($prefill): ?>
            <input type="hidden" name="subsection_id" value="<?= $prefill['subsection_id'] ?>">
            <input type="text" value="<?= htmlspecialchars($prefill['subsection_name']) ?>" 
                  class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-100" readonly>
          <?php else: ?>
            <select id="subsection" name="subsection_id" required
                    class="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
              <option value="">-- Pilih Subsection --</option>
            </select>
          <?php endif; ?>
        </div>

        <!-- Dropdown Machine -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Machine</label>
          <?php if ($prefill): ?>
            <input type="hidden" name="machine_id" value="<?= $prefill['machine_id'] ?>">
            <input type="text" value="<?= htmlspecialchars($prefill['machine_name']) ?>" 
                  class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-100" readonly>
          <?php else: ?>
            <select id="machine" name="machine_id" required
                    class="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
              <option value="">-- Pilih Machine --</option>
            </select>
          <?php endif; ?>
        </div>

        <!-- Input Nama File -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nama File</label>
          <input type="text" name="nama_file" required
                 class="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400" />
        </div>

        <!-- Upload PDF -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Pilih File (PDF)</label>
          <input type="file" name="pdf_file" accept=".pdf" required
                 class="w-full border border-slate-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-red-400" />
        </div>

        <button type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-md">
          Upload
        </button>
      </form>
    </div>
  </div>

<script>
$(document).ready(function() {

  // === Dropdown 1: Departemen ===
  $('#dept').change(function() {
    const deptId = $(this).val();
    $('#section').html('<option value="">-- Pilih Section --</option>');
    $('#subsection').html('<option value="">-- Pilih Subsection --</option>');
    $('#machine').html('<option value="">-- Pilih Machine --</option>');

    if (deptId) {
      $.post('actions/ajax/get_sections.php', { dept_id: deptId }, function(data) {
        $.each(data, function(i, item) {
          $('#section').append(`<option value="${item.id}">${item.name}</option>`);
        });
      }, 'json');
    }
  });

  // === Dropdown 2: Section ===
  $('#section').change(function() {
    const sectionId = $(this).val();
    $('#subsection').html('<option value="">-- Pilih Subsection --</option>');
    $('#machine').html('<option value="">-- Pilih Machine --</option>');

    if (sectionId) {
      $.post('actions/ajax/get_subsection.php', { section_id: sectionId }, function(data) {
        $.each(data, function(i, item) {
          $('#subsection').append(`<option value="${item.id}">${item.name}</option>`);
        });
      }, 'json');
    }
  });

  // === Dropdown 3: Subsection ===
  $('#subsection').change(function() {
    const subsectionId = $(this).val();
    $('#machine').html('<option value="">-- Pilih Machine --</option>');

    if (subsectionId) {
      $.post('actions/ajax/get_machine.php', { subsection_id: subsectionId }, function(data) {
        $.each(data, function(i, item) {
          $('#machine').append(`<option value="${item.id}">${item.machine_name}</option>`);
        });
      }, 'json');
    }
  });

  // === Upload Manual Book via AJAX ===
  $('#uploadForm').submit(function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
      url: 'actions/upload_manual_book.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function() {
        Swal.fire({
          title: 'Sedang mengupload...',
          text: 'Mohon tunggu sebentar.',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      },
      success: function(response) {
        try {
          const res = JSON.parse(response);
          if (res.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              html: res.message,
              confirmButtonColor: '#dc2626'
            }).then(() => {
              window.location.href = 'index.php?page=list_machine';
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal!',
              text: res.message,
              confirmButtonColor: '#dc2626'
            });
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan Sistem!',
            text: 'Response tidak valid dari server.'
          });
          console.error('Response error:', response);
        }
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Terjadi Error!',
          text: 'Tidak dapat menghubungi server.'
        });
        console.error('AJAX Error:', error);
      }
    });
  });

});
</script>


</body>
</html>
