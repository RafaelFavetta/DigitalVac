<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coren_crm = htmlspecialchars(trim($_POST['coren_crm'])); // Sanitize and trim input
    $senha = $_POST['senha'];

    // Verificar se os campos estão preenchidos
    if (empty($coren_crm) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        $stmt = $conn->prepare("SELECT id_medico, coren_crm, senha FROM medico WHERE coren_crm = ?");
        if (!$stmt) {
            error_log("Erro ao preparar a consulta: " . $conn->error); // Log de erro
            $erro = "Erro interno. Tente novamente mais tarde.";
        } else {
            $stmt->bind_param("s", $coren_crm);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                error_log("COREN/CRM encontrado: " . $row['coren_crm']); // Log para depuração

                // Verificar se a senha é criptografada
                if (password_verify($senha, $row['senha'])) { // Caso seja criptografada
                    $_SESSION['id_medico'] = $row['id_medico']; // Store id_medico in session
                    error_log("Login bem-sucedido com senha criptografada."); // Log de sucesso
                    header("Location: telainicio.php");
                    exit();
                } elseif ($senha === $row['senha']) { // Caso não seja criptografada
                    $_SESSION['id_medico'] = $row['id_medico']; // Store id_medico in session
                    error_log("Login bem-sucedido com senha não criptografada."); // Log de sucesso
                    header("Location: telainicio.php");
                    exit();
                } else {
                    error_log("Senha inválida para COREN/CRM: " . $coren_crm); // Log para depuração
                    $erro = "COREN/CRM ou senha inválidos!";
                }
            } else {
                error_log("COREN/CRM não encontrado: " . $coren_crm); // Log para depuração
                $erro = "COREN/CRM ou senha inválidos!";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/telalogin.css">
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php if ($erro): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <!-- Navbar padronizada -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55">
                <!-- Removido: <a class="navbar-brand fs-4 fw-bold ms-2">DigitalVac</a> -->
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <!-- Apenas login, sem links ativos -->
                </div>
            </div>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px; position: relative;">
            <!-- Botão de Voltar -->
            <a href="javascript:history.back()" class="btn btn-link text-decoration-none text-primary"
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Login Médico</h2>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="coren_crm" class="form-label">COREN ou CRM</label>
                    <input type="text" name="coren_crm" id="coren_crm" class="form-control" placeholder="COREN/CRM-UF 000000"
                        required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha"
                        required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                <div class="text-center mt-3">
                    <a href="../outros/esquecisenha.php" class="text-decoration-none">Esqueceu a senha?</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>