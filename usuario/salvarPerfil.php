<?php
header('Content-Type: application/json');
include("../outros/db_connect.php");
include("../Recebedados/validacoes.php");
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';

if (empty($email) || empty($telefone) || empty($genero)) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit();
}
if (!validarTelefone($telefone)) {
    echo json_encode(['success' => false, 'message' => 'Telefone inválido.']);
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'E-mail inválido.']);
    exit();
}

// Atualize os dados no banco de dados
$userId = $_SESSION['id_usuario'];
$stmt = $conn->prepare("UPDATE usuario SET tel_usuario = ?, genero_usuario = ?, email_usuario = ? WHERE id_usuario = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $conn->error]);
    exit();
}
$stmt->bind_param("sssi", $telefone, $genero, $email, $userId);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
exit();
?>