<!-- filepath: c:\xampp\htdocs\site 6.0\pesquisa_paciente.php -->
<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

$erro = "";
$pacientes = [];

// Pesquisa de pacientes
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cpf'])) {
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf']); // Remove caracteres não numéricos
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE cpf LIKE CONCAT('%', ?, '%')"); // Busca parcial por CPF
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pacientes = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $erro = "Nenhum paciente encontrado.";
    }
} else {
    // Exibe todos os pacientes se nenhum CPF for pesquisado
    $result = $conn->query("SELECT * FROM usuario");
    if ($result->num_rows > 0) {
        $pacientes = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Pacientes</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
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

        /* Remover estilo das colunas */
        .table td,
        .table th {
            background-color: transparent;
        }

        .btn-outline-danger {
            border-color: red;
            color: red;
            width: 220px; /* Aumenta o comprimento do botão */
        }

        .btn-outline-danger:hover {
            background-color: red;
            color: white;
        }

        .btn-outline-success {
            width: 220px; /* Aumenta o comprimento do botão */
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
                        <i class="bi bi-person-plus-fill" style="font-size: 20px;"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="listavac.php">
                        <i class="bi bi-list" style="font-size: 20px;"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="pesquisa_paciente.php">
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
        <h2 class="text-center text-primary fw-bold">Pesquisar Pacientes</h2>
        <div class="text-end mb-3">
            <!-- Removido o botão "Cadastrar Atestado" daqui -->
        </div>
        <div class="container-fluid col-md-6 mt-4">
            <form class="d-flex" role="search" method="get" action="pesquisa_paciente.php">
                <input class="form-control me-2 border border-primary fw-bold" type="text" name="cpf" id="cpf"
                    placeholder="Digite o CPF" aria-label="CPF" value="<?php echo isset($cpf) ? htmlspecialchars($cpf) : ''; ?>">
                <button class="btn btn-outline-success fw-bold me-2" type="submit">Pesquisar</button>
                <a href="pesquisa_paciente.php" class="btn btn-outline-danger fw-bold">Limpar Filtros</a>
            </form>
        </div>
        <br>
        <?php if (!empty($pacientes)): ?>
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Data de Nascimento</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Tipo Sanguíneo</th>
                        <th>Peso (kg)</th>
                        <th>Gênero</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paciente['nome_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['cpf']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($paciente['naci_usuario']))); ?></td> <!-- Formata a data -->
                            <td><?php echo htmlspecialchars($paciente['tel_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['email_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['tipo_sang_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['peso_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['genero_usuario']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($erro): ?>
            <p class="alert alert-warning text-center"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function () {
            $('#cpf').mask('000.000.000-00'); // Aplica a máscara de CPF
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>