<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT nome_medico FROM medico WHERE nome_medico LIKE CONCAT('%', ?, '%') LIMIT 10");
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row['nome_medico'];
}
echo json_encode($suggestions);
?>
