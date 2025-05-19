<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

// Consulta SQL corrigida para buscar os atributos corretos da tabela `vacina`
$sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, idade_aplica, via_adimicao, n_dose, intervalo_dose, estoque 
        FROM vacina";
$result = $conn->query($sql);

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

        /* Estilo para linhas alternadas */
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Cor cinza claro para linhas pares */
        }

        .table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Cor branca para linhas ímpares */
        }

        /* Destaque para o bloco dos títulos */
        .table thead th {
            background-color: #0d6efd; /* Azul do site */
            color: white; /* Texto branco */
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
                        <i class="bi bi-person-plus" style="font-size: 20px;"></i> Cadastrar Pacientes
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
        <div class="text-end mb-3">
            <!-- Removido o botão "Cadastrar Atestado" daqui -->
        </div>
        <table class="table table-bordered">
            <thead class="table-primary">
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
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
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
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>