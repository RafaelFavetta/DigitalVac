<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../outros/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $nome_vacina = $_POST['nome_vacina'];
    $dose_aplicad = isset($_POST['dose_aplicad']) ? (int) $_POST['dose_aplicad'] : 0;
    $data_aplica = $_POST['data_aplica'];
    $cpf_paciente = preg_replace('/\D/', '', $_POST['cpf_paciente']);
    $nome_posto = $_POST['nome_posto'];
    $coren_crm = $_POST['coren_crm'];

    // Busca id_usuario pelo CPF
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE cpf = ?");
    $stmt->bind_param("s", $cpf_paciente);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    if (!$id_usuario) {
        echo json_encode(['success' => false, 'message' => 'Paciente não encontrado.']);
        exit;
    }

    // Busca id_vaci pelo nome da vacina
    $stmt = $conn->prepare("SELECT id_vaci, n_dose, estoque FROM vacina WHERE nome_vaci = ?");
    $stmt->bind_param("s", $nome_vacina);
    $stmt->execute();
    $stmt->bind_result($id_vaci, $n_dose, $estoque_atual);
    $stmt->fetch();
    $stmt->close();

    if (!$id_vaci) {
        echo json_encode(['success' => false, 'message' => 'Vacina não encontrada.']);
        exit;
    }

    // Busca id_posto pelo nome do posto
    $stmt = $conn->prepare("SELECT id_posto FROM posto WHERE nome_posto = ?");
    $stmt->bind_param("s", $nome_posto);
    $stmt->execute();
    $stmt->bind_result($id_posto);
    $stmt->fetch();
    $stmt->close();

    if (!$id_posto) {
        echo json_encode(['success' => false, 'message' => 'Posto não encontrado.']);
        exit;
    }

    // Busca id_medico pelo COREN/CRM
    $stmt = $conn->prepare("SELECT id_medico FROM medico WHERE coren_crm = ?");
    $stmt->bind_param("s", $coren_crm);
    $stmt->execute();
    $stmt->bind_result($id_medico);
    $stmt->fetch();
    $stmt->close();

    if (!$id_medico) {
        echo json_encode(['success' => false, 'message' => 'Profissional não encontrado.']);
        exit;
    }

    // Busca quantas doses já aplicadas para este usuário/vacina
    $stmt = $conn->prepare("SELECT COUNT(*) FROM aplicacao WHERE id_usuario = ? AND id_vaci = ?");
    $stmt->bind_param("ii", $id_usuario, $id_vaci);
    $stmt->execute();
    $stmt->bind_result($doses_tomadas);
    $stmt->fetch();
    $stmt->close();

    // Verifica se ainda há doses a serem tomadas
    if ($doses_tomadas >= $n_dose) {
        echo json_encode(['success' => false, 'message' => 'Todas as doses desta vacina já foram aplicadas para este paciente.']);
        exit;
    }

    // Verifica estoque
    if ($estoque_atual <= 0) {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente para esta vacina.']);
        exit;
    }

    // Calcula a próxima dose a ser aplicada
    $proxima_dose = $doses_tomadas + 1;

    // Insere na tabela aplicacao
    $stmt = $conn->prepare("INSERT INTO aplicacao (id_usuario, id_posto, id_medico, id_vaci, data_aplica, dose_aplicad) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiisi", $id_usuario, $id_posto, $id_medico, $id_vaci, $data_aplica, $proxima_dose);

    if ($stmt->execute()) {
        // Atualiza o estoque da vacina
        $stmt->close();
        $stmt = $conn->prepare("UPDATE vacina SET estoque = estoque - 1 WHERE id_vaci = ?");
        $stmt->bind_param("i", $id_vaci);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true, 'message' => 'Aplicação cadastrada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar aplicação: ' . $stmt->error]);
        $stmt->close();
        $conn->close();
    }
    exit;
}

// Se for usar intervalo em alguma lógica, trate como inteiro (meses)

echo json_encode(['success' => false, 'message' => 'Método inválido.']);
exit;
?>