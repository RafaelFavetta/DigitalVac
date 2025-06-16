<?php
include('../outros/db_connect.php');
header('Content-Type: application/json');

// Log para debug
file_put_contents(__DIR__ . '/debug_autocomplete.txt', date('c') . " - q=" . (isset($_GET['q']) ? $_GET['q'] : '') . PHP_EOL, FILE_APPEND);

$cpfs = [];

if (!$conn || $conn->connect_errno) {
    file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Erro de conexão\n", FILE_APPEND);
    echo json_encode(['error' => 'Erro de conexão com o banco']);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    $sql = "SELECT cpf FROM usuario";
    $result = $conn->query($sql);
    if ($result === false) {
        file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Erro na consulta SQL: " . $conn->error . "\n", FILE_APPEND);
        echo json_encode(['error' => 'Erro na consulta SQL: ' . $conn->error]);
        exit;
    }
    while ($row = $result->fetch_assoc()) {
        $cpfs[] = $row['cpf'];
    }
} else {
    $sql = "SELECT cpf FROM usuario WHERE cpf LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Erro no prepare SQL: " . $conn->error . "\n", FILE_APPEND);
        echo json_encode(['error' => 'Erro no prepare SQL: ' . $conn->error]);
        exit;
    }
    $like = $q . '%';
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Erro ao executar get_result: " . $stmt->error . "\n", FILE_APPEND);
        echo json_encode(['error' => 'Erro ao executar get_result: ' . $stmt->error]);
        exit;
    }
    while ($row = $result->fetch_assoc()) {
        $cpfs[] = $row['cpf'];
    }
    $stmt->close();
}

if (empty($cpfs)) {
    file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Nenhum CPF encontrado\n", FILE_APPEND);
    echo json_encode(['info' => 'Nenhum CPF encontrado']);
    exit;
}

file_put_contents(__DIR__ . '/debug_autocomplete.txt', "Retornando CPFs: " . json_encode($cpfs) . "\n", FILE_APPEND);
echo json_encode($cpfs);
?>