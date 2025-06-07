<?php
header('Content-Type: application/json');
include(__DIR__ . '/../outros/db_connect.php');

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_vacina = $_POST["nome"];
    $fabricante = $_POST["fabricante"];
    $lote = $_POST["lote"];
    $idade_aplica = $_POST["idade_aplica"];
    $idade_meses_reco = isset($_POST['idade_meses_reco']) ? intval($_POST['idade_meses_reco']) : null;
    $idade_anos_reco = isset($_POST['idade_anos_reco']) ? intval($_POST['idade_anos_reco']) : null;
    $via = $_POST["via"];
    $doses = $_POST["doses"];
    $intervalo = isset($_POST['intervalo']) ? preg_replace('/\D/', '', $_POST['intervalo']) : '';
    $estoque = $_POST['estoque'];
    $origem = isset($_POST['origem']) ? htmlspecialchars($_POST['origem']) : 'admin';

    if (empty($nome_vacina) || empty($fabricante) || empty($lote) || empty($idade_aplica) || empty($via) || empty($doses) || empty($intervalo) || empty($estoque)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Por favor, preencha todos os campos obrigatórios."]);
        exit;
    }

    $sql = "INSERT INTO vacina (nome_vaci, lote_vaci, fabri_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque, idade_meses_reco, idade_anos_reco) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisiisii", $nome_vacina, $lote, $fabricante, $idade_aplica, $via, $doses, $intervalo, $estoque, $idade_meses_reco, $idade_anos_reco);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Cadastro de vacina realizado com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Erro ao cadastrar vacina: " . $stmt->error]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>