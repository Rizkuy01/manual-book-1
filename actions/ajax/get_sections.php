<?php
include '../../config.php';
header('Content-Type: application/json');

$dept_id = $_POST['dept_id'] ?? null;
if (!$dept_id) {
    echo json_encode([]);
    exit;
}

$query = $connMB->prepare("SELECT id, name AS name FROM section WHERE dept_id = ?");
$query->bind_param("i", $dept_id);
$query->execute();
$result = $query->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

echo json_encode($sections);
?>
