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

    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO posto (nome_posto, cep_posto, n_posto) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome_posto, $cep, $numero_posto);

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