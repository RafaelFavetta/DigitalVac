<?php
session_start();
require_once("../outros/db_connect.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['grupo' => null]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT grupo FROM grupo_especial WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    echo json_encode(['grupo' => $row['grupo']]);
} else {
    echo json_encode(['grupo' => null]);
}
$stmt->close();
$conn->close();
?>
