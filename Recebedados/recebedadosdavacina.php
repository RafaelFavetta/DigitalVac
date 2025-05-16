<?php
include(__DIR__ . '/../outros/db_connect.php');

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_vacina = $_POST["nome"];
    $fabricante = $_POST["fabricante"];
    $lote = $_POST["lote"];
    $idade_aplica = $_POST["idade_min"];
    $via = $_POST["via"];
    $doses = $_POST["doses"];
    $intervalo = $_POST['intervalo'];
    $estoque = $_POST['estoque'];
    $origem = isset($_POST['origem']) ? htmlspecialchars($_POST['origem']) : 'admin';

    if (empty($nome_vacina) || empty($fabricante) || empty($lote) || empty($idade_aplica) || empty($via) || empty($doses) || empty($intervalo) || empty($estoque)) {
        die("Por favor, preencha todos os campos obrigatórios.");
    }

    $sql = "INSERT INTO vacina (nome_vaci, lote_vaci, fabri_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisiis", $nome_vacina, $lote, $fabricante, $idade_aplica, $via, $doses, $intervalo, $estoque);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastro de vacina realizado com sucesso!'); window.location.href = '../$origem/telainicio.php';</script>";
    } else {
        die("Erro ao cadastrar vacina: " . $stmt->error);
    }
}
?>