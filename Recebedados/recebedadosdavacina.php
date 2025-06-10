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
$idade_aplica = $_POST['idade_aplica'] ?? '';
$estoque = $_POST['estoque'] ?? '';

// Validação básica
if (!$lote || !$nome || !$fabricante || $doses === '' || !$via || $intervalo === '' || $idade_aplica === '' || $estoque === '') {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

// Exemplo: supondo que idade_aplica seja em anos
$idade_anos_reco = intval($idade_aplica);
$idade_meses_reco = 0;

// Ajuste conforme sua tabela, por exemplo, se id_calendario for obrigatório:
// $id_calendario = null; // ou obtenha pelo nome da vacina, se necessário

$sql = "INSERT INTO vacina (lote_vaci, nome_vaci, fabri_vaci, n_dose, via_adimicao, intervalo_dose, idade_anos_reco, idade_meses_reco, estoque)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro no prepare: ' . $conn->error]);
    exit;
}
$stmt->bind_param("sssisiiii", $lote, $nome, $fabricante, $doses, $via, $intervalo, $idade_anos_reco, $idade_meses_reco, $estoque);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vacina cadastrada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>