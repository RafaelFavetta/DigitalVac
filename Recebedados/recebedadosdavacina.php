<?php
header('Content-Type: application/json');
include('../outros/db_connect.php');

// Recebe dados do POST
$lote = $_POST['lote'] ?? '';
$nome = $_POST['nome'] ?? '';
$fabricante = $_POST['fabricante'] ?? '';
$doses = $_POST['doses'] ?? '';
$via = $_POST['via'] ?? '';
$intervalo = $_POST['intervalo'] ?? '';
$estoque = $_POST['estoque'] ?? '';
$sus = $_POST['sus'] ?? '';

// Corrigido: Recebe corretamente os campos de idade
$idade_anos_reco = isset($_POST['idade_anos_reco']) ? intval($_POST['idade_anos_reco']) : 0;
$idade_meses_reco = isset($_POST['idade_meses_reco']) ? intval($_POST['idade_meses_reco']) : 0;

// Recebe idade_reco_final (ex: "7 anos" ou "18 meses")
$idade_reco = isset($_POST['idade_reco_final']) ? trim($_POST['idade_reco_final']) : '';

// Validação básica (agora valida os campos corretos)
if (
    !$lote || !$nome || !$fabricante || $doses === '' || !$via || $intervalo === '' ||
    $idade_reco === '' || $estoque === '' || $sus === ''
) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

// Prepara o SQL com o campo idade_reco (VARCHAR)
$sql = "INSERT INTO vacina (lote_vaci, nome_vaci, fabri_vaci, n_dose, via_adimicao, intervalo_dose, idade_reco, estoque, sus)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro no prepare: ' . $conn->error]);
    exit;
}
$stmt->bind_param(
    "sssisissi",
    $lote,
    $nome,
    $fabricante,
    $doses,
    $via,
    $intervalo,
    $idade_reco,
    $estoque,
    $sus
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vacina cadastrada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>