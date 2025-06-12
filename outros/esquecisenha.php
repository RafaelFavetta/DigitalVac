<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('db_connect.php');

    $coren_crm = $_POST['coren_crm'] ?? '';
    $coren_crm = trim($coren_crm);

    if (empty($coren_crm)) {
        $mensagem = "Por favor, informe o COREN/CRM.";
    } else {
        $sql = "SELECT id_medico FROM medico WHERE coren_crm = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $coren_crm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            session_start();
            $_SESSION['coren_crm'] = $coren_crm;
            $_SESSION['cargo'] = 'medico';
            header("Location: alterar_senha.php");
            exit;
        } else {
            $mensagem = "COREN/CRM não encontrado.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/telalogin.css">
</head>
<body class="bg-light">
    <?php if (isset($mensagem) && $mensagem): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 350px; position: relative;">
            <!-- Botão de Voltar -->
            <a href="javascript:history.back()" class="btn btn-link text-decoration-none text-primary"
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Redefinir Senha</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="coren_crm" class="form-label">Digite seu COREN/CRM</label>
                    <input type="text" id="coren_crm" name="coren_crm" class="form-control" placeholder="COREN/CRM-UF 000000" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verificar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>