<?php
include './config.php';

$id = $_GET['id'] ?? 0;
if (!$id) die('Invalid ID');
$stmt = $connMB->prepare("
    SELECT 
        cm.id,
        cm.machine_name,
        cm.code_machine,
        cm.fixedasset,
        cm.maker,
        cm.user,
        cm.created_at,
        d.dept_name AS department,
        s.name AS section,
        ss.name AS subsection,
        b.nama_file,
        b.file_path,
        b.uploaded_at
    FROM contoh_mesin cm
    LEFT JOIN department d ON cm.dept_id = d.id
    LEFT JOIN section s ON cm.section_id = s.id
    LEFT JOIN subsection ss ON cm.subsection_id = ss.id
    LEFT JOIN book_file b ON b.machine_id = cm.id
    WHERE cm.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) die('Data mesin tidak ditemukan.');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Machine Manual Book</title>
  <link rel="stylesheet" href="../src/output.css">
  <style>
    body {
      background-color: #f1f5f9;
      min-height: 100vh;
      font-family: "Inter", system-ui, sans-serif;
      color: #1e293b;
    }
    .page-title {
      text-align: center;
      font-size: 1.75rem;
      font-weight: 700;
      color: #b91c1c;
      margin-bottom: 32px;
      letter-spacing: 0.5px;
    }
    .container {
      display: flex;
      gap: 24px;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
      padding: 24px;
      display: flex;
      flex-direction: column;
    }
    .card.info { flex: 1 1 40%; min-width: 360px; }
    .card.viewer { flex: 1 1 55%; min-width: 400px; }
    .card h2 {
      font-size: 1.25rem; color: #334155; font-weight: 600;
      margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;
    }
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td { padding: 8px 4px; vertical-align: top; font-size: 0.95rem; }
    .info-table td:first-child { font-weight: 600; color: #475569; width: 160px; }
    .back-btn {
      display: inline-block; background: #dc2626; color: #fff; padding: 8px 18px;
      font-size: 0.9rem; font-weight: 500; border-radius: 6px; text-decoration: none;
      align-self: flex-start; margin-top: 16px; transition: all 0.25s ease;
    }
    .back-btn:hover { background: #b91c1c; transform: scale(1.02); }
    iframe {
      width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;
      min-height: 600px;
    }
    .pdf-placeholder {
      text-align: center; color: #64748b; font-style: italic;
      padding: 80px 20px; border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;
    }
    @media (max-width: 900px) {
      .container { flex-direction: column; }
      iframe { min-height: 480px; }
    }
  </style>
</head>

<body>
  <h1 class="page-title">📘 Detail Machine Manual Book</h1>

  <div class="container">

    <!-- KIRI: DETAIL MESIN -->
    <div class="card info">
      <h2>Machine Information</h2>
      <table class="info-table">
        <tr><td>Machine Name</td><td>: <?= htmlspecialchars($data['machine_name'] ?? '') ?></td></tr>
        <tr><td>Code Machine</td><td>: <?= htmlspecialchars($data['code_machine'] ?? '') ?></td></tr>
        <tr><td>Fixed Asset</td><td>: <?= htmlspecialchars($data['fixedasset'] ?? '') ?></td></tr>
        <tr><td>Maker</td><td>: <?= htmlspecialchars($data['maker'] ?? '') ?></td></tr>
        <tr><td>User</td><td>: <?= htmlspecialchars($data['user'] ?? '') ?></td></tr>
        <tr><td>Department</td><td>: <?= htmlspecialchars($data['department'] ?? '') ?></td></tr>
        <tr><td>Section</td><td>: <?= htmlspecialchars($data['section'] ?? '') ?></td></tr>
        <tr><td>Subsection</td><td>: <?= htmlspecialchars($data['subsection'] ?? '') ?></td></tr>
        <tr><td>Created At</td><td>:
            <?php
              if (!empty($data['created_at'])) {
                $dt = strtotime($data['created_at']);
                echo $dt ? date('d M Y H:i', $dt) : htmlspecialchars($data['created_at']);
              } else {
                echo '-';
              }
            ?>
          </td>
        </tr>

        <?php if (!empty($data['nama_file'])): ?>
          <tr>
            <td>Nama File</td>
            <td>: <?= htmlspecialchars($data['nama_file'] ?? '') ?></td>
          </tr>
          <tr>
            <td>Tanggal Upload</td>
            <td>:
              <?php
                if (!empty($data['uploaded_at'])) {
                  $dt2 = strtotime($data['uploaded_at']);
                  echo $dt2 ? date('d M Y H:i', $dt2) : htmlspecialchars($data['uploaded_at']);
                } else {
                  echo '-';
                }
              ?>
            </td>
          </tr>
        <?php else: ?>
          <tr>
            <td colspan="2" class="text-slate-500 italic">Belum memiliki manual book.</td>
          </tr>
        <?php endif; ?>
      </table>

      <a href="index.php?page=list_machine" class="back-btn">← Kembali</a>
    </div>

    <!-- KANAN: PDF VIEWER -->
    <div class="card viewer">
      <h2>Manual Book Viewer</h2>
      <?php
        $filePath = $data['file_path'] ?? '';
        $showIframe = false;
        $iframeUrl = '';

        if (!empty($filePath)) {
            if (strpos($filePath, 'http://') === 0 || strpos($filePath, 'https://') === 0) {
                $iframeUrl = $filePath;
                $showIframe = true;
            } else {
                if (file_exists($filePath)) {
                    $filename = basename($filePath);
                    $iframeUrl = "../manual-book-files/" . rawurlencode($filename);
                    $showIframe = true;
                } else {
                    $maybe = $filePath;
                    if (file_exists(__DIR__ . '/../' . ltrim($maybe, '/'))) {
                      $filename = basename($maybe);
                      $iframeUrl = "../manual-book-files/" . rawurlencode($filename);
                      $showIframe = true;
                    } else {
                      $showIframe = false;
                    }
                }
            }
        }

        if ($showIframe && !empty($iframeUrl)): ?>
          <iframe src="<?= htmlspecialchars($iframeUrl) ?>" frameborder="0"></iframe>
      <?php else: ?>
          <div class="pdf-placeholder">Belum memiliki file manual book.</div>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
