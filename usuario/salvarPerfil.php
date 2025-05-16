<?php
include("../outros/db_connect.php");
include("../Recebedados/validacoes.php"); // Include validation functions
session_start();

// Verifique se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Obtenha os dados do formulário
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';

// Valide os dados
if (empty($email) || empty($telefone) || empty($genero)) {
    echo "<script>alert('Todos os campos são obrigatórios.');</script>";
    echo "<script>window.location.href = 'editarPefilU.php';</script>";
    exit();
}

if (!validarTelefone($telefone)) {
    echo "<script>alert('Telefone inválido.');</script>";
    echo "<script>window.location.href = 'editarPefilU.php';</script>";
    exit();
}

// Atualize os dados no banco de dados
$sql = "UPDATE usuario SET email_usuario = ?, tel_usuario = ?, genero_usuario = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $email, $telefone, $genero, $_SESSION['id_usuario']);
$stmt->execute();

// Verifique se a atualização foi bem-sucedida
if ($stmt->affected_rows > 0) {
    echo "<script>alert('Perfil atualizado com sucesso!');</script>";
    echo "<script>window.location.href = 'perfilU.php';</script>";
} else {
    echo "<script>alert('Nenhuma alteração foi feita ou ocorreu um erro.');</script>";
    echo "<script>window.location.href = 'editarPefilU.php';</script>";
}

// Feche a conexão
$stmt->close();
$conn->close();
?>