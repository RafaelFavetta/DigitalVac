<?php
header('Content-Type: application/json');

// Inclui o arquivo de conexão
include('../outros/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_campanha = $_POST['nome_campanha'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    // Validação básica
    if (!$nome_campanha || !$data_inicio || !$data_fim || !$descricao || !isset($_FILES['imagem'])) {
        echo json_encode(['success' => false, 'message' => "Preencha todos os campos."]);
        $conn->close();
        exit;
    }

    // Upload da imagem
    $imagem = $_FILES['imagem'];
    $upload_dir = "../uploads/campanhas/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid('campanha_', true) . '.' . $ext;
    $caminho_arquivo = $upload_dir . $nome_arquivo;

    // Verifica tipo de arquivo
    $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $tipos_permitidos)) {
        echo json_encode(['success' => false, 'message' => "Tipo de imagem não permitido."]);
        $conn->close();
        exit;
    }

    if (!move_uploaded_file($imagem['tmp_name'], $caminho_arquivo)) {
        echo json_encode(['success' => false, 'message' => "Erro ao salvar a imagem."]);
        $conn->close();
        exit;
    }

    // Caminho relativo para salvar no banco
    $caminho_relativo = "uploads/campanhas/" . $nome_arquivo;

    // Prepara a consulta SQL
    $sql = "INSERT INTO campanha (nome_campanha, data_inicio, data_fim, imagem, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome_campanha, $data_inicio, $data_fim, $caminho_relativo, $descricao);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Campanha cadastrada com sucesso!"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Erro ao cadastrar a campanha: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>
