<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vac";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_posto = $_POST['nome_posto'];
    $cep = $_POST['cep'];
    $numero_posto = $_POST['numero_posto'];
    $endereco = $_POST['endereco'] ?? '';
    $cidade = $_POST['cidade'] ?? '';

    // Se não veio endereço/cidade do POST, tenta buscar pelo CEP (fallback)
    if (empty($endereco) || empty($cidade)) {
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep);
        if (strlen($cep_limpo) === 8) {
            $url = "https://viacep.com.br/ws/{$cep_limpo}/json/";
            $response = @file_get_contents($url);
            if ($response !== false) {
                $dados = json_decode($response, true);
                if (!isset($dados['erro'])) {
                    $endereco = ($dados['logradouro'] ?? '') . ', ' . ($dados['bairro'] ?? '') . ', ' . ($dados['localidade'] ?? '') . ' - ' . ($dados['uf'] ?? '');
                    $cidade = $dados['localidade'] ?? '';
                }
            }
        }
    }

    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO posto (nome_posto, cep_posto, n_posto, endereco, cidade) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $nome_posto, $cep, $numero_posto, $endereco, $cidade);

    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Posto cadastrado com sucesso!"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Erro ao cadastrar o posto: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => "Método inválido."]);
exit;
?>