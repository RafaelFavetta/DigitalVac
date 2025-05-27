<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../outros/db_connect.php'); // Incluindo o arquivo de conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebendo os dados do formulário
    $nome_paciente = htmlspecialchars($_POST['nome_paciente']); // Nome do paciente
    $nome_medico = htmlspecialchars($_POST['nome_medico']); // Nome do médico
    $data_inicio = htmlspecialchars($_POST['data_inicio']); // Data de início do atestado
    $data_termino = htmlspecialchars($_POST['data_termino']); // Data de término do atestado
    $justificativa = htmlspecialchars($_POST['justificativa']); // Justificativa do atestado
    $origem = isset($_POST['origem']) ? htmlspecialchars($_POST['origem']) : 'medica';

    // Validações de campos obrigatórios
    if (empty($nome_paciente)) {
        echo json_encode(['success' => false, 'message' => "Erro: O campo 'Nome do Paciente' é obrigatório."]);
        exit;
    }
    if (empty($nome_medico)) {
        echo json_encode(['success' => false, 'message' => "Erro: O campo 'Nome do Médico' é obrigatório."]);
        exit;
    }
    if (empty($data_inicio)) {
        echo json_encode(['success' => false, 'message' => "Erro: O campo 'Data de Início' é obrigatório."]);
        exit;
    }
    if (empty($data_termino)) {
        echo json_encode(['success' => false, 'message' => "Erro: O campo 'Data de Término' é obrigatório."]);
        exit;
    }
    if (empty($justificativa)) {
        echo json_encode(['success' => false, 'message' => "Erro: O campo 'Justificativa' é obrigatório."]);
        exit;
    }

    // Buscar o ID do paciente pelo nome na tabela `usuario`
    $nome_paciente = trim($nome_paciente);
    $query_paciente = "SELECT id_usuario FROM usuario WHERE TRIM(LOWER(nome_usuario)) = TRIM(LOWER(?))";
    $stmt_paciente = $conn->prepare($query_paciente);
    if (!$stmt_paciente) {
        echo json_encode(['success' => false, 'message' => "Erro interno ao preparar consulta de paciente."]);
        exit;
    }
    $stmt_paciente->bind_param("s", $nome_paciente);
    $stmt_paciente->execute();
    $result_paciente = $stmt_paciente->get_result();

    if ($result_paciente->num_rows > 0) {
        $row_paciente = $result_paciente->fetch_assoc();
        $id_paci = $row_paciente['id_usuario'];
    } else {
        echo json_encode(['success' => false, 'message' => "Erro: Paciente não encontrado."]);
        exit;
    }

    // Buscar o ID do médico pelo nome na tabela `medico`
    $nome_medico = trim($nome_medico);
    $query_medico = "SELECT id_medico FROM medico WHERE TRIM(LOWER(nome_medico)) = TRIM(LOWER(?))";
    $stmt_medico = $conn->prepare($query_medico);
    if (!$stmt_medico) {
        echo json_encode(['success' => false, 'message' => "Erro interno ao preparar consulta de médico."]);
        exit;
    }
    $stmt_medico->bind_param("s", $nome_medico);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();

    if ($result_medico->num_rows > 0) {
        $row_medico = $result_medico->fetch_assoc();
        $id_medico = $row_medico['id_medico'];
    } else {
        echo json_encode(['success' => false, 'message' => "Erro: Médico não encontrado."]);
        exit;
    }

    // Preparando a consulta SQL para inserir os dados no banco de dados
    $sql = "INSERT INTO atestado (id_paci, id_medico, data_inicio, data_fim, justificativa) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar cadastro de atestado: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("iisss", $id_paci, $id_medico, $data_inicio, $data_termino, $justificativa);

    // Executando a consulta
    if ($stmt->execute()) {
        $novo_id = $conn->insert_id;
        // Redireciona para a tela inicial do médico com o ID do atestado na URL
        echo json_encode(['success' => true, 'message' => 'Cadastro de atestado realizado com sucesso!', 'atestado_id' => $novo_id]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar atestado: ' . $stmt->error]);
        exit;
    }

    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>