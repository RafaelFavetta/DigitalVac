<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';
$fuzzy = isset($_GET['fuzzy']) ? $_GET['fuzzy'] : '';

// Função para remover acentos, hífens, espaços e pontuação
function normalize($str) {
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
    return strtolower($str);
}

$search = normalize($q);
$fuzzy_search = normalize($fuzzy);

$suggestions = [];

if ($search !== '') {
    // Busca normalizada no banco
    $sql = "SELECT nome_medico FROM medico";
    $result = $conn->query($sql);
    $found = [];
    while ($row = $result->fetch_assoc()) {
        $nome = $row['nome_medico'];
        $nome_norm = normalize($nome);
        // Busca exata, substring ou fuzzy (todas as letras na ordem)
        if (
            strpos($nome_norm, $search) !== false ||
            similar_text($nome_norm, $search) / max(strlen($nome_norm), 1) > 0.7 ||
            preg_match('/' . implode('.*', str_split($search)) . '/i', $nome_norm)
        ) {
            $found[$nome] = true;
        }
    }
    $suggestions = array_keys($found);
    $suggestions = array_slice($suggestions, 0, 10);
}

echo json_encode($suggestions);
?>
