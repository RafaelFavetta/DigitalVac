<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../outros/db_connect.php'); // Certifique-se de que a conexão está correta
include(__DIR__ . '/../Recebedados/validacoes.php'); // Inclui as funções de validação

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validações de campos obrigatórios
    if (!isset($_POST['nome']) || empty($_POST['nome'])) {
        die("Erro: O campo 'nome' está vazio.");
    }
    if (!isset($_POST['cpf']) || empty($_POST['cpf'])) {
        die("Erro: O campo 'CPF' está vazio.");
    }
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        die("Erro: O campo 'email' está vazio.");
    }
    if (!isset($_POST['telefone']) || empty($_POST['telefone'])) {
        die("Erro: O campo 'telefone' está vazio.");
    }
    if (!isset($_POST['data_nascimento']) || empty($_POST['data_nascimento'])) {
        die("Erro: O campo 'data_nascimento' está vazio.");
    }
    if (!isset($_POST['genero']) || empty($_POST['genero'])) {
        die("Erro: O campo 'genero' está vazio.");
    }
    if (!isset($_POST['peso']) || empty($_POST['peso'])) {
        die("Erro: O campo 'peso' está vazio.");
    }
    if (!isset($_POST['tipo_sanguineo']) || empty($_POST['tipo_sanguineo'])) {
        die("Erro: O campo 'tipo_sanguineo' está vazio.");
    }
    if (!isset($_POST['cep']) || empty($_POST['cep'])) {
        die("Erro: O campo 'cep' está vazio.");
    }
    if (!isset($_POST['numero_casa']) || empty($_POST['numero_casa'])) {
        die("Erro: O campo 'numero_casa' está vazio.");
    }

    // Escapar os dados para evitar SQL Injection
    $nome = htmlspecialchars($_POST['nome']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove caracteres não numéricos do CPF
    $email = htmlspecialchars($_POST['email']);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']); // Remove caracteres não numéricos

    // Validação do telefone
    if (!validarTelefone($telefone)) {
        die("Erro: Telefone inválido. Use 10 ou 11 dígitos numéricos (com DDD).");
    }

    $data_nascimento = htmlspecialchars($_POST['data_nascimento']);
    // Formata a data de nascimento para o padrão ddmmyyyy
    $senha = DateTime::createFromFormat('Y-m-d', $data_nascimento)->format('dmY');
    error_log("Senha gerada (antes da criptografia): " . $senha); // Log para depuração

    // Criptografa a senha formatada
    $senha = password_hash($senha, PASSWORD_DEFAULT);
    error_log("Senha criptografada: " . $senha); // Log para depuração
    
    $genero = htmlspecialchars($_POST['genero']); // Recebe diretamente "M", "F" ou "O"
    $peso = floatval($_POST['peso']);
    $tipo_sanguineo = htmlspecialchars($_POST['tipo_sanguineo']);
    $alergia = isset($_POST['alergia']) ? htmlspecialchars($_POST['alergia']) : null;
    $doencas = isset($_POST['doencas']) ? htmlspecialchars($_POST['doencas']) : null;
    $medicamentos = isset($_POST['medicamentos']) ? htmlspecialchars($_POST['medicamentos']) : null;
    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']); // Limpeza do CEP, mantendo apenas números

    // Validação do CEP
    if (!validarCEP($cep)) {
        die("Erro: CEP inválido. Use 8 dígitos numéricos.");
    }

    $numero_casa = htmlspecialchars($_POST['numero_casa']);
    $origem = isset($_POST['origem']) ? htmlspecialchars($_POST['origem']) : 'usuario';

    // Verificar se o CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE cpf = ?");
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("<script>alert('Erro: Este CPF já está cadastrado.'); window.history.back();</script>");
    }

    $stmt->close();

    try {
        // Preparar a query de inserção no banco de dados
        $sql = "INSERT INTO usuario (nome_usuario, cpf, email_usuario, tel_usuario, naci_usuario, genero_usuario, peso_usuario, tipo_sang_usuario, ale_usuario, doen_usuario, med_usuario, cep_usuario, nc_usuario, senha) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        // Bind dos parâmetros
        $stmt->bind_param(
            "ssssssdsssssss",
            $nome,
            $cpf, // CPF sem máscara
            $email,
            $telefone,
            $data_nascimento,
            $genero, // Já está no formato correto
            $peso,
            $tipo_sanguineo,
            $alergia,
            $doencas,
            $medicamentos,
            $cep,
            $numero_casa,
            $senha
        );

        // Executa a query
        if ($stmt->execute()) {
            echo "<script>
                    alert('Cadastro realizado com sucesso!');
                    window.location.href = '../$origem/telainicio.php';
                  </script>";
        } else {
            die("<script>alert('Erro ao inserir no banco de dados: " . $stmt->error . "'); window.history.back();</script>");
        }

        $stmt->close();
    } catch (Exception $e) {
        die("<script>alert('Erro ao inserir no banco de dados: " . $e->getMessage() . "'); window.history.back();</script>");
    }
}
?>