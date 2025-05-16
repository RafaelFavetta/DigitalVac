<?php
session_start();
require_once '../outros/db_connect.php';

// Verifica se o ID do usuário está na sessão
if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];

// Consulta para buscar os atestados relacionados ao usuário
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
        WHERE a.id_paci = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac - Atestados Médicos</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container {
            max-width: 95%;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
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
                    <a class="nav-link active fs-6 fw-bold" href="carteira_vac.php">
                        <i class="bi bi-postcard-heart-fill"></i> Carteira de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="proxima_vac.php">
                        <i class="bi bi-calendar2-week-fill"></i> Próximas Vacinas
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="atestado_medico.php">
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

    <!-- Conteúdo Principal -->
    <div class="container mt-4">
        <h2 class="text-center text-primary fw-bold">Atestados Médicos</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card p-4">
                    <p><strong>Nome do Paciente:</strong> <?php echo htmlspecialchars($row['nome_paciente']); ?></p>
                    <p><strong>Médico Responsável:</strong> <?php echo htmlspecialchars($row['medico_responsavel']); ?></p>
                    <p><strong>Data de Emissão:</strong>
                        <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_emissao']))); ?></p>
                    <p><strong>Justificativa:</strong> <?php echo htmlspecialchars($row['justificativa']); ?></p>
                    <p><strong>Período de Afastamento:</strong> <?php echo htmlspecialchars($row['periodo_afastamento']); ?>
                        dias</p>
                    <div class="text-center mt-4">
                        <a href="download_atestado.php?id=<?php echo $row['id_atestado']; ?>" class="btn btn-primary">Baixar o
                            atestado</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="alert alert-warning text-center">Nenhum atestado encontrado.</p>
        <?php endif; ?>

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