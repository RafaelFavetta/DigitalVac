<?php
session_start();
require_once '../outros/db_connect.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../usuario/login.php");
    exit();
}

$userId = $_SESSION['id_usuario'];

// Recebe os dados do formulário
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$peso = isset($_POST['peso']) ? trim($_POST['peso']) : '';
$alergias = isset($_POST['alergias']) ? trim($_POST['alergias']) : '';
$doencas = isset($_POST['doencas']) ? trim($_POST['doencas']) : '';
$medicamentos = isset($_POST['medicamentos']) ? trim($_POST['medicamentos']) : '';
$nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : '';

// Recebe o valor da origem
$origem = isset($_POST['origem']) ? trim($_POST['origem']) : 'usuario';

// Validação dos campos obrigatórios
if (empty($telefone) || empty($genero) || empty($email) || empty($peso)) {
    header("Location: editarPerfilU.php?error=preencha_todos_os_campos");
    exit();
}

// Validação do formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: editarPerfilU.php?error=email_invalido");
    exit();
}

// Validação do peso (deve ser um número positivo)
if (!is_numeric($peso) || $peso <= 0) {
    header("Location: editarPerfilU.php?error=peso_invalido");
    exit();
}

// Validação do telefone (apenas números, 10 ou 11 dígitos)
if (!preg_match('/^\d{10,11}$/', $telefone)) {
    header("Location: editarPerfilU.php?error=telefone_invalido");
    exit();
}

// Atualiza os dados do usuário no banco de dados
$sql = "UPDATE usuario 
        SET tel_usuario = ?, genero_usuario = ?, email_usuario = ?, peso_usuario = ?, ale_usuario = ?, doen_usuario = ?, med_usuario = ? 
        WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

// Vincula os parâmetros e executa a consulta
$stmt->bind_param("sssssssi", $telefone, $genero, $email, $peso, $alergias, $doencas, $medicamentos, $userId);

if ($stmt->execute()) {
    if (!empty($nova_senha)) {
        $nova_senha_hashed = password_hash($nova_senha, PASSWORD_DEFAULT); // Criptografa a nova senha
        $sql = "UPDATE usuario SET senha = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nova_senha_hashed, $userId);
        $stmt->execute();
    }
    // Feche o statement e a conexão antes de redirecionar
    $stmt->close();
    $conn->close();
    // Redireciona para a página de origem
    header("Location: ../$origem/telainicio.php");
    exit();
} else {
    // Feche o statement e a conexão em caso de erro
    $stmt->close();
    $conn->close();
    die("Erro ao atualizar os dados: " . $stmt->error);
}
?>