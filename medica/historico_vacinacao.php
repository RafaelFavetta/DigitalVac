<?php
include('../outros/db_connect.php');
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Usuário inválido.";
    exit;
}

// Busca nome do usuário
$stmt = $conn->prepare("SELECT nome_usuario FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "Usuário não encontrado.";
    exit;
}
$usuario = $res->fetch_assoc();

// Busca histórico de vacinação (inclui id_aplica para o botão)
$sql = "SELECT a.id_aplica, a.data_aplica, v.nome_vaci, v.fabri_vaci, a.dose_aplicad, p.nome_posto
        FROM aplicacao a
        JOIN vacina v ON a.id_vaci = v.id_vaci
        JOIN posto p ON a.id_posto = p.id_posto
        WHERE a.id_usuario = ?
        ORDER BY a.data_aplica DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$result = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table thead th {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .table td,
        .table th {
            background-color: transparent;
        }

        .btn-info {
            min-width: 40px;
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
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.php">
                        <i class="bi bi-clipboard2-heart-fill"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="listavac.php">
                        <i class="bi bi-list"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.php">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Meus Atestados
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
        <h2 class="text-primary fw-bold mb-4">Histórico de Vacinação de
            <?php echo htmlspecialchars($usuario['nome_usuario']); ?></h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered text-center mx-auto">
                    <thead>
                        <tr>
                            <th>Vacina</th>
                            <th>Fabricante</th>
                            <th>Dose Aplicada</th>
                            <th>Data de Aplicação</th>
                            <th>Posto</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rowIndex = 0;
                        while ($row = $result->fetch_assoc()):
                            $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['dose_aplicad']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_aplica']))); ?></td>
                                <td><?php echo htmlspecialchars($row['nome_posto']); ?></td>
                                <td>
                                    <a href="ver_vacina.php?id_aplica=<?php echo $row['id_aplica']; ?>"
                                        class="btn btn-sm btn-info" title="Ver Informações">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $rowIndex++; endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Nenhuma vacinação registrada para este usuário.</div>
        <?php endif; ?>
        <a href="pesquisa_paciente.php" class="btn btn-secondary">Voltar</a>
    </div>
</body>

</html>
<?php $conn->close(); ?>