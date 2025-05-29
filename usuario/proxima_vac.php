<?php
session_start();
require_once '../outros/db_connect.php'; // Ajuste o caminho para o arquivo de conexão com o banco

// Obter o ID do usuário da sessão
$user_id = $_SESSION['id_usuario'];

// Buscar a data de nascimento do usuário
$query_user = "SELECT naci_usuario FROM usuario WHERE id_usuario = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();

$vacinas_nao_tomadas = [];
if ($user_data) {
    $data_nascimento = $user_data['naci_usuario'];
    $idade = date_diff(date_create($data_nascimento), date_create('today'))->y;

    // Buscar vacinas dentro ou acima da faixa etária
    $query_vacinas = "
        SELECT v.id_vaci, v.nome_vaci, v.idade_aplica, v.n_dose
        FROM vacina v
        WHERE v.idade_aplica <= ?
    ";
    $stmt_vacinas = $conn->prepare($query_vacinas);
    $stmt_vacinas->bind_param("i", $idade);
    $stmt_vacinas->execute();
    $result_vacinas = $stmt_vacinas->get_result();

    $vacinas_disponiveis = [];
    while ($row = $result_vacinas->fetch_assoc()) {
        $vacinas_disponiveis[] = $row;
    }

    // Buscar vacinas já aplicadas ao usuário
    $query_aplicadas = "
        SELECT a.id_vaci
        FROM aplicacao a
        WHERE a.id_usuario = ?
    ";
    $stmt_aplicadas = $conn->prepare($query_aplicadas);
    $stmt_aplicadas->bind_param("i", $user_id);
    $stmt_aplicadas->execute();
    $result_aplicadas = $stmt_aplicadas->get_result();

    $vacinas_aplicadas = [];
    while ($row = $result_aplicadas->fetch_assoc()) {
        $vacinas_aplicadas[] = $row['id_vaci'];
    }

    // Filtro por pesquisa (AJAX)
    $pesquisa = '';
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
        isset($_GET['pesquisa'])
    ) {
        $pesquisa = trim($_GET['pesquisa']);
    }

    // Filtrar vacinas que o usuário ainda não tomou e, se pesquisa, filtrar pelo nome
    $vacinas_nao_tomadas = array_filter($vacinas_disponiveis, function ($vacina) use ($vacinas_aplicadas, $pesquisa) {
        $nao_tomou = !in_array($vacina['id_vaci'], $vacinas_aplicadas);
        if ($pesquisa !== '') {
            return $nao_tomou && (stripos($vacina['nome_vaci'], $pesquisa) !== false);
        }
        return $nao_tomou;
    });
}

// Se for AJAX, retorna só o <tbody>
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    ?>
    <tbody>
        <?php if (!empty($vacinas_nao_tomadas)): ?>
            <?php
            $rowIndex = 0;
            foreach ($vacinas_nao_tomadas as $vacina):
                $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
            ?>
                <tr class="<?php echo $rowClass; ?>">
                    <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                    <td><?= $vacina['idade_aplica'] ?> anos</td>
                    <td><?= $vacina['n_dose'] ?></td>
                </tr>
            <?php $rowIndex++; endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Nenhuma vacina pendente.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <?php
    exit;
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
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            min-width: 700px;
            /* Defina o min-width apenas aqui */
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

        /* Remove o X nativo do input type search no Chrome/Edge */
        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
        }

        /* Remove o X nativo do input type search no Firefox */
        input[type="search"]::-ms-clear {
            display: none;
            width: 0;
            height: 0;
        }

        /* Remove o X nativo do input type search no IE */
        input[type="search"]::-ms-reveal {
            display: none;
            width: 0;
            height: 0;
        }

        @media (max-width: 800px) {
            .table {
                min-width: 0;
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="telainicioU.php">
                        <i class="bi bi-house-fill"></i> Inicio
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
                        <a class="btn btn-danger fw-bold px-2 py-1" style="font-size: 15px; min-width: 70px;" href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center text-primary fw-bold">Vacinas a serem aplicadas</h2>
        <div class="w-100 d-flex justify-content-center">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-proxima-vacina" style="max-width:600px; width:100%;">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-proxima-vacina" autocomplete="off" maxlength="50" pattern="[A-Za-zÀ-ÿ\s]+">
            </form>
        </div>
        <br>
        <div class="table-responsive d-flex justify-content-center" id="tabela-proxima-vacina">
            <div style="width: 100%;">
                <table class="table table-bordered text-center w-100 mx-auto">
                    <thead>
                        <tr>
                            <th>Vacina</th>
                            <th>Idade Recomendada</th>
                            <th>Doses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vacinas_nao_tomadas)): ?>
                            <?php
                            $rowIndex = 0;
                            foreach ($vacinas_nao_tomadas as $vacina):
                                // Primeira linha branca, depois alterna entre cinza e branco
                                if ($rowIndex === 0) {
                                    $rowClass = 'bg-white';
                                } else {
                                    $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                                }
                            ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                                    <td><?= $vacina['idade_aplica'] ?> anos</td>
                                    <td><?= $vacina['n_dose'] ?></td>
                                </tr>
                            <?php $rowIndex++; endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Nenhuma vacina pendente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-center mt-4">
            <a href="https://www.gov.br/saude/pt-br/vacinacao/calendario" target="_blank" class="link">Acesse a lista de
                vacinas</a>
        </p>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Pesquisa automática AJAX para vacinas a serem aplicadas
    document.getElementById('pesquisa-proxima-vacina').addEventListener('input', function () {
        // Permite apenas letras e espaços no campo de pesquisa
        this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0, 50);
        const termo = this.value;
        fetch('proxima_vac.php?pesquisa=' + encodeURIComponent(termo), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                // Atualiza apenas o <tbody>
                const tbody = document.querySelector('#tabela-proxima-vacina tbody');
                if (tbody) {
                    tbody.outerHTML = html;
                }
            });
    });
</script>
</html>