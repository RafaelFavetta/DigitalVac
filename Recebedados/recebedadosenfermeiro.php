<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('../outros/db_connect.php');
include_once('../Recebedados/validacoes.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $nome_medico = htmlspecialchars(trim($_POST['nome_medico']));
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
    $email = htmlspecialchars(trim($_POST['email']));
    $data_nascimento = htmlspecialchars(trim($_POST['data_nascimento']));
    $tipo_medico = htmlspecialchars(trim($_POST['tipo_medico']));
    $posto_trabalho = intval($_POST['posto_trabalho']);
    $coren_crm = htmlspecialchars(trim($_POST['coren_crm']));

    // Validações de campos obrigatórios
    if (empty($nome_medico) || empty($cpf) || empty($coren_crm) || empty($telefone) || empty($email) || empty($data_nascimento) || empty($tipo_medico) || empty($posto_trabalho)) {
        echo json_encode(['success' => false, 'message' => "Por favor, preencha todos os campos obrigatórios."]);
        exit;
    }

    // Validação do CPF
    if (!validarCPF($cpf)) {
        echo json_encode(['success' => false, 'message' => "CPF inválido."]);
        exit;
    }

    // Validação do telefone
    if (!validarTelefone($telefone)) {
        echo json_encode(['success' => false, 'message' => "Telefone inválido. Use 11 dígitos numéricos (com DDD)."]);
        exit;
    }

    // Validação do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => "E-mail inválido."]);
        exit;
    }

    // Verificar se o CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id_medico FROM medico WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "Erro: Este CPF já está cadastrado."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Verificar se o COREN/CRM já está cadastrado
    $stmt = $conn->prepare("SELECT id_medico FROM medico WHERE coren_crm = ?");
    $stmt->bind_param("s", $coren_crm);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "Erro: Este COREN/CRM já está cadastrado."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Certifique-se de que o CPF seja armazenado sem formatação
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Criptografa a senha gerada automaticamente (data de nascimento no formato ddmmaaaa)
    $senha = date('dmY', strtotime($data_nascimento));
    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

    // Preparar a query de inserção no banco de dados
    $sql = "INSERT INTO medico (nome_medico, cpf, coren_crm, tel_medico, email_medico, naci_medico, tipo_medico, id_posto, senha) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind dos parâmetros
        $stmt->bind_param("sssssssis", $nome_medico, $cpf, $coren_crm, $telefone, $email, $data_nascimento, $tipo_medico, $posto_trabalho, $senha_hashed);

        // Executa a query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Cadastro de médico realizado com sucesso!"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erro ao cadastrar médico: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Erro ao preparar a consulta: " . $conn->error]);
    }

    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>
