<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

// Campo de pesquisa por nome da vacina
$nome_vacina = '';
if (isset($_GET['nome_vacina'])) {
    $nome_vacina = trim($_GET['nome_vacina']);
}

// Monta a consulta SQL com filtro por nome da vacina, se fornecido
if (!empty($nome_vacina)) {
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque, idade_meses_reco, idade_anos_reco 
            FROM vacina WHERE nome_vaci LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_param = '%' . $nome_vacina . '%';
    $stmt->bind_param('s', $like_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque, idade_meses_reco, idade_anos_reco 
            FROM vacina";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Erro ao buscar vacinas: " . $conn->error);
}

// Após obter $result, crie um array para ordenar pela idade de aplicação
$vacinas = [];
while ($row = $result->fetch_assoc()) {
    // Calcule idade total em meses para ordenação
    $idade_meses = (isset($row['idade_anos_reco']) ? intval($row['idade_anos_reco']) : 0) * 12 +
                   (isset($row['idade_meses_reco']) ? intval($row['idade_meses_reco']) : 0);
    // Para "Ao nascer", garanta que fique no início
    if (($row['id_vaci'] == 22 || $row['id_vaci'] == 23) || ($idade_meses === 0)) {
        $idade_meses = -1;
    }
    $row['idade_total_meses'] = $idade_meses;
    $vacinas[] = $row;
}
// Ordena pelo campo idade_total_meses
usort($vacinas, function($a, $b) {
    return $a['idade_total_meses'] <=> $b['idade_total_meses'];
});
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
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
    </style>
</head>

<body>
    <!-- Navbar padronizada -->
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
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="listavac.php">
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
        <h2 class="text-center text-primary fw-bold">Lista de Vacinas</h2>
        <div class="container-fluid col-md-6 mt-4">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-vacina">
                <input class="form-control me-2 border border-primary" type="search" name="nome_vacina"
                    placeholder="Digite o nome da vacina" value="<?php echo htmlspecialchars($nome_vacina); ?>"
                    id="input-nome-vacina" autocomplete="off">
            </form>
        </div>
        <br>
        <div id="tabela-vacinas" class="d-flex justify-content-center">
            <table class="table table-bordered text-center mx-auto">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Nome</th>
                        <th>Fabricante</th>
                        <th>Lote</th>
                        <th>Idade Aplicação</th>
                        <th>Via de Administração</th>
                        <th>Número de Doses</th>
                        <th>Intervalo entre Doses (meses)</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowIndex = 0;
                    foreach ($vacinas as $row):
                        if ($rowIndex === 0) {
                            $rowClass = 'bg-white';
                        } else {
                            $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                        }
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <!-- <td><?php echo htmlspecialchars($row['id_vaci']); ?></td> -->
                            <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                            <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                            <td><?php echo htmlspecialchars($row['lote_vaci']); ?></td>
                            <td>
                                <?php
                                    // Exibe "Ao nascer" para as duas primeiras vacinas (id_vaci 22 e 23)
                                    if ($row['id_vaci'] == 22 || $row['id_vaci'] == 23) {
                                        echo "Ao nascer";
                                    } else {
                                        $idade_meses = isset($row['idade_meses_reco']) ? intval($row['idade_meses_reco']) : 0;
                                        $idade_anos = isset($row['idade_anos_reco']) ? intval($row['idade_anos_reco']) : 0;
                                        $partes = [];
                                        if ($idade_anos > 0) $partes[] = $idade_anos . " anos";
                                        if ($idade_meses > 0) $partes[] = $idade_meses . " meses";
                                        if (empty($partes)) $partes[] = "-";
                                        echo htmlspecialchars(implode(" / ", $partes));
                                    }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['via_adimicao']); ?></td>
                            <td><?php echo htmlspecialchars($row['n_dose']); ?></td>
                            <td><?php echo htmlspecialchars($row['intervalo_dose']); ?></td>
                            <td><?php echo htmlspecialchars($row['estoque']); ?></td>
                            <td>
                                <a href="ver_vacina.php?id_vaci=<?php echo urlencode($row['id_vaci']); ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-info-circle"></i> Ver informações
                                </a>
                            </td>
                        </tr>
                        <?php $rowIndex++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pesquisa automática AJAX
        document.getElementById('input-nome-vacina').addEventListener('input', function () {
            const nome = this.value;
            const tabela = document.getElementById('tabela-vacinas');
            const params = new URLSearchParams({ nome_vacina: nome });
            fetch('listavac.php?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const novaTabela = temp.querySelector('#tabela-vacinas');
                    if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
                });
        });
    </script>
</body>

</html>