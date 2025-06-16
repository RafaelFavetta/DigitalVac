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
function buscarCidadePorCEP($cep)
{
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) !== 8)
        return null;
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    $response = @file_get_contents($url);
    if ($response === false)
        return null;
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

// Dados para o PDF (mantém UTF-8, igual ao download da médica)
$nome_paciente = $atestado['nome_paciente'];
$medico_responsavel = $atestado['nome_medico'];
$coren_crm = $atestado['coren_crm'];
$justificativa = $atestado['justificativa'];
$periodo_afastamento = $atestado['periodo_afastamento'];
$cidade_pdf = $cidade;
$nome_medico_completo = 'Dr. ' . $atestado['nome_medico'];

// Cria o PDF usando a biblioteca FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Logo prefeitura maior à esquerda (dobrado para 70mm)
$pdf->Image('../img/logo-prefeitura.png', 10, 10, 70); // 70mm de largura

// Logo DigitalVac à direita
$pdf->Image('../img/logo.png', 150, 10, 35); // 35mm à direita

// Centraliza nome do médico e COREN/CRM em relação à logo DigitalVac
$pdf->SetXY(150, 48); // Abaixo da logo DigitalVac
$pdf->SetFont('Times', 'I', 14);
$pdf->Cell(35, 7, utf8_decode($nome_medico_completo), 0, 2, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 6, utf8_decode($coren_crm), 0, 2, 'C');

// Abaixa o restante do documento para centralizar melhor
$pdf->SetY(75);

// Título centralizado
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 12, utf8_decode('ATESTADO MÉDICO'), 0, 1, 'C');
$pdf->Ln(5);

// Corpo do texto
$pdf->SetFont('Arial', '', 12);
$data_inicio_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->MultiCell(
    0,
    10,
    utf8_decode("Atesto para os devidos fins que $nome_paciente esteve sob tratamento médico em meu consultório às ____:____ do dia $data_inicio_formatada. Recomendo o afastamento imediato do paciente por motivo de: $justificativa.")
);
$pdf->Ln(2);
$pdf->MultiCell(
    0,
    10,
    utf8_decode("Período de afastamento necessário: $periodo_afastamento.")
);

// Local e data
$pdf->Ln(10);
$data_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->Cell(0, 10, utf8_decode("$cidade_pdf, $data_formatada"), 0, 1, 'L');

// Frase de validade legal antes da assinatura (mais para o meio da folha)
$pdf->Ln(35);
$pdf->SetFont('Arial', 'I', 11);
$pdf->MultiCell(0, 8, utf8_decode('Este documento é válido para fins de comprovação junto ao empregador ou órgão competente.'), 0, 'C');

// Espaço para assinatura do médico centralizada (mais abaixo)
$pdf->Ln(30);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('__________________________'), 0, 1, 'C');
$pdf->Cell(0, 7, utf8_decode($nome_medico_completo), 0, 1, 'C');
$pdf->Cell(0, 7, utf8_decode('Assinatura do médico'), 0, 1, 'C');

// Saída do PDF para download
$pdf->Output('D', 'atestado_' . $id_atestado . '.pdf');

$conn->close();
?>
<style>
    body {
        background: #FDFDFD !important;
    }
</style>