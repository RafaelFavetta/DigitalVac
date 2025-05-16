<?php
session_start();
require_once '../outros/db_connect.php'; // Usando o arquivo db_connect.php para conexão
require_once __DIR__ . '/../outros/fpdf186/fpdf.php';

// Verifica se o ID do usuário está na sessão
if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];

// Verifica se o ID do atestado foi passado via GET
if (!isset($_GET['id'])) {
    die("Atestado não especificado.");
}

$id_atestado = intval($_GET['id']);

// Consulta para buscar os dados do atestado
$sql = "SELECT u.nome_usuario AS nome_paciente, 
               m.nome_medico AS medico_responsavel, 
               a.data_inicio AS data_emissao, 
               a.justificativa AS justificativa, 
               CONCAT(DATE_FORMAT(a.data_inicio, '%d/%m/%Y'), ' a ', DATE_FORMAT(a.data_fim, '%d/%m/%Y')) AS periodo_afastamento
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

// Cria o PDF usando a biblioteca FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Adiciona logo
$pdf->Image('../img/logo.png', 10, 6, 30);

// Cabeçalho
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode($atestado['medico_responsavel']), 0, 1, 'R');
$pdf->Ln(10);

// Título
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'Atestado', 0, 1, 'C');
$pdf->Ln(5);

// Corpo do texto
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(
    0,
    10,
    utf8_decode(
        "Atesto para os devidos fins que " . $atestado['nome_paciente'] .
        ", residente e domiciliado(a), esteve sob tratamento médico neste consultório, no período de " .
        $atestado['periodo_afastamento'] .
        ", necessitando o(a) mesmo(a) de afastamento por motivo de " .
        $atestado['justificativa'] . "."
    )
);

// Local e data
$pdf->Ln(10);
$data_formatada = date('d/m/Y', strtotime($atestado['data_emissao']));
$pdf->Cell(0, 10, utf8_decode("Porto Alegre, $data_formatada"), 0, 1, 'L');

// Espaço para assinaturas
$pdf->Ln(20);
$pdf->Cell(90, 10, '__________________________', 0, 0, 'C');
$pdf->Cell(10, 10, '', 0, 0);
$pdf->Cell(90, 10, '__________________________', 0, 1, 'C');
$pdf->Cell(90, 5, 'Assinatura do paciente', 0, 0, 'C');
$pdf->Cell(10, 5, '', 0, 0);
$pdf->Cell(90, 5, 'Assinatura do médico', 0, 1, 'C');

// Rodapé


// Saída do PDF para download
$pdf->Output('D', 'atestado_' . $id_atestado . '.pdf');

$conn->close();
?>