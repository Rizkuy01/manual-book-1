<?php
if (!isset($_SESSION['pending_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<div class="flex justify-center items-center min-h-[calc(100vh-4rem)]"> 
  <div class="bg-white shadow-md rounded-xl w-full max-w-lg p-8">
    <h2 class="text-2xl font-bold mb-6 text-center text-red-700">
      Upload Manual Book (PDF)
    </h2>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded mb-4">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="actions/upload_manual_book.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama File</label>
        <input type="text" name="nama_file" required
               class="w-full border border-slate-300 rounded-md px-3 py-2 
                      focus:outline-none focus:ring-2 focus:ring-red-400" />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih File (PDF)</label>
        <input type="file" name="pdf_file" accept=".pdf" required
               class="w-full border border-slate-300 rounded-md px-3 py-2 bg-white 
                      focus:outline-none focus:ring-2 focus:ring-red-400" />
      </div>

      <button type="submit" 
              class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-md">
        Upload
      </button>
    </form>
  </div>
</div>
