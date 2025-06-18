<?php
session_start();

$id = $_SESSION['id_usuario'];

include('../outros/db_connect.php');

// Pesquisa automatizada
$pesquisa = '';
if (isset($_GET['pesquisa'])) {
    $pesquisa = trim($_GET['pesquisa']);
}

// Filtro de ordenação
$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : 'data_aplica';
$ordem_sql = "a.data_aplica DESC"; // padrão

if ($ordenar_por === 'nome') {
    $ordem_sql = "v.nome_vaci ASC";
} elseif ($ordenar_por === 'doses') {
    $ordem_sql = "v.n_dose DESC";
}

// Consulta com filtro se houver pesquisa
$sql = "SELECT v.id_vaci, v.nome_vaci, v.n_dose, a.dose_aplicad, a.data_aplica, p.nome_posto, m.nome_medico, v.intervalo_dose
        FROM aplicacao a
        JOIN vacina v ON a.id_vaci = v.id_vaci
        JOIN medico m ON a.id_medico = m.id_medico
        JOIN posto p ON a.id_posto = p.id_posto
        WHERE a.id_usuario = ?";
if ($pesquisa !== '') {
    $sql .= " AND v.nome_vaci LIKE ?";
}
$sql .= " ORDER BY $ordem_sql";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

if ($pesquisa !== '') {
    $like = '%' . $pesquisa . '%';
    $stmt->bind_param("is", $id, $like);
} else {
    $stmt->bind_param("i", $id);
}
$stmt->execute();
$result = $stmt->get_result();

// Agrupa doses por vacina
$vacinas = [];
while ($row = $result->fetch_assoc()) {
    $id_vaci = $row['id_vaci'];
    if (!isset($vacinas[$id_vaci])) {
        $vacinas[$id_vaci] = [
            'nome_vaci' => $row['nome_vaci'],
            'n_dose' => $row['n_dose'],
            'intervalo_dose' => $row['intervalo_dose'],
            'doses' => [],
        ];
    }
    $vacinas[$id_vaci]['doses'][] = [
        'dose_aplicad' => $row['dose_aplicad'],
        'data_aplica' => $row['data_aplica'],
        'nome_posto' => $row['nome_posto'],
        'nome_medico' => $row['nome_medico'],
    ];
}

// Função para renderizar apenas a tabela (para AJAX)
function renderTabelaCarteiraVac($vacinas)
{
    ob_start();
    ?>
    <div class="table-responsive">
        <table class="table table-bordered text-center mx-auto tabela-vacinas">
            <thead>
                <tr>
                    <th>Nome da Vacina</th>
                    <th>Doses Tomadas</th>
                    <th>Data(s) de Aplicação</th>
                    <th>Posto</th>
                    <th>Médico</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rowIndex = 0;
                // Ordena as doses de cada vacina pela data de aplicação (mais recente primeiro)
                foreach ($vacinas as &$vacina) {
                    usort($vacina['doses'], function ($a, $b) {
                        // Corrigir para usar 'data_aplica' (não 'data_aplicada')
                        return strtotime($b['data_aplica']) - strtotime($a['data_aplica']);
                    });
                }
                unset($vacina);
                foreach ($vacinas as $vacina):
                    $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                    $doses_tomadas = count($vacina['doses']);
                    $n_dose = $vacina['n_dose'];
                    $datas = [];
                    $postos = [];
                    $medicos = [];
                    foreach ($vacina['doses'] as $dose) {
                        // Corrigir para usar 'data_aplica' (não 'data_aplicada')
                        $datas[] = date('d/m/Y', strtotime($dose['data_aplica']));
                        $postos[] = htmlspecialchars($dose['nome_posto']);
                        $medicos[] = htmlspecialchars($dose['nome_medico']);
                    }
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo htmlspecialchars($vacina['nome_vaci']); ?></td>
                        <td>
                            <?php echo htmlspecialchars("$doses_tomadas/$n_dose"); ?>
                            <?php if ($doses_tomadas >= $n_dose): ?>
                                <span class="badge bg-success">Esquema Completo</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo implode('<br>', $datas); ?></td>
                        <td><?php echo implode('<br>', $postos); ?></td>
                        <td><?php echo implode('<br>', $medicos); ?></td>
                    </tr>
                    <?php $rowIndex++; endforeach; ?>
                <?php if ($rowIndex === 0): ?>
                    <tr>
                        <td colspan="5">Nenhuma aplicação registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

// Se for AJAX, retorna só a tabela
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    echo '<div id="tabela-carteira-vac">';
    echo renderTabelaCarteiraVac($vacinas);
    echo '</div>';
    $conn->close();
    exit;
}
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
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }

        .container {
            max-width: 95%;
        }

        .table {
            background: #FDFDFD;
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

        .img-select {
            opacity: 25%;
        }

        #tabela-carteira-vac {
            width: 100%;
        }

        .dropdown-sort {
            min-width: 180px;
        }

        /* Garante que a tabela mantenha largura fixa */
        .tabela-vacinas {
            width: 100%;
            min-width: 900px; /* ajuste conforme necessário */
            table-layout: fixed;
        }

        /* Garante que o campo de pesquisa não altere largura ao focar */
        #searchInput {
            width: 500px; /* ajuste conforme necessário */
            max-width: 100%;
            transition: none;
            box-sizing: border-box;
        }

        /* Remove qualquer alteração de padding/borda ao focar */
        #searchInput:focus {
            outline: 2px solid #1976d2;
            width: 500px;
            box-sizing: border-box;
        }

        @media (max-width: 800px) {
            .table {
                font-size: 0.95rem;
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
                    <a class="nav-link active fs-6 fw-bold" href="telainicioU.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="perfilU.php">
                        <i class="bi bi-person-fill"></i> Perfil
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="carteira_vac.php">
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

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-start pt-4">
        <h2 class="text-primary fw-bold mb-4 text-center w-100" style="max-width:600px;">
            Aplicações de Vacina
        </h2>
        <div class="w-100 d-flex justify-content-center mb-4" style="max-width:600px;">
            <form class="d-flex position-relative w-100" role="search" id="form-pesquisa-vacina">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-vacina" autocomplete="off" maxlength="50"
                    pattern="[A-Za-zÀ-ÿ\s]+">
            </form>
        </div>
        <div class="d-flex justify-content-end align-items-center mb-2 w-100" style="max-width:1200px;">
            <!-- Removido o dropdown de ordenação -->
        </div>
        <div id="tabela-carteira-vac" class="flex-grow-1 w-100 d-flex flex-column align-items-center" style="max-width:1200px;">
            <div class="w-100">
                <?php echo renderTabelaCarteiraVac($vacinas); ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };

        // Permite apenas letras e espaços no campo de pesquisa
        document.getElementById('pesquisa-vacina').addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0, 50);
        });

        // Pesquisa automática AJAX igual às tabelas da pasta medica
        const inputVacina = document.getElementById('pesquisa-vacina');
        const tabela = document.getElementById('tabela-carteira-vac');

        function atualizarTabelaCarteiraVac() {
            const termo = inputVacina.value;
            // Sempre usa o padrão 'data_aplica'
            fetch('carteira_vac.php?pesquisa=' + encodeURIComponent(termo), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const novaTabela = temp.querySelector('#tabela-carteira-vac');
                    if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
                });
        }

        inputVacina.addEventListener('input', atualizarTabelaCarteiraVac);

        // Mostra todas as vacinas ao focar se o campo estiver vazio
        inputVacina.addEventListener('focus', function () {
            if (!this.value) atualizarTabelaCarteiraVac();
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>