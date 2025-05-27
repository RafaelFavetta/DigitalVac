<?php
session_start();
require_once '../outros/db_connect.php';
require_once __DIR__ . '/../outros/fpdf186/fpdf.php';

// Permitir apenas médicos autenticados
if (!isset($_SESSION['id_medico'])) {
    die("Usuário não autenticado.");
}

// Permitir baixar qualquer atestado (não filtra por id_medico)
if (!isset($_GET['id'])) {
    die("Atestado não especificado.");
}
$id_atestado = intval($_GET['id']);

// Consulta para buscar os dados do atestado e cidade do paciente (sem filtro por médico)
$sql = "SELECT u.nome_usuario AS nome_paciente, 
               m.nome_medico AS medico_responsavel, 
               a.data_inicio AS data_emissao, 
               a.justificativa AS justificativa, 
               CONCAT(DATE_FORMAT(a.data_inicio, '%d/%m/%Y'), ' a ', DATE_FORMAT(a.data_fim, '%d/%m/%Y')) AS periodo_afastamento,
               u.cep_usuario
        FROM atestado a
        INNER JOIN usuario u ON a.id_paci = u.id_usuario
        INNER JOIN medico m ON a.id_medico = m.id_medico
        WHERE a.id_atestado = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_atestado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Atestado não encontrado ou você não tem permissão para acessá-lo.");
}

$atestado = $result->fetch_assoc();

// Busca cidade pelo CEP (pois campo cidade pode não existir)
function buscarCidadePorCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) !== 8) return null;
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    $response = @file_get_contents($url);
    if ($response === false) return null;
    $dados = json_decode($response, true);
    if (isset($dados['localidade']) && !empty($dados['localidade'])) {
        return $dados['localidade'];
    }
    return null;
}
$cidade = buscarCidadePorCEP($atestado['cep_usuario']);
if (!$cidade) {
    $cidade = "Cidade não encontrada";
}

// Não converte mais para Windows-1252, mantém UTF-8
$nome_paciente = $atestado['nome_paciente'];
$medico_responsavel = $atestado['medico_responsavel'];
$justificativa = $atestado['justificativa'];
$periodo_afastamento = $atestado['periodo_afastamento'];
$cidade_pdf = $cidade;

// Cria o PDF usando a biblioteca FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Adiciona logo
$pdf->Image('../img/logo.png', 10, 6, 30);

// Cabeçalho
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode($medico_responsavel), 0, 1, 'R');
$pdf->Ln(10);

// Título
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, utf8_decode('Atestado'), 0, 1, 'C');
$pdf->Ln(5);

// Corpo do texto
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(
    0,
    10,
    utf8_decode("Atesto para os devidos fins que $nome_paciente esteve sob tratamento médico neste consultório, no período de $periodo_afastamento, necessitando o(a) mesmo(a) de afastamento por motivo de: $justificativa.")
);

// Local e data
$pdf->Ln(10);
$data_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->Cell(0, 10, utf8_decode("$cidade_pdf, $data_formatada"), 0, 1, 'L');

// Espaço para assinatura do médico centralizada
$pdf->Ln(25);
$pdf->Cell(0, 10, utf8_decode('__________________________'), 0, 1, 'C');
$pdf->Cell(0, 7, utf8_decode($medico_responsavel), 0, 1, 'C');
$pdf->Cell(0, 7, utf8_decode('Assinatura do médico'), 0, 1, 'C');

// Saída do PDF para download
$pdf->Output('D', 'atestado_' . $id_atestado . '.pdf');

$conn->close();
?>
