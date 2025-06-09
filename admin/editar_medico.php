<?php
include('../outros/db_connect.php');
session_start();


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Médico inválido.";
    exit;
}

// Excluir médico
if (isset($_POST['apagar']) && $_POST['apagar'] === '1') {
    $stmt = $conn->prepare("DELETE FROM medico WHERE id_medico = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: listamedico.php?msg=apagado");
        exit;
    } else {
        $erro = "Erro ao apagar: " . $conn->error;
    }
}

// Atualização dos dados
$sucesso = false;
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['apagar'])) {
    $nome_medico = trim($_POST['nome_medico']);
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $email_medico = trim($_POST['email_medico']);
    $tel_medico = preg_replace('/\D/', '', $_POST['tel_medico']);
    $coren_crm = trim($_POST['coren_crm']);
    $tipo_medico = trim($_POST['tipo_medico']);
    $naci_medico = $_POST['naci_medico'];
    $id_posto = intval($_POST['id_posto']);

    $sql = "UPDATE medico SET 
        nome_medico=?, cpf=?, email_medico=?, tel_medico=?, coren_crm=?, tipo_medico=?, naci_medico=?, id_posto=?
        WHERE id_medico=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssii",
        $nome_medico,
        $cpf,
        $email_medico,
        $tel_medico,
        $coren_crm,
        $tipo_medico,
        $naci_medico,
        $id_posto,
        $id
    );
    if ($stmt->execute()) {
        $sucesso = true;
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Busca os dados atualizados
$stmt = $conn->prepare("SELECT * FROM medico WHERE id_medico = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Médico não encontrado.";
    exit;
}
$medico = $result->fetch_assoc();

// Funções utilitárias
function formatarCPF($cpf)
{
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
}
function formatarTelefone($telefone)
{
    if (strlen($telefone) == 11) {
        return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $telefone);
    } elseif (strlen($telefone) == 10) {
        return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $telefone);
    }
    return $telefone;
}

// Dados do médico
$nome = $medico['nome_medico'];
$cpf = $medico['cpf'];
$email = $medico['email_medico'];
$telefone = $medico['tel_medico'];
$coren_crm = $medico['coren_crm'];
$tipo = $medico['tipo_medico'];
$dataNascimento = $medico['naci_medico'];
$id_posto = $medico['id_posto'];

// Buscar postos para select
$postos = [];
$res = $conn->query("SELECT id_posto, nome_posto FROM posto ORDER BY nome_posto");
while ($row = $res->fetch_assoc()) {
    $postos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
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
                    <a class="nav-link active fs-6 fw-bold" href="telainicio.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastromedico.php">
                        <i class="bi bi-person-plus-fill"></i> Cadastrar Médico
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="listamedico.php">
                        <i class="bi bi-person-lines-fill"></i> Lista de Médicos
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
        <!-- Toast Bootstrap -->
        <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3"
            style="z-index: 1080; top: 80px;">
            <div id="toast-alert" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true" style="min-width:350px; max-width:500px;">
                <div class="d-flex">
                    <div class="toast-body" id="toast-alert-body"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg p-4 text-center">
                    <h3 class="mt-3"><?php echo htmlspecialchars($nome); ?></h3>
                    <a href="listamedico.php" class="btn btn-primary fw-bold mt-3 px-2 py-1"
                        style="font-size: 15px; min-width: 70px;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <form method="post" onsubmit="return confirm('Tem certeza que deseja apagar este médico?');" class="mt-3">
                        <input type="hidden" name="apagar" value="1">
                        <button type="submit" class="btn btn-outline-danger fw-bold">
                            <i class="bi bi-trash"></i> Apagar Médico
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Editar Dados do Médico</h3>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off" id="form-editar-medico">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nome</label>
                                <input type="text" name="nome_medico" class="form-control" required
                                    value="<?php echo htmlspecialchars($nome); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control" required maxlength="14"
                                    value="<?php echo htmlspecialchars(formatarCPF($cpf)); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">E-mail</label>
                                <input type="email" name="email_medico" class="form-control" required
                                    value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telefone</label>
                                <input type="text" name="tel_medico" id="tel_medico" class="form-control" required
                                    maxlength="15" value="<?php echo htmlspecialchars(formatarTelefone($telefone)); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">COREN/CRM</label>
                                <input type="text" name="coren_crm" class="form-control" required
                                    value="<?php echo htmlspecialchars($coren_crm); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipo</label>
                                <input type="text" name="tipo_medico" class="form-control" required
                                    value="<?php echo htmlspecialchars($tipo); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Data de Nascimento</label>
                                <input type="date" name="naci_medico" class="form-control" required
                                    value="<?php echo htmlspecialchars($dataNascimento); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Posto</label>
                                <select name="id_posto" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($postos as $posto): ?>
                                        <option value="<?php echo $posto['id_posto']; ?>" <?php if ($id_posto == $posto['id_posto']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($posto['nome_posto']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary fw-bold">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Máscara para CPF
        new Cleave('#cpf', {
            delimiters: ['.', '.', '-'],
            blocks: [3, 3, 3, 2],
            numericOnly: true
        });

        // Máscara para telefone (celular/fixo)
        new Cleave('#tel_medico', {
            phone: true,
            phoneRegionCode: 'BR'
        });

        // Remove máscara antes de enviar o formulário
        document.getElementById('form-editar-medico').addEventListener('submit', function (e) {
            var cpfInput = document.getElementById('cpf');
            cpfInput.value = cpfInput.value.replace(/\D/g, '');

            var telInput = document.getElementById('tel_medico');
            telInput.value = telInput.value.replace(/\D/g, '');
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
    </script>
    <?php if ($sucesso): ?>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            showAlert('success', 'Dados do médico atualizados com sucesso!');
        });
    </script>
    <?php elseif ($erro): ?>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            showAlert('error', <?php echo json_encode($erro); ?>);
        });
    </script>
    <?php endif; ?>
</body>

</html>
<?php $conn->close(); ?>
