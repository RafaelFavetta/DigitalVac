<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT coren_crm FROM medico WHERE coren_crm LIKE CONCAT('%', ?, '%') LIMIT 10");
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row['coren_crm'];
}
echo json_encode($suggestions);
?>
