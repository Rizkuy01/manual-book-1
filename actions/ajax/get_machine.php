<?php
require_once '../../config.php';
header('Content-Type: application/json');

$subsection_id = $_POST['subsection_id'] ?? null;
if (!$subsection_id) {
    echo json_encode([]);
    exit;
}

$query = $connMB->prepare("
    SELECT id, machine_name 
    FROM contoh_mesin 
    WHERE subsection_id = ?
    ORDER BY machine_name
");
$query->bind_param("i", $subsection_id);
$query->execute();
$result = $query->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
