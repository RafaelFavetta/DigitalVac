<?php
include('../outros/db_connect.php');
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

$id_aplica = isset($_GET['id_aplica']) ? intval($_GET['id_aplica']) : 0;
$id_vaci = isset($_GET['id_vaci']) ? intval($_GET['id_vaci']) : 0;
if ($id_aplica <= 0 && $id_vaci <= 0) {
    echo "Aplicação ou vacina inválida.";
    exit;
}

// Função para formatar CPF
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2);
    }
    return $cpf;
}

// Função para formatar COREN/CRM (exemplo: COREN-XX 123456 ou CRM-XX 123456)
function formatarCorenCrm($coren) {
    $coren = trim($coren);
    // Tenta separar prefixo (COREN/CRM), UF e número
    if (preg_match('/^(COREN|CRM)[-\s]?([A-Z]{2})\s?(\d+)$/i', $coren, $m)) {
        return strtoupper($m[1]) . '-' . strtoupper($m[2]) . ' ' . $m[3];
    }
    // Fallback: separa letras e números
    if (preg_match('/^([A-Z\-]+)\s*(\d+)$/i', $coren, $m)) {
        return strtoupper($m[1]) . ' ' . $m[2];
    }
    return $coren;
}

if ($id_aplica > 0) {
    // Busca dados da aplicação e da vacina
    $sql = "SELECT 
                a.*, 
                v.nome_vaci, v.fabri_vaci, v.lote_vaci, v.idade_aplica, v.via_adimicao, v.n_dose, v.intervalo_dose, v.estoque,
                u.nome_usuario, u.cpf, u.email_usuario,
                p.nome_posto,
                m.nome_medico, m.coren_crm
            FROM aplicacao a
            JOIN vacina v ON a.id_vaci = v.id_vaci
            JOIN usuario u ON a.id_usuario = u.id_usuario
            JOIN posto p ON a.id_posto = p.id_posto
            JOIN medico m ON a.id_medico = m.id_medico
            WHERE a.id_aplica = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Erro ao preparar consulta: " . $conn->error;
        exit;
    }
    $stmt->bind_param("i", $id_aplica);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        echo "Aplicação não encontrada.";
        exit;
    }
    $row = $result->fetch_assoc();

    // Verificações para campos possivelmente nulos
    $data_aplica = !empty($row['data_aplica']) ? date('d/m/Y', strtotime($row['data_aplica'])) : '';
    $dose_aplicad = isset($row['dose_aplicad']) ? $row['dose_aplicad'] : '';
} elseif ($id_vaci > 0) {
    // Busca apenas dados da vacina
    $sql = "SELECT 
                v.nome_vaci, v.fabri_vaci, v.lote_vaci, v.idade_aplica, v.via_adimicao, v.n_dose, v.intervalo_dose, v.estoque
            FROM vacina v
            WHERE v.id_vaci = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Erro ao preparar consulta: " . $conn->error;
        exit;
    }
    $stmt->bind_param("i", $id_vaci);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        echo "Vacina não encontrada.";
        exit;
    }
    $row = $result->fetch_assoc();
    // Preenche campos de aplicação como vazio
    $data_aplica = '';
    $dose_aplicad = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Informações da Vacina</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); padding: 32px; margin-top: 32px;}
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
                    <a class="nav-link active fs-6 fw-bold" href="telainicio.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.html">
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
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Meus Atestados
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
    <div class="container">
        <div class="card mx-auto" style="max-width: 700px;">
            <h2 class="card-title mb-4">Informações da Vacina Aplicada</h2>
            <?php if ($id_aplica > 0): ?>
            <h5 class="mb-3">Paciente: <?php echo htmlspecialchars($row['nome_usuario']); ?> (CPF: <?php echo htmlspecialchars(formatarCPF($row['cpf'])); ?>)</h5>
            <?php endif; ?>
            <h6 class="mb-3">Vacina: <?php echo htmlspecialchars($row['nome_vaci']); ?></h6>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Fabricante:</strong> <?php echo htmlspecialchars($row['fabri_vaci']); ?></li>
                <li class="list-group-item"><strong>Lote:</strong> <?php echo htmlspecialchars($row['lote_vaci']); ?></li>
                <li class="list-group-item"><strong>Idade de Aplicação Recomendada:</strong> <?php echo htmlspecialchars($row['idade_aplica']); ?> anos</li>
                <li class="list-group-item"><strong>Via de Administração:</strong> <?php echo htmlspecialchars($row['via_adimicao']); ?></li>
                <li class="list-group-item"><strong>Número de Doses do Esquema:</strong> <?php echo htmlspecialchars($row['n_dose']); ?></li>
                <li class="list-group-item"><strong>Intervalo entre Doses:</strong> <?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</li>
                <li class="list-group-item"><strong>Estoque Atual:</strong> <?php echo htmlspecialchars($row['estoque']); ?></li>
            </ul>
            <?php if ($id_aplica > 0): ?>
            <h6 class="mb-3">Informações da Aplicação</h6>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Data da Aplicação:</strong> <?php echo htmlspecialchars($data_aplica); ?></li>
                <li class="list-group-item"><strong>Dose Aplicada:</strong> <?php echo htmlspecialchars($dose_aplicad); ?></li>
                <li class="list-group-item"><strong>Posto:</strong> <?php echo htmlspecialchars($row['nome_posto']); ?></li>
                <li class="list-group-item"><strong>Profissional Responsável:</strong>
                    <?php
                        echo htmlspecialchars($row['nome_medico']);
                        $coren = trim($row['coren_crm']);
                        // Força a máscara COREN-XX 123456 ou CRM-XX 123456
                        if (preg_match('/^(COREN|CRM)[\s\-]*([A-Z]{2})[\s\-]*(\d+)$/i', $coren, $m)) {
                            $coren_formatado = strtoupper($m[1]) . '-' . strtoupper($m[2]) . ' ' . $m[3];
                        } elseif (preg_match('/^([A-Z\-]+)\s*(\d+)$/i', $coren, $m)) {
                            $coren_formatado = strtoupper($m[1]) . ' ' . $m[2];
                        } else {
                            $coren_formatado = $coren;
                        }
                        echo ' (' . htmlspecialchars($coren_formatado) . ')';
                    ?>
                </li>
            </ul>
            <?php endif; ?>
            <a href="javascript:history.back()" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
