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
$sql = "SELECT a.id_aplica, a.data_aplica, v.id_vaci, v.nome_vaci, v.fabri_vaci, v.lote_vaci, v.via_adimicao, v.n_dose, v.intervalo_dose, v.estoque, v.idade_reco, v.sus, a.dose_aplicad, p.nome_posto
        FROM aplicacao a
        JOIN vacina v ON a.id_vaci = v.id_vaci
        JOIN posto p ON a.id_posto = p.id_posto
        WHERE a.id_usuario = ?
        ORDER BY a.data_aplica DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$result = $stmt2->get_result();

// Agrupa aplicações por vacina para facilitar exibição no modal
$aplicacoes_por_vacina = [];
$vacinas_info = [];
while ($row = $result->fetch_assoc()) {
    $id_vaci = $row['id_vaci'];
    if (!isset($aplicacoes_por_vacina[$id_vaci])) {
        $aplicacoes_por_vacina[$id_vaci] = [];
        $vacinas_info[$id_vaci] = [
            'nome_vaci' => $row['nome_vaci'],
            'fabri_vaci' => $row['fabri_vaci'],
            'lote_vaci' => $row['lote_vaci'],
            'via_adimicao' => $row['via_adimicao'],
            'n_dose' => $row['n_dose'],
            'intervalo_dose' => $row['intervalo_dose'],
            'estoque' => $row['estoque'],
            'idade_reco' => $row['idade_reco'],
            'sus' => $row['sus'],
        ];
    }
    $aplicacoes_por_vacina[$id_vaci][] = [
        'id_aplica' => $row['id_aplica'],
        'data_aplica' => $row['data_aplica'],
        'dose_aplicad' => $row['dose_aplicad'],
        'nome_posto' => $row['nome_posto'],
    ];
}
// Para exibir a tabela normalmente, precisamos reexecutar a query:
$stmt2->execute();
$result = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
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

        @media (max-width: 991.98px) {
            .modal-lg {
                max-width: 98vw;
                margin: 0.5rem;
            }

            .modal-content {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .modal-body .row> [class^="col-"] {
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 767.98px) {
            .modal-lg {
                max-width: 100vw;
                margin: 0;
            }

            .modal-content {
                border-radius: 0 !important;
            }

            .modal-body {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
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
        <h2 class="text-primary fw-bold mb-4" style="text-align: center;">
            Histórico de Vacinação de <?php echo htmlspecialchars($usuario['nome_usuario']); ?>
        </h2>
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
                            $id_vaci = $row['id_vaci'];
                            $modalId = "modalVacina" . $id_vaci;
                        ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['dose_aplicad']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_aplica']))); ?></td>
                                <td><?php echo htmlspecialchars($row['nome_posto']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Ver Informações" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <!-- Modal de informações da vacina e aplicações -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="label<?php echo $modalId; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                          <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                            <h5 class="modal-title fw-bold" id="label<?php echo $modalId; ?>">
                                                <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                                <span class="ms-2">Informações da Vacina</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body px-2 px-md-4 py-3">
                                            <div class="row g-3 g-md-4">
                                                <div class="col-12 col-md-4">
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Nome:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['nome_vaci']); ?></span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-building"></i> Fabricante:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['fabri_vaci']); ?></span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Lote:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['lote_vaci']); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Aplicação:</span><br>
                                                        <span class="fs-6">
                                                        <?php
                                                            $nome = $vacinas_info[$id_vaci]['nome_vaci'];
                                                            $idade_reco = isset($vacinas_info[$id_vaci]['idade_reco']) ? trim($vacinas_info[$id_vaci]['idade_reco']) : '';
                                                            if (
                                                                stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                                            ) {
                                                                echo "50 anos";
                                                            } elseif (
                                                                stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                                            ) {
                                                                echo "10 anos";
                                                            } elseif (
                                                                stripos($nome, 'HPV') !== false
                                                            ) {
                                                                echo "9 anos";
                                                            } elseif (
                                                                stripos($nome, 'Influenza') !== false
                                                            ) {
                                                                echo "9 anos";
                                                            } elseif (
                                                                stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                                            ) {
                                                                echo "18 anos";
                                                            } elseif (
                                                                stripos($nome, 'VSR') !== false ||
                                                                stripos($nome, 'Raiva') !== false ||
                                                                stripos($nome, 'viajantes') !== false
                                                            ) {
                                                                echo "A qualquer momento";
                                                            } else {
                                                                echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                            }
                                                        ?>
                                                        </span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-diagram-3"></i> Via de Administração:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['via_adimicao']); ?></span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Número de Doses:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['n_dose']); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre Doses:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['intervalo_dose']); ?> meses</span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-box-seam"></i> Estoque:</span><br>
                                                        <span class="fs-6"><?php echo htmlspecialchars($vacinas_info[$id_vaci]['estoque']); ?></span>
                                                    </div>
                                                    <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                        <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                        <span class="fs-6"><?php echo ($vacinas_info[$id_vaci]['sus'] == 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-clock-history"></i> Histórico de Aplicações desta Vacina</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center mx-auto mb-0">
                                                    <thead>
                                                        <tr style="background: #0d6efd; color: #fff;">
                                                            <th>Dose</th>
                                                            <th>Data de Aplicação</th>
                                                            <th>Posto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $aplicIndex = 0;
                                                        foreach ($aplicacoes_por_vacina[$id_vaci] as $aplic) {
                                                            echo '<tr>';
                                                            // Dose
                                                            $cellStyle = ($aplicIndex % 2 === 1) ? 'background-color:rgb(204, 202, 202);' : '';
                                                            echo '<td style="' . $cellStyle . '">' . htmlspecialchars($aplic['dose_aplicad']) . '</td>';
                                                            // Data de Aplicação
                                                            $cellStyle = ($aplicIndex % 2 === 1) ? 'background-color: rgb(204, 202, 202);' : '';
                                                            echo '<td style="' . $cellStyle . '">' . htmlspecialchars(date('d/m/Y', strtotime($aplic['data_aplica']))) . '</td>';
                                                            // Posto
                                                            $cellStyle = ($aplicIndex % 2 === 1) ? 'background-color: rgb(204, 202, 202);' : '';
                                                            echo '<td style="' . $cellStyle . '">' . htmlspecialchars($aplic['nome_posto']) . '</td>';
                                                            echo '</tr>';
                                                            $aplicIndex++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
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
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    
</body>

</html>
<?php $conn->close(); ?>