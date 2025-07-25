<?php
session_start();
require_once '../outros/db_connect.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_vaci = isset($_GET['id_vaci']) ? intval($_GET['id_vaci']) : 0;
if ($id_vaci <= 0) {
    echo "Vacina não especificada.";
    exit;
}

$sql = "SELECT * FROM vacina WHERE id_vaci = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vaci);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo "Vacina não encontrada.";
    exit;
}
$row = $result->fetch_assoc();
$stmt->close();
$conn->close();
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
        body { background-color: #f8f9fa; }
        .card {
            background: #FDFDFD;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            padding: 32px;
            margin-top: 32px;
        }
        .card-title { color: #0d6efd; font-weight: bold; }
        .btn-back { margin-top: 24px; }
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
    <div class="container">
        <div class="card mx-auto" style="max-width: 700px;">
            <h2 class="card-title mb-4">Informações da Vacina</h2>
            <h6 class="mb-3">Vacina: <?php echo htmlspecialchars($row['nome_vaci']); ?></h6>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Fabricante:</strong> <?php echo htmlspecialchars($row['fabri_vaci']); ?></li>
                <li class="list-group-item"><strong>Lote:</strong> <?php echo htmlspecialchars($row['lote_vaci']); ?></li>
                <li class="list-group-item">
                    <strong>Idade de Aplicação Recomendada:</strong>
                    <?php
                        $nome = $row['nome_vaci'];
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
                            echo htmlspecialchars($row['idade_reco'] ?? "Ao nascer");
                        }
                    ?>
                </li>
                <li class="list-group-item"><strong>Via de Administração:</strong> <?php echo htmlspecialchars($row['via_adimicao']); ?></li>
                <li class="list-group-item"><strong>Número de Doses do Esquema:</strong> <?php echo htmlspecialchars($row['n_dose']); ?></li>
                <li class="list-group-item"><strong>Intervalo entre Doses:</strong> <?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</li>
                <li class="list-group-item"><strong>Estoque Atual:</strong> <?php echo htmlspecialchars($row['estoque']); ?></li>
            </ul>
            <a href="javascript:history.back()" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
