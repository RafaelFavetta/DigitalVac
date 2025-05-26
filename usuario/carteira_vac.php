<?php
session_start();

$id = $_SESSION['id_usuario'];

include('../outros/db_connect.php');

// Pesquisa automatizada
$pesquisa = '';
if (isset($_GET['pesquisa'])) {
    $pesquisa = trim($_GET['pesquisa']);
}

// Consulta com filtro se houver pesquisa
$sql = "SELECT v.nome_vaci, a.dose_aplicad AS dose_vaci, a.data_aplica, p.nome_posto, m.nome_medico
        FROM aplicacao a
        JOIN vacina v ON a.id_vaci = v.id_vaci
        JOIN medico m ON a.id_medico = m.id_medico
        JOIN posto p ON a.id_posto = p.id_posto
        WHERE a.id_usuario = ?";
if ($pesquisa !== '') {
    $sql .= " AND v.nome_vaci LIKE ?";
}

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

// Função para renderizar apenas a tabela (para AJAX)
function renderTabelaCarteiraVac($result)
{
    ob_start();
    ?>
    <div class="table-responsive">
        <table class="table table-bordered text-center mx-auto">
            <thead>
                <tr>
                    <th>Nome da Vacina</th>
                    <th>Dose</th>
                    <th>Data de Aplicação</th>
                    <th>Posto</th>
                    <th>Médico</th>
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
                        <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                        <td><?php echo htmlspecialchars($row['dose_vaci']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_aplica']))); ?></td>
                        <td><?php echo htmlspecialchars($row['nome_posto']); ?></td>
                        <td><?php echo htmlspecialchars($row['nome_medico']); ?></td>
                    </tr>
                <?php $rowIndex++; endwhile; ?>
                <?php if ($rowIndex === 0): ?>
                    <tr><td colspan="5">Nenhuma aplicação registrada.</td></tr>
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
    echo renderTabelaCarteiraVac($result);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .container {
            max-width: 95%;
        }

        .table-responsive {
            /* min-width: 700px; */ /* Removido para padrão Bootstrap */
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

        .img-select {
            opacity: 25%;
        }

        #tabela-carteira-vac {
            width: 100%;
        }

        @media (max-width: 800px) {
            /* Não altere min-width para manter o tamanho fixo */
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
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55">
                <a class="navbar-brand fs-4 fw-bold ms-2">DigitalVac</a>
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
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="btn btn-danger fw-bold" href="../outros/sair.php">
                                <i class="bi bi-box-arrow-right" style="font-size: 20px;"></i> Sair</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center text-primary fw-bold">Aplicações de Vacina</h2>
        <div class="w-100 d-flex justify-content-center">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-vacina" style="max-width:600px; width:100%;">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-vacina" autocomplete="off">
            </form>
        </div>
        <br>
        <div id="tabela-carteira-vac">
            <div style="width: 100%;">
                <?php echo renderTabelaCarteiraVac($result); ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };

        // Pesquisa automática AJAX igual às tabelas da pasta medica
        const inputVacina = document.getElementById('pesquisa-vacina');
        const tabela = document.getElementById('tabela-carteira-vac');

        function atualizarTabelaCarteiraVac() {
            const termo = inputVacina.value;
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