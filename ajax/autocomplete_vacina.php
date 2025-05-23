<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';

// Função para remover acentos, hífens, espaços e pontuação
function normalize($str) {
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
    return strtolower($str);
}

$search = normalize($q);

$suggestions = [];
$sql = "SELECT nome_vaci FROM vacina";
$result = $conn->query($sql);
$found = [];
while ($row = $result->fetch_assoc()) {
    $nome = $row['nome_vaci'];
    $nome_norm = normalize($nome);
    if ($search === '' ||
        strpos($nome_norm, $search) !== false ||
        similar_text($nome_norm, $search) / max(strlen($nome_norm), 1) > 0.7 ||
        preg_match('/' . implode('.*', str_split($search)) . '/i', $nome_norm)
    ) {
        $found[$nome] = true;
    }
}
$suggestions = array_keys($found);
// Ordena alfabeticamente
sort($suggestions, SORT_LOCALE_STRING);
$suggestions = array_slice($suggestions, 0, 10);

echo json_encode($suggestions);
?>
