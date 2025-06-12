<?php
include('../outros/db_connect.php');
session_start();

// Validação do parâmetro
if (!isset($_GET['id_vaci']) || !is_numeric($_GET['id_vaci'])) {
    die('ID da vacina inválido.');
}
$id_vaci = intval($_GET['id_vaci']);

// Busca os dados atuais da vacina
$stmt = $conn->prepare("SELECT * FROM vacina WHERE id_vaci = ?");
$stmt->bind_param('i', $id_vaci);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('Vacina não encontrada.');
}
$vacina = $result->fetch_assoc();

// Atualização dos dados
$sucesso = false;
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_vaci = trim($_POST['nome_vaci']);
    $fabri_vaci = trim($_POST['fabri_vaci']);
    $lote_vaci = trim($_POST['lote_vaci']);
    $idade_reco = trim($_POST['idade_reco']);
    $via_adimicao = trim($_POST['via_adimicao']);
    $n_dose = intval($_POST['n_dose']);
    $intervalo_dose = intval($_POST['intervalo_dose']);
    $estoque = intval($_POST['estoque']);

    $stmt = $conn->prepare("UPDATE vacina SET nome_vaci=?, fabri_vaci=?, lote_vaci=?, idade_reco=?, via_adimicao=?, n_dose=?, intervalo_dose=?, estoque=? WHERE id_vaci=?");
    $stmt->bind_param(
        'ssssiiiii',
        $nome_vaci,
        $fabri_vaci,
        $lote_vaci,
        $idade_reco,
        $via_adimicao,
        $n_dose,
        $intervalo_dose,
        $estoque,
        $id_vaci
    );
    if ($stmt->execute()) {
        $sucesso = true;
        // Atualiza os dados exibidos após salvar
        $vacina = [
            'nome_vaci' => $nome_vaci,
            'fabri_vaci' => $fabri_vaci,
            'lote_vaci' => $lote_vaci,
            'idade_reco' => $idade_reco,
            'via_adimicao' => $via_adimicao,
            'n_dose' => $n_dose,
            'intervalo_dose' => $intervalo_dose,
            'estoque' => $estoque
        ];
    } else {
        $erro = "Erro ao atualizar vacina: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Vacina</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
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
            <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3" />
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.php">
                        <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px"></i>
                        Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill" style="font-size: 20px"></i>
                        Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" aria-disabled="true" href="cadastrovac.html">
                        <i class="bi bi-capsule" style="font-size: 20px"></i> Cadastrar
                        Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroenf.php">
                        <i class="bi bi-person-badge" style="font-size: 20px"></i>
                        Cadastrar Enfermeiro
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroposto.html">
                        <i class="bi bi-building-fill-add" style="font-size: 20px"></i>
                        Cadastrar Posto
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastrocampanha.html">
                        <i class="bi bi-megaphone-fill" style="font-size: 20px;"></i>
                        Cadastrar Campanha
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="listamedico.php">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 20px"></i>
                        Listar Medicos
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" href="listavac.php">
                        <i class="bi bi-list" style="font-size: 20px"></i>
                        Listar Vacinas
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
                    <h3 class="mt-3"><?php echo htmlspecialchars($vacina['nome_vaci']); ?></h3>
                    <a href="listavac.php" class="btn btn-primary fw-bold mt-3 px-2 py-1"
                        style="font-size: 15px; min-width: 70px;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Editar Dados da Vacina</h3>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off" id="form-editar-vacina">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nome</label>
                                <input type="text" name="nome_vaci" class="form-control" required
                                    value="<?php echo htmlspecialchars($vacina['nome_vaci']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fabricante</label>
                                <input type="text" name="fabri_vaci" class="form-control" required
                                    value="<?php echo htmlspecialchars($vacina['fabri_vaci']); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lote</label>
                                <input type="text" name="lote_vaci" class="form-control" required
                                    value="<?php echo htmlspecialchars($vacina['lote_vaci']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Via de Administração</label>
                                <input type="text" name="via_adimicao" class="form-control" required
                                    value="<?php echo htmlspecialchars($vacina['via_adimicao']); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número de Doses</label>
                                <input type="number" name="n_dose" class="form-control" min="0" required
                                    value="<?php echo htmlspecialchars($vacina['n_dose']); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Intervalo entre Doses (meses)</label>
                                <input type="number" name="intervalo_dose" class="form-control" min="0" required
                                    value="<?php echo htmlspecialchars($vacina['intervalo_dose']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Estoque</label>
                                <input type="number" name="estoque" class="form-control" min="0" required
                                    value="<?php echo htmlspecialchars($vacina['estoque']); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Idade Recomendada (ex: "2 meses", "1 ano", "Ao nascer")</label>
                                <input type="text" name="idade_reco" class="form-control" required
                                    value="<?php echo htmlspecialchars($vacina['idade_reco'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success fw-bold">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
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
                showAlert('success', 'Dados da vacina atualizados com sucesso!');
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
