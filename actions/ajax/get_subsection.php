<?php
include '../../config.php';
header('Content-Type: application/json');

$section_id = $_POST['section_id'] ?? null;
if (!$section_id) {
    echo json_encode([]);
    exit;
}

$query = $connMB->prepare("SELECT id, name AS name FROM subsection WHERE section_id = ?");
$query->bind_param("i", $section_id);
$query->execute();
$result = $query->get_result();

$subsections = [];
while ($row = $result->fetch_assoc()) {
    $subsections[] = $row;
}

echo json_encode($subsections);
?>
