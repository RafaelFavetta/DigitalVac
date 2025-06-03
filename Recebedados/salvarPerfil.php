<?php
header('Content-Type: application/json');
session_start();
require_once '../outros/db_connect.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit();
}

$userId = $_SESSION['id_usuario'];

// Recebe os dados do formulário
$telefone = isset($_POST['telefone']) ? preg_replace('/\D/', '', $_POST['telefone']) : '';
$genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validação dos campos obrigatórios
if (empty($telefone) || empty($genero) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit();
}

// Validação do formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'E-mail inválido.']);
    exit();
}

// Validação do telefone (apenas números, 10 ou 11 dígitos)
if (!preg_match('/^\d{10,11}$/', $telefone)) {
    echo json_encode(['success' => false, 'message' => 'Telefone inválido.']);
    exit();
}

// Atualiza os dados do usuário no banco de dados
$sql = "UPDATE usuario 
        SET tel_usuario = ?, genero_usuario = ?, email_usuario = ?
        WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssi", $telefone, $genero, $email, $userId);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
    exit();
} else {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados: ' . $stmt->error]);
    exit();
}
?>