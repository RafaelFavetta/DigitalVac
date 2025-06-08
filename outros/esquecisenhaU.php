<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('db_connect.php');

    $cpf = $_POST['cpf'] ?? '';
    $cpf = preg_replace('/\D/', '', $cpf);

    if (empty($cpf)) {
        $mensagem = "Por favor, informe o CPF.";
    } else {
        $sql = "SELECT id_usuario FROM usuario WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            session_start();
            $_SESSION['cpf'] = $cpf;
            $_SESSION['cargo'] = 'usuario';
            header("Location: alterar_senha.php");
            exit;
        } else {
            $mensagem = "CPF não encontrado.";
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
            <h2 class="text-center mb-4 text-primary fw-bold">Esqueci a Senha (Usuário)</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="cpf" class="form-label">Digite seu CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="CPF" required maxlength="14">
                </div>
                <button type="submit" class="btn btn-primary w-100">Verificar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
        });
    </script>
</body>
</html>
