<?php
session_start();
require_once '../outros/db_connect.php';

// Verifica se o ID do médico está na sessão
if (!isset($_SESSION['id_medico'])) {
    die("Usuário não autenticado.");
}

$id_medico = $_SESSION['id_medico'];

// Filtro de ordem
$ordem = (isset($_GET['ordem']) && $_GET['ordem'] === 'asc') ? 'asc' : 'desc';
$ordem_sql = ($ordem === 'asc') ? 'ASC' : 'DESC';

// Consulta para buscar os atestados cadastrados pelo médico logado
$sql = "SELECT 
            a.id_atestado, 
            u.nome_usuario AS nome_paciente, 
            m.nome_medico AS medico_responsavel, 
            a.data_inicio AS data_emissao, 
            a.justificativa, 
            DATEDIFF(a.data_fim, a.data_inicio) AS periodo_afastamento 
        FROM atestado a
        JOIN usuario u ON a.id_paci = u.id_usuario
        JOIN medico m ON a.id_medico = m.id_medico
        WHERE a.id_medico = ?
        ORDER BY a.data_inicio $ordem_sql";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_medico);
$stmt->execute();
$result = $stmt->get_result();
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

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 16px 20px !important;
        }

        @media (min-width: 576px) {
            .card {
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }
        }

        .card p {
            margin-bottom: 6px;
        }

        .card .btn {
            padding: 4px 16px;
            font-size: 1rem;
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5em;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 8px 22px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(13, 110, 253, 0.08);
            transition: background 0.2s, box-shadow 0.2s;
            margin-bottom: 18px;
        }

        .filter-btn:hover,
        .filter-btn:focus {
            background: #084298;
            color: #fff;
            box-shadow: 0 4px 16px rgba(13, 110, 253, 0.18);
            outline: none;
            text-decoration: none;
        }

        .filter-btn i {
            font-size: 1.2em;
        }

        .filter-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 0;
            border-bottom: none !important;
            /* Remove qualquer linha inferior */
        }

        /* Remove linha inferior padrão do Bootstrap para .filter-bar se houver */
        .filter-bar,
        .filter-bar+hr,
        .filter-bar:after {
            border-bottom: none !important;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
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
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.html">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="atestado_medico.php">
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

    <!-- Conteúdo Principal -->
    <div class="container mt-4" style="margin-bottom: 40px;">
        <h2 class="text-center text-primary fw-bold">Atestados Cadastrados</h2>
        <div class="filter-bar">
            <a href="?ordem=<?php echo $ordem === 'desc' ? 'asc' : 'desc'; ?>" class="filter-btn"
                title="Alterar ordem dos atestados">
                <i class="bi <?php echo $ordem === 'desc' ? 'bi-sort-down' : 'bi-sort-up'; ?>"></i>
                <?php echo $ordem === 'desc' ? 'Mais recentes primeiro' : 'Mais antigos primeiro'; ?>
            </a>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-2"
            style="margin-top:32px; max-width:1200px; margin-left:auto; margin-right:auto;">
            <?php if ($result->num_rows > 0): ?>
                <?php
                $today = date('Y-m-d');
                while ($row = $result->fetch_assoc()):
                    // Buscar data_fim para saber se está vencido
                    $sql_fim = "SELECT data_fim FROM atestado WHERE id_atestado = ?";
                    $stmt_fim = $conn->prepare($sql_fim);
                    $stmt_fim->bind_param("i", $row['id_atestado']);
                    $stmt_fim->execute();
                    $res_fim = $stmt_fim->get_result();
                    $data_fim = $res_fim->fetch_assoc()['data_fim'];
                    $is_expired = ($data_fim < $today);
                    ?>
                    <div class="col mb-2 d-flex align-items-stretch">
                        <div class="card position-relative w-100">
                            <!-- Ícone de status no canto superior direito -->
                            <span class="position-absolute top-0 end-0 p-2">
                                <?php if ($is_expired): ?>
                                    <!-- Ícone relógio vermelho (Bootstrap) -->
                                    <i class="bi bi-alarm-fill text-danger" title="Atestado vencido" style="font-size: 2rem;"></i>
                                <?php else: ?>
                                    <!-- Ícone relógio verde (Bootstrap) -->
                                    <i class="bi bi-alarm-fill text-success" title="Atestado válido" style="font-size: 2rem;"></i>
                                <?php endif; ?>
                            </span>
                            <p><strong>Nome do Paciente:</strong> <?php echo htmlspecialchars($row['nome_paciente']); ?></p>
                            <p><strong>Médico Responsável:</strong> <?php echo htmlspecialchars($row['medico_responsavel']); ?>
                            </p>
                            <p><strong>Data de Emissão:</strong>
                                <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_emissao']))); ?></p>
                            <p><strong>Justificativa:</strong> <?php echo htmlspecialchars($row['justificativa']); ?></p>
                            <p><strong>Período de Afastamento:</strong>
                                <?php echo htmlspecialchars($row['periodo_afastamento']); ?> dia(s)</p>
                            <?php
                            // Busca a data de término do afastamento
                            $data_fim_formatada = '';
                            if (!empty($data_fim)) {
                                $data_fim_formatada = date('d/m/Y', strtotime($data_fim));
                            }
                            ?>
                            <p><strong>Término de Afastamento:</strong> <?php echo htmlspecialchars($data_fim_formatada); ?></p>
                            <div class="text-center mt-3">
                                <a href="download_atestado.php?id=<?php echo $row['id_atestado']; ?>"
                                    class="btn btn-primary">Baixar o atestado</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="alert alert-warning text-center">Nenhum atestado encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    </script>
</body>

</html>

<?php
$conn->close();
?>