<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove símbolos do CPF
    $senha = $_POST['senha'];

    error_log("Tentativa de login: CPF = $cpf, Senha = $senha"); // Log para depuração

    if (!validarCPF($cpf)) {
        $erro = "CPF inválido!";
    } else {
        $stmt = $conn->prepare("SELECT id_usuario, senha FROM usuario WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Senha armazenada no banco (criptografada): " . $row['senha']); // Log para depuração

            if (password_verify($senha, $row['senha'])) { // Verifica a senha criptografada
                error_log("Login bem-sucedido para CPF: $cpf"); // Log para depuração
                $_SESSION['id_usuario'] = $row['id_usuario'];
                header("Location: telainicioU.php");
                exit();
            } else {
                error_log("Senha inválida para CPF: $cpf"); // Log para depuração
                $erro = "CPF ou senha inválidos!";
            }
        } else {
            error_log("CPF não encontrado: $cpf"); // Log para depuração
            $erro = "CPF ou senha inválidos!";
        }

        $stmt->close();
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
</head>

<body class="bg-light">
    <?php if ($erro): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px; position: relative;">
            <!-- Botão de Voltar -->
            <a href="../index.php" class="btn btn-link text-decoration-none text-primary "
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Login Usuário</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Digite seu CPF" required
                        maxlength="14" inputmode="numeric" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha"
                        required minlength="6" maxlength="20">
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                <div class="text-center mt-3">
                    <a href="../outros/esquecisenhaU.php" class="text-decoration-none">Esqueceu a senha?</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script>
        // Aplica a máscara ao campo CPF
        const cpfField = new Cleave('#cpf', {
            delimiters: ['.', '.', '-'],
            blocks: [3, 3, 3, 2],
            numericOnly: true
        });

        // Máscara para senha: apenas impede colar espaços e limita tamanho
        document.getElementById('senha').addEventListener('input', function () {
            this.value = this.value.replace(/\s/g, '').slice(0, 20);
        });

        // Remove a máscara antes de enviar o formulário
        document.querySelector("form").addEventListener("submit", function (event) {
            const cpfInput = document.getElementById("cpf");
            cpfInput.value = cpfInput.value.replace(/\D/g, ""); // Remove tudo que não for número
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>