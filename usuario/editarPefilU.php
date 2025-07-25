<?php
session_start();
require_once '../outros/db_connect.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id_usuario'];

$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}
$nome = $user['nome_usuario'];
$telefone = $user['tel_usuario'];
$genero = $user['genero_usuario'];
$email = $user['email_usuario'];
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
    <style>
        .card.shadow-lg {
            background: #FDFDFD;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="telainicioU.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="perfilU.php">
                        <i class="bi bi-person-fill"></i> Perfil
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="carteira_vac.php">
                        <i class="bi bi-postcard-heart-fill"></i> Carteira de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="proxima_vac.php">
                        <i class="bi bi-calendar2-week-fill"></i> Próximas Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Atestados
                    </a>
                </div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger fw-bold px-2 py-1" style="font-size: 15px; min-width: 70px;"
                            href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg p-4 text-center">
                    <h3 class="mt-3"><?php echo htmlspecialchars($nome); ?></h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Editar Perfil</h3>
                    <form id="editarPerfilForm" action="../Recebedados/salvarPerfil.php" method="POST">
                        <div class="mb-3">
                            <label for="telefone" class="form-label"><strong>Telefone:</strong></label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                value="<?php echo htmlspecialchars($telefone); ?>" maxlength="15" inputmode="tel"
                                pattern="\(?\d{2}\)?\s?\d{4,5}-?\d{4}">
                        </div>
                        <div class="mb-3">
                            <label for="genero" class="form-label"><strong>Gênero:</strong></label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="M" <?php echo $genero == 'M' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo $genero == 'F' ? 'selected' : ''; ?>>Feminino</option>
                                <option value="O" <?php echo $genero == 'O' ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>E-mail:</strong></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($email); ?>"
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                        </div>
                        <!-- Campos abaixo de email removidos -->
                        <button type="submit" class="btn btn-primary fw-bold">Salvar Alterações</button>
                        <a href="perfilU.php" class="btn btn-danger fw-bold px-2 py-1"
                            style="font-size: 15px; min-width: 70px;">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Bootstrap -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 1080; top: 80px;">
        <div id="toast-alert" class="toast align-items-center text-bg-primary border-0" role="alert"
            aria-live="assertive" aria-atomic="true" style="min-width:350px; max-width:500px;">
            <div class="d-flex">
                <div class="toast-body" id="toast-alert-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Máscara para telefone brasileiro (celular e fixo)
        new Cleave('#telefone', {
            delimiters: ['(', ') ', '-'],
            blocks: [0, 2, 5, 4],
            numericOnly: true
        });

        // Toast Bootstrap
        function showAlert(type, message) {
            const toastEl = document.getElementById('toast-alert');
            const toastBody = document.getElementById('toast-alert-body');
            toastBody.textContent = message;
            toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-primary');
            if (type === 'success') {
                toastEl.classList.add('text-bg-success');
            } else if (type === 'error') {
                toastEl.classList.add('text-bg-danger');
            } else {
                toastEl.classList.add('text-bg-primary');
            }
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }

        document.getElementById("editarPerfilForm").addEventListener("submit", function (event) {
            event.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json().catch(() => ({ success: false, message: "Erro inesperado do servidor." })))
                .then((data) => {
                    if (data.success) {
                        showAlert('success', data.message || "Alterações salvas com sucesso!");
                        setTimeout(() => { window.location.href = "perfilU.php"; }, 1500);
                    } else {
                        showAlert('error', data.message || "Erro ao salvar alterações.");
                    }
                })
                .catch((error) => {
                    showAlert('error', "Ocorreu um erro ao salvar. Tente novamente.");
                });
        });
    </script>
</body>

</html>