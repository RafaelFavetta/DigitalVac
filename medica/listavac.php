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
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque 
            FROM vacina WHERE nome_vaci LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_param = '%' . $nome_vacina . '%';
    $stmt->bind_param('s', $like_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque 
            FROM vacina";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Erro ao buscar vacinas: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Vacinas</title>
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
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55">
            <a class="navbar-brand fs-4 fw-bold px-3">DigitalVac</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="telainicio.php">
                        <i class="bi bi-house-fill" style="font-size: 20px;"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.html">
                        <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px;"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill" style="font-size: 20px;"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="listavac.php">
                        <i class="bi bi-list" style="font-size: 20px;"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill" style="font-size: 20px;"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.html">
                        <i class="bi bi-clipboard2-plus-fill" style="font-size: 20px;"></i> Cadastrar Atestado
                    </a>
                </div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger fw-bold" href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 20px;"></i> Sair
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
                <input class="form-control me-2 border border-primary fw-bold" type="text" name="nome_vacina"
                    placeholder="Digite o nome da vacina"
                    value="<?php echo htmlspecialchars($nome_vacina); ?>" id="input-nome-vacina" autocomplete="off">
                <button type="button" id="limpar-pesquisa-vacina" class="btn position-absolute end-0 top-50 translate-middle-y me-2"
                    style="z-index:2; background:transparent; border:none; color:#888; font-size:1.3rem; right:0.5rem; display:none;"
                    tabindex="-1">&times;</button>
            </form>
        </div>
        <br>
        <div id="tabela-vacinas">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Fabricante</th>
                    <th>Lote</th>
                    <th>Idade Aplicação</th>
                    <th>Via de Administração</th>
                    <th>Número de Doses</th>
                    <th>Intervalo entre Doses</th>
                    <th>Estoque</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rowIndex = 0;
                while ($row = $result->fetch_assoc()):
                    if ($rowIndex === 0) {
                        $rowClass = 'bg-white';
                    } else {
                        $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                    }
                ?>
                <tr class="<?php echo $rowClass; ?>">
                    <td><?php echo htmlspecialchars($row['id_vaci']); ?></td>
                    <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                    <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                    <td><?php echo htmlspecialchars($row['lote_vaci']); ?></td>
                    <td><?php echo htmlspecialchars($row['idade_aplica']); ?></td>
                    <td><?php echo htmlspecialchars($row['via_adimicao']); ?></td>
                    <td><?php echo htmlspecialchars($row['n_dose']); ?></td>
                    <td><?php echo htmlspecialchars($row['intervalo_dose']); ?></td>
                    <td><?php echo htmlspecialchars($row['estoque']); ?></td>
                </tr>
                <?php $rowIndex++; endwhile; ?>
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
                // Extrai apenas a tabela da resposta
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const novaTabela = temp.querySelector('#tabela-vacinas');
                if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
            });
        document.getElementById('limpar-pesquisa-vacina').style.display = nome ? 'block' : 'none';
    });

    // Botão X para limpar pesquisa
    document.getElementById('limpar-pesquisa-vacina').addEventListener('click', function () {
        const input = document.getElementById('input-nome-vacina');
        input.value = '';
        input.dispatchEvent(new Event('input'));
        this.style.display = 'none';
        input.focus();
    });

    // Exibe o botão X se já houver texto ao carregar
    window.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('input-nome-vacina');
        document.getElementById('limpar-pesquisa-vacina').style.display = input.value ? 'block' : 'none';
    });
    </script>
</body>
</html>