<?php
header('Content-Type: application/json');
session_start();
require_once("../outros/db_connect.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$grupo = isset($_POST['grupo']) ? trim($_POST['grupo']) : '';

if (!in_array($grupo, ['Imunodeprimido', 'Gestante', 'Nenhum'])) {
    echo json_encode(['success' => false, 'message' => 'Seleção inválida.']);
    exit;
}

// Salva ou atualiza o grupo especial do usuário
$stmt = $conn->prepare("INSERT INTO grupo_especial (id_usuario, grupo) VALUES (?, ?) ON DUPLICATE KEY UPDATE grupo = VALUES(grupo)");
$stmt->bind_param("is", $id_usuario, $grupo);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar grupo especial.']);
}
$stmt->close();
$conn->close();
exit;
?>
