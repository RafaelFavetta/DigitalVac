<?php
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

    // Busca endereço e cidade pelo CEP usando ViaCEP
    $cep_limpo = preg_replace('/[^0-9]/', '', $cep);
    $endereco = '';
    $cidade = '';
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

    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO posto (nome_posto, cep_posto, n_posto, endereco, cidade) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $nome_posto, $cep, $numero_posto, $endereco, $cidade);

    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        echo "Posto cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o posto: " . $stmt->error;
    }

    // Fecha a declaração e a conexão
    $stmt->close();
}

$conn->close();
?>