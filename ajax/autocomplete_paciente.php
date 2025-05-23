<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';

// Remove tudo que não é número
function normalize($str) {
    return preg_replace('/\D/', '', $str);
}

$search = normalize($q);

$suggestions = [];
if ($search !== '') {
    $sql = "SELECT cpf FROM usuario";
    $result = $conn->query($sql);
    $found = [];
    while ($row = $result->fetch_assoc()) {
        $cpf = preg_replace('/\D/', '', $row['cpf']);
        if (
            strpos($cpf, $search) !== false ||
            preg_match('/' . implode('.*', str_split($search)) . '/i', $cpf)
        ) {
            $found[$row['cpf']] = true;
        }
    }
    $suggestions = array_keys($found);
    $suggestions = array_slice($suggestions, 0, 10);
}

echo json_encode($suggestions);
?>
