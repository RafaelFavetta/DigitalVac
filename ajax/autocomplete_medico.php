<?php
include('../outros/db_connect.php');
$q = isset($_GET['q']) ? $_GET['q'] : '';

// Remove tudo que não é letra ou número
function normalize($str)
{
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $str));
}

$search = normalize($q);

$suggestions = [];
$sql = "SELECT coren_crm FROM medico";
$result = $conn->query($sql);
$found = [];
while ($row = $result->fetch_assoc()) {
    $coren = normalize($row['coren_crm']);
    if (
        $search === '' ||
        strpos($coren, $search) !== false ||
        preg_match('/' . implode('.*', str_split($search)) . '/i', $coren)
    ) {
        $found[$row['coren_crm']] = true;
    }
}
$suggestions = array_keys($found);
// Ordena alfabeticamente
sort($suggestions, SORT_LOCALE_STRING);
$suggestions = array_slice($suggestions, 0, 10);

echo json_encode($suggestions);
?>