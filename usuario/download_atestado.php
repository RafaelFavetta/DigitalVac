<?php
session_start();
require_once '../outros/db_connect.php';
require_once __DIR__ . '/../outros/fpdf186/fpdf.php';

// Permitir apenas usuários autenticados
if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}
$id_usuario = $_SESSION['id_usuario'];

// Verifica se o ID do atestado foi passado via GET
if (!isset($_GET['id'])) {
    die("Atestado não especificado.");
}
$id_atestado = intval($_GET['id']);

// Consulta para buscar os dados do atestado e cidade do paciente (filtra por usuário)
$sql = "SELECT u.nome_usuario AS nome_paciente, 
               m.nome_medico AS nome_medico, 
               m.coren_crm AS coren_crm, 
               a.data_inicio AS data_emissao, 
               a.justificativa AS justificativa, 
               CONCAT(DATE_FORMAT(a.data_inicio, '%d/%m/%Y'), ' a ', DATE_FORMAT(a.data_fim, '%d/%m/%Y')) AS periodo_afastamento,
               u.cep_usuario
        FROM atestado a
        INNER JOIN usuario u ON a.id_paci = u.id_usuario
        INNER JOIN medico m ON a.id_medico = m.id_medico
        WHERE a.id_atestado = ? AND a.id_paci = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("ii", $id_atestado, $id_usuario);
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
    $cidade = "Porto Alegre";
}

// Função para converter para Windows-1252
function utf8_to_win1252($str) {
    return mb_convert_encoding($str, 'Windows-1252', 'UTF-8');
}

// Converta todos os campos dinâmicos para Windows-1252
$nome_paciente = utf8_to_win1252($atestado['nome_paciente']);
$medico_responsavel = utf8_to_win1252($atestado['nome_medico']);
$coren_crm = utf8_to_win1252($atestado['coren_crm']);
$justificativa = utf8_to_win1252($atestado['justificativa']);
$periodo_afastamento = utf8_to_win1252($atestado['periodo_afastamento']);
$cidade_pdf = utf8_to_win1252($cidade);

// Sempre usar "Dr.(a) Nome"
$nome_medico_completo = utf8_to_win1252('Dr.(a) ' . $atestado['nome_medico']);

// Cria o PDF usando a biblioteca FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// TOPO: Logo prefeitura à esquerda, logo DigitalVac à direita
$pdf->Image('../img/logo-prefeitura.png', 10, 10, 35); // Prefeitura à esquerda
$pdf->Image('../img/logo.png', 165, 10, 35); // DigitalVac à direita

// Espaço após logos
$pdf->Ln(28);

// Nome do médico (cursiva, centralizado)
$pdf->SetFont('Times', 'I', 16);
$pdf->Cell(0, 8, $nome_medico_completo, 0, 1, 'C');

// COREN/CRM (centralizado, menor)
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, $coren_crm, 0, 1, 'C');
$pdf->Ln(5);

// Título
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 12, utf8_to_win1252('ATESTADO MÉDICO'), 0, 1, 'C');
$pdf->Ln(5);

// Corpo do texto
$pdf->SetFont('Arial', '', 12);
$data_inicio_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->MultiCell(
    0,
    10,
    utf8_to_win1252("Atesto para os devidos fins que $nome_paciente esteve sob tratamento médico em meu consultório às ____:____ do dia $data_inicio_formatada, necessitando o(a) mesmo(a) de afastamento por motivo de: $justificativa.")
);
$pdf->Ln(2);
$pdf->MultiCell(
    0,
    10,
    utf8_to_win1252("Período de afastamento: $periodo_afastamento.")
);

// Local e data
$pdf->Ln(10);
$data_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->Cell(0, 10, utf8_to_win1252("$cidade_pdf, $data_formatada"), 0, 1, 'L');

// Espaço para assinatura do médico centralizada
$pdf->Ln(25);
$pdf->Cell(0, 10, utf8_to_win1252('__________________________'), 0, 1, 'C');
$pdf->Cell(0, 7, $medico_responsavel, 0, 1, 'C');
$pdf->Cell(0, 7, utf8_to_win1252('Assinatura do médico'), 0, 1, 'C');

// Saída do PDF para download
$pdf->Output('D', 'atestado_' . $id_atestado . '.pdf');

$conn->close();
?>