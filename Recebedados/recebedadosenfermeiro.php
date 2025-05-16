<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $nome_medico = htmlspecialchars(trim($_POST['nome_medico']));
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
    $email = htmlspecialchars(trim($_POST['email']));
    $data_nascimento = htmlspecialchars(trim($_POST['data_nascimento']));
    $tipo_medico = htmlspecialchars(trim($_POST['tipo_medico']));
    $posto_trabalho = intval($_POST['posto_trabalho']);
    $coren_crm = htmlspecialchars(trim($_POST['coren_crm']));

    // Validações de campos obrigatórios
    if (empty($nome_medico) || empty($coren_crm) || empty($telefone) || empty($email) || empty($data_nascimento) || empty($tipo_medico) || empty($posto_trabalho)) {
        die("<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.history.back();</script>");
    }

    // Validação do telefone
    if (!validarTelefone($telefone)) {
        die("<script>alert('Telefone inválido. Use 11 dígitos numéricos (com DDD).'); window.history.back();</script>");
    }

    // Validação do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('E-mail inválido.'); window.history.back();</script>");
    }

    // Verificar se o COREN/CRM já está cadastrado
    $stmt = $conn->prepare("SELECT id_medico FROM medico WHERE coren_crm = ?");
    $stmt->bind_param("s", $coren_crm);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("<script>alert('Erro: Este COREN/CRM já está cadastrado.'); window.history.back();</script>");
    }
    $stmt->close();

    // Certifique-se de que o CPF seja armazenado sem formatação
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Criptografa a senha gerada automaticamente (data de nascimento no formato ddmmaaaa)
    $senha = date('dmY', strtotime($data_nascimento));
    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

    // Preparar a query de inserção no banco de dados
    $sql = "INSERT INTO medico (nome_medico, coren_crm, tel_medico, email_medico, naci_medico, tipo_medico, id_posto, senha) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind dos parâmetros
        $stmt->bind_param("ssssssis", $nome_medico, $coren_crm, $telefone, $email, $data_nascimento, $tipo_medico, $posto_trabalho, $senha_hashed);

        // Executa a query
        if ($stmt->execute()) {
            echo "<script>
                    alert('Cadastro de médico realizado com sucesso!');
                    window.location.href = '../admin/telainicio.php';
                  </script>";
        } else {
            echo "<script>alert('Erro ao cadastrar médico: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "'); window.history.back();</script>";
    }

    $conn->close();
}
?>
