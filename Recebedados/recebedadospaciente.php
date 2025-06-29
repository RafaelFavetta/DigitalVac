<?php
header('Content-Type: application/json');
include(__DIR__ . '/../outros/db_connect.php');
include_once(__DIR__ . '/../Recebedados/validacoes.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validações de campos obrigatórios
    if (
        empty($_POST['nome']) || empty($_POST['cpf']) || empty($_POST['email']) ||
        empty($_POST['telefone']) || empty($_POST['data_nascimento']) || empty($_POST['genero']) ||
        empty($_POST['peso']) || empty($_POST['tipo_sanguineo']) || empty($_POST['cep']) ||
        empty($_POST['numero_casa'])
    ) {
        echo json_encode(['success' => false, 'message' => "Preencha todos os campos obrigatórios."]);
        exit;
    }

    $nome = trim($_POST['nome']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $email = trim($_POST['email']);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
    $data_nascimento = $_POST['data_nascimento'];
    $genero = strtoupper(substr(trim($_POST['genero']), 0, 1)); // Garante M/F/O
    $peso = floatval($_POST['peso']);
    $tipo_sanguineo = trim($_POST['tipo_sanguineo']);
    $alergia = isset($_POST['alergia']) ? trim($_POST['alergia']) : '';
    $doencas = isset($_POST['doencas']) ? trim($_POST['doencas']) : '';
    $medicamentos = isset($_POST['medicamentos']) ? trim($_POST['medicamentos']) : '';
    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
    $numero_casa = trim($_POST['numero_casa']);
    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';
    $origem = isset($_POST['origem']) ? trim($_POST['origem']) : 'usuario';

    // Validações
    if (!validarCPF($cpf)) {
        echo json_encode(['success' => false, 'message' => "CPF inválido!"]);
        exit;
    }
    if (!validarTelefone($telefone)) {
        echo json_encode(['success' => false, 'message' => "Telefone inválido!"]);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => "E-mail inválido!"]);
        exit;
    }
    if (!validarCEP($cep)) {
        echo json_encode(['success' => false, 'message' => "CEP inválido!"]);
        exit;
    }
    if ($peso <= 0) {
        echo json_encode(['success' => false, 'message' => "Peso inválido!"]);
        exit;
    }
    if (!in_array($genero, ['M', 'F', 'O'])) {
        echo json_encode(['success' => false, 'message' => "Gênero inválido!"]);
        exit;
    }

    // Verificar se o CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "Este CPF já está cadastrado."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Gerar senha padrão (data de nascimento ddmmyyyy)
    $senha_padrao = DateTime::createFromFormat('Y-m-d', $data_nascimento)->format('dmY');
    $senha_hash = password_hash($senha_padrao, PASSWORD_DEFAULT);

    // Inserir no banco de dados
    $sql = "INSERT INTO usuario (
        nome_usuario, cpf, email_usuario, tel_usuario, naci_usuario, genero_usuario, peso_usuario, tipo_sang_usuario,
        ale_usuario, doen_usuario, med_usuario, cep_usuario, nc_usuario, senha, endereco, cidade
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => "Erro ao preparar consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param(
        "ssssssdsssssssss",
        $nome,
        $cpf,
        $email,
        $telefone,
        $data_nascimento,
        $genero,
        $peso,
        $tipo_sanguineo,
        $alergia,
        $doencas,
        $medicamentos,
        $cep,
        $numero_casa,
        $senha_hash,
        $endereco,
        $cidade
    );
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Cadastro realizado com sucesso!"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Erro ao cadastrar: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>