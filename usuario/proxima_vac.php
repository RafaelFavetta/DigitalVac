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

    // Filtrar vacinas que o usuário ainda não tomou
    $vacinas_nao_tomadas = array_filter($vacinas_disponiveis, function ($vacina) use ($vacinas_aplicadas) {
        return !in_array($vacina['id_vaci'], $vacinas_aplicadas);
    });
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

        .table {
            margin-top: 20px;
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
        <h2 class="text-center text-primary fw-bold">Vacinas a serem aplicadas</h2>
        <div class="table-responsive">
            <table class="table table-striped-columns text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Vacina</th>
                        <th>Idade Recomendada</th>
                        <th>Doses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vacinas_nao_tomadas)): ?>
                        <?php foreach ($vacinas_nao_tomadas as $vacina): ?>
                            <tr>
                                <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                                <td><?= $vacina['idade_aplica'] ?> anos</td>
                                <td><?= $vacina['n_dose'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhuma vacina pendente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-center mt-4">
            <a href="https://www.gov.br/saude/pt-br/vacinacao/calendario" target="_blank" class="link">Acesse a lista de
                vacinas</a>
        </p>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>