<?php
session_start();
if (!isset($_SESSION['cargo'])) {
    header("Location: esquecisenha.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('db_connect.php');

    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $cargo = $_SESSION['cargo'];

    if (empty($nova_senha) || empty($confirmar_senha)) {
        $mensagem = "Preencha todos os campos.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem.";
    } else {
        $nova_senha_hashed = password_hash($nova_senha, PASSWORD_DEFAULT);

        if ($cargo === 'usuario' && isset($_SESSION['cpf'])) {
            $cpf = $_SESSION['cpf'];
            $update_sql = "UPDATE usuario SET senha = ? WHERE cpf = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $nova_senha_hashed, $cpf);
        } elseif ($cargo === 'medico' && isset($_SESSION['coren_crm'])) {
            $coren_crm = $_SESSION['coren_crm'];
            $update_sql = "UPDATE medico SET senha = ? WHERE coren_crm = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $nova_senha_hashed, $coren_crm);
        } else {
            $mensagem = "Sessão inválida. Tente novamente.";
            $conn->close();
            session_destroy();
            header("Location: ../index.php");
            exit;
        }

        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            $mensagem = "Senha redefinida com sucesso!";
            $conn->close();
            session_destroy();
            if ($cargo === 'medico') {
                header("Location: ../medica/login.php");
            } else {
                header("Location: ../usuario/login.php");
            }
            exit;
        } else {
            $mensagem = "Erro ao atualizar a senha ou nenhuma mudança feita.";
        }

        $conn->close();
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="telalogin.css">
</head>

<body class="bg-light">
    <?php if (isset($mensagem) && $mensagem): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 350px; position: relative;"> <!-- Ajustado para 350px -->
            <!-- Botão de Voltar -->
            <a href="javascript:history.back()" class="btn btn-link text-decoration-none text-primary"
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Alterar Senha</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control"
                        placeholder="Nova senha" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control"
                        placeholder="Confirme a senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>