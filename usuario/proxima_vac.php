<?php
session_start();
include('../outros/db_connect.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Busca data de nascimento do usuário
$stmt = $conn->prepare("SELECT naci_usuario FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($naci_usuario);
$stmt->fetch();
$stmt->close();
$naci_usuario = $naci_usuario ?? date('Y-m-d');

// Busca vacinas
$sql = "SELECT id_vaci, nome_vaci, idade_reco, n_dose, intervalo_dose, sus FROM vacina";
$result = $conn->query($sql);
$vacinas = [];
while ($row = $result->fetch_assoc()) {
    $vacinas[] = $row;
}

// Busca aplicações do usuário
$sql_aplic = "SELECT id_vaci, MAX(data_aplica) as ultima_data, MAX(dose_aplicad) as ultima_dose, COUNT(*) as total_doses
              FROM aplicacao
              WHERE id_usuario = ?
              GROUP BY id_vaci";
$stmt = $conn->prepare($sql_aplic);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res_aplic = $stmt->get_result();
$aplicacoes = [];
while ($row = $res_aplic->fetch_assoc()) {
    $aplicacoes[$row['id_vaci']] = $row;
}
$stmt->close();

// Função para calcular próxima dose
function calcularProximaDose($vacina, $aplicacao, $naci_usuario) {
    $idade_reco = trim($vacina['idade_reco']);
    $intervalo = intval($vacina['intervalo_dose']);
    $n_dose = intval($vacina['n_dose']);
    if (!$aplicacao) {
        if (preg_match('/(\d+)\s*mes/i', $idade_reco, $m)) {
            $meses = intval($m[1]);
            $data = date('Y-m-d', strtotime("+$meses months", strtotime($naci_usuario)));
            return $data;
        } elseif (preg_match('/(\d+)\s*ano/i', $idade_reco, $m)) {
            $anos = intval($m[1]);
            $data = date('Y-m-d', strtotime("+$anos years", strtotime($naci_usuario)));
            return $data;
        } else {
            return $naci_usuario;
        }
    } else {
        $ultima_data = $aplicacao['ultima_data'];
        $ultima_dose = intval($aplicacao['ultima_dose']);
        if ($ultima_dose >= $n_dose) {
            return "Esquema completo";
        }
        if ($intervalo > 0) {
            $data = date('Y-m-d', strtotime("+$intervalo months", strtotime($ultima_data)));
            return $data;
        } else {
            return "Consultar profissional";
        }
    }
}

function formatarIdade($idade_reco) {
    if (preg_match('/(\d+)\s*mes/i', $idade_reco, $m)) {
        if (intval($m[1]) === 0) return "Ao nascer";
        return $m[1] . " meses";
    } elseif (preg_match('/(\d+)\s*ano/i', $idade_reco, $m)) {
        return $m[1] . " anos";
    } else {
        return $idade_reco;
    }
}

// Ordenação por idade recomendada
function ordenarIdadeRecomendada($a, $b) {
    $getOrder = function($idade) {
        $idade = trim(mb_strtolower($idade));
        if ($idade === 'a qualquer momento') return [0, 0];
        if ($idade === 'ao nascer' || $idade === '0 meses' || $idade === '0 mes') return [1, 0];
        if (preg_match('/(\d+)\s*mes/i', $idade, $m)) return [2, intval($m[1])];
        if (preg_match('/(\d+)\s*ano/i', $idade, $m)) return [3, intval($m[1])];
        return [4, 999];
    };
    $ordA = $getOrder($a['idade_reco']);
    $ordB = $getOrder($b['idade_reco']);
    if ($ordA[0] !== $ordB[0]) return $ordA[0] - $ordB[0];
    return $ordA[1] - $ordB[1];
}

// AJAX: retorna só a tabela se for requisição AJAX
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    $pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
    $vacinas_filtradas = [];
    foreach ($vacinas as $vacina) {
        if ($pesquisa === '' || stripos($vacina['nome_vaci'], $pesquisa) !== false) {
            $vacinas_filtradas[] = $vacina;
        }
    }
    usort($vacinas_filtradas, 'ordenarIdadeRecomendada');
    $vacinas_obrigatorias = [];
    $vacinas_opcionais = [];
    foreach ($vacinas_filtradas as $vacina) {
        if (isset($vacina['sus']) && intval($vacina['sus']) === 1) {
            $vacinas_obrigatorias[] = $vacina;
        } else {
            $vacinas_opcionais[] = $vacina;
        }
    }
    ?>
    <div id="tabela-proximas-vacinas" class="flex-grow-1 w-100 d-flex flex-column align-items-center" style="max-width:1200px;">
        <div class="w-100">
        <?php
        $grupos = [
            ['Vacinas Obrigatórias (SUS)', $vacinas_obrigatorias, 'primary', 'bi-shield-check'],
            ['Vacinas Opcionais', $vacinas_opcionais, 'warning', 'bi-patch-question']
        ];
        foreach ($grupos as [$titulo, $grupo, $cor, $icone]):
        if (count($grupo) > 0): ?>
            <div class="border border-<?= $cor ?> rounded-3 mb-4 p-2 shadow-sm w-100" style="background-color: <?= $cor === 'primary' ? '#eaf4ff' : '#fffbe6' ?>;">
                <h5 class="text-<?= $cor ?> text-center mb-2 fw-bold">
                    <i class="bi <?= $icone ?>"></i> <?= $titulo ?>
                </h5>
                <table class="table table-bordered text-center mx-auto">
                    <thead>
                        <tr>
                            <th style="background-color: #0d6efd; color: white;">Vacina</th>
                            <th style="background-color: #0d6efd; color: white;">Idade Recomendada</th>
                            <th style="background-color: #0d6efd; color: white;">Próxima Dose</th>
                            <th style="background-color: #0d6efd; color: white;">Doses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowIndex = 0; foreach ($grupo as $vacina):
                            $id_vaci = $vacina['id_vaci'];
                            $aplic = $aplicacoes[$id_vaci] ?? null;
                            $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                            $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                            $n_dose = intval($vacina['n_dose']);
                            $rowClass = ($rowIndex % 2 === 0) ? 'bg-white' : 'table-secondary';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                            <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                            <td>
                                <?php
                                if ($proxima_dose === "Esquema completo") {
                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                } elseif ($proxima_dose === "Consultar profissional") {
                                    echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                    echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                } else {
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                }
                                ?>
                            </td>
                            <td><?= $doses_tomadas . " / " . $n_dose ?></td>
                        </tr>
                        <?php $rowIndex++; endforeach; ?>
                        <?php if (count($grupo) === 0): ?>
                            <tr><td colspan="4">Nenhuma vacina encontrada.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; endforeach; ?>
        </div>
    </div>
    <?php exit; } ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
        .modal-header {
            background: #0d6efd;
            color: #fff;
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
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="proxima_vac.php">
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
    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-start pt-4">
        <h2 class="text-primary fw-bold mb-4 text-center w-100" style="max-width:600px;">
            Próximas Vacinas
        </h2>
        <div class="w-100 d-flex justify-content-center mb-4" style="max-width:600px; margin-top: 0;">
            <form class="d-flex justify-content-center w-100" role="search" id="form-pesquisa-vacina">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-vacina" autocomplete="off" maxlength="50"
                    pattern="[A-Za-zÀ-ÿ\s]+">
            </form>
        </div>
        <div id="tabela-proximas-vacinas" class="flex-grow-1 w-100 d-flex flex-column align-items-center" style="max-width:1200px;">
            <div class="w-100">
                <?php
                $vacinas_filtradas = $vacinas;
                usort($vacinas_filtradas, 'ordenarIdadeRecomendada');
                $vacinas_obrigatorias = [];
                $vacinas_opcionais = [];
                foreach ($vacinas_filtradas as $vacina) {
                    if (isset($vacina['sus']) && intval($vacina['sus']) === 1) {
                        $vacinas_obrigatorias[] = $vacina;
                    } else {
                        $vacinas_opcionais[] = $vacina;
                    }
                }
                // Sempre renderiza uma embaixo da outra
                $grupos = [
                    ['Vacinas Obrigatórias (SUS)', $vacinas_obrigatorias, 'primary', 'bi-shield-check'],
                    ['Vacinas Opcionais', $vacinas_opcionais, 'warning', 'bi-patch-question']
                ];
                foreach ($grupos as [$titulo, $grupo, $cor, $icone]):
                if (count($grupo) > 0): ?>
                <div class="border border-<?= $cor ?> rounded-3 mb-4 p-2 shadow-sm w-100" style="background-color: <?= $cor === 'primary' ? '#eaf4ff' : '#fffbe6' ?>;">
                    <h5 class="text-<?= $cor ?> text-center mb-2 fw-bold">
                        <i class="bi <?= $icone ?>"></i> <?= $titulo ?>
                    </h5>
                    <table class="table table-bordered text-center mx-auto">
                        <thead>
                            <tr>
                                <th style="background-color: #0d6efd; color: white;">Vacina</th>
                                <th style="background-color: #0d6efd; color: white;">Idade Recomendada</th>
                                <th style="background-color: #0d6efd; color: white;">Próxima Dose</th>
                                <th style="background-color: #0d6efd; color: white;">Doses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rowIndex = 0; foreach ($grupo as $vacina):
                                $id_vaci = $vacina['id_vaci'];
                                $aplic = $aplicacoes[$id_vaci] ?? null;
                                $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                                $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                                $n_dose = intval($vacina['n_dose']);
                                $rowClass = ($rowIndex % 2 === 0) ? 'bg-white' : 'table-secondary';
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                                <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                                <td>
                                    <?php
                                    if ($proxima_dose === "Esquema completo") {
                                        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                    } elseif ($proxima_dose === "Consultar profissional") {
                                        echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                    } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                        echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                        echo date('d/m/Y', strtotime($proxima_dose));
                                    } else {
                                        echo date('d/m/Y', strtotime($proxima_dose));
                                    }
                                    ?>
                                </td>
                                <td><?= $doses_tomadas . " / " . $n_dose ?></td>
                            </tr>
                            <?php $rowIndex++; endforeach; ?>
                            <?php if (count($grupo) === 0): ?>
                                <tr><td colspan="4">Nenhuma vacina encontrada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('pesquisa-vacina').addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0, 50);
        });
        const inputVacina = document.getElementById('pesquisa-vacina');
        const tabela = document.getElementById('tabela-proximas-vacinas');
        function atualizarTabelaProximasVacinas() {
            const termo = inputVacina.value;
            fetch('proxima_vac.php?pesquisa=' + encodeURIComponent(termo), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const novaTabela = temp.querySelector('#tabela-proximas-vacinas');
                    if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
                });
        }
        inputVacina.addEventListener('input', atualizarTabelaProximasVacinas);
        inputVacina.addEventListener('focus', function () {
            if (!this.value) atualizarTabelaProximasVacinas();
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>