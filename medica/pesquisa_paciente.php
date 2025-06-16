<!-- filepath: c:\xampp\htdocs\site 6.0\pesquisa_paciente.php -->
<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o formulário foi enviado
$cpf = '';
if (isset($_GET['cpf'])) {
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf']); // Remove caracteres não numéricos
}

// Consulta SQL para buscar pacientes
$sql = "SELECT * FROM usuario";
if (!empty($cpf)) {
    $sql = "SELECT * FROM usuario WHERE cpf LIKE CONCAT('%', ?, '%')";
}

$stmt = $conn->prepare($sql);

if (!empty($cpf)) {
    $stmt->bind_param('s', $cpf); // Vincula o CPF como parâmetro
}

$stmt->execute();
$result = $stmt->get_result();

// Se for requisição AJAX, retorna só a tabela
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    ob_start();
    ?>
    <div id="tabela-pacientes">
        <table class="table table-bordered text-center mx-auto">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Gênero</th>
                    <th>Data de Nascimento</th>
                    <th>Peso</th>
                    <th>Tipo Sanguíneo</th>
                    <th>Ações</th>
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
                        <!-- <td><?php echo htmlspecialchars($row['id_usuario']); ?></td> -->
                        <td><?php echo htmlspecialchars($row['nome_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($row['email_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['tel_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['genero_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['naci_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['peso_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_sang_usuario']); ?></td>
                        <td>
                            <a href="ver_paciente.php?id=<?php echo $row['id_usuario']; ?>"
                                class="btn btn-sm btn-info mb-1" title="Ver Informações">
                                <i class="bi bi-info-circle"></i>
                            </a>
                            <a href="historico_vacinacao.php?id=<?php echo $row['id_usuario']; ?>"
                                class="btn btn-sm btn-success mb-1" title="Ver Histórico de Vacinação">
                                <i class="bi bi-journal-medical"></i>
                            </a>
                        </td>
                    </tr>
                    <?php $rowIndex++; endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    echo ob_get_clean();
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
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
            background: #FDFDFD;
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

        /* Remover estilo das colunas */
        .table td,
        .table th {
            background-color: transparent;
        }

        .btn-outline-danger {
            border-color: red;
            color: red;
            width: 220px;
            /* Aumenta o comprimento do botão */
        }

        .btn-outline-danger:hover {
            background-color: red;
            color: white;
        }

        .btn-outline-success {
            width: 220px;
            /* Aumenta o comprimento do botão */
        }

        /* Destaque para o bloco dos títulos */
        .table thead th {
            background-color: #0d6efd;
            /* Azul do site */
            color: white;
            /* Texto branco */
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Navbar padronizada -->
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
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.php">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
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

    <div class="container mt-4">
        <h2 class="text-center text-primary fw-bold">Pesquisar Pacientes</h2>
        <div class="container-fluid col-md-6 mt-4">
            <form class="d-flex position-relative" role="search" method="get" action="pesquisa_paciente.php"
                id="form-pesquisa-cpf">
                <input class="form-control me-2 border border-primary" type="search" name="cpf" id="cpf"
                    placeholder="Digite o CPF" aria-label="CPF"
                    value="<?php echo isset($cpf) ? htmlspecialchars($cpf) : ''; ?>" autocomplete="off">
            </form>
        </div>
        <br>
        <div id="tabela-pacientes" class="d-flex justify-content-center">
            <table class="table table-bordered text-center mx-auto">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Gênero</th>
                        <th>Data de Nascimento</th>
                        <th>Peso</th>
                        <th>Tipo Sanguíneo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowIndex = 0;
                    while ($row = $result->fetch_assoc()):
                        // Primeira linha branca, depois alterna entre cinza e branco
                        if ($rowIndex === 0) {
                            $rowClass = 'bg-white';
                        } else {
                            $rowClass = ($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white';
                        }
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <!-- <td><?php echo htmlspecialchars($row['id_usuario']); ?></td> -->
                            <td><?php echo htmlspecialchars($row['nome_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($row['email_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['tel_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['genero_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['naci_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['peso_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipo_sang_usuario']); ?></td>
                            <td>
                                <a href="ver_paciente.php?id=<?php echo $row['id_usuario']; ?>"
                                    class="btn btn-sm btn-info mb-1" title="Ver Informações">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <a href="historico_vacinacao.php?id=<?php echo $row['id_usuario']; ?>"
                                    class="btn btn-sm btn-success mb-1" title="Ver Histórico de Vacinação">
                                    <i class="bi bi-journal-medical"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $rowIndex++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#cpf').mask('000.000.000-00');
            $('#cpf').on('input', function () {
                var cpf = $(this).val();
                $.ajax({
                    url: 'pesquisa_paciente.php',
                    type: 'GET',
                    data: { cpf: cpf },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (data) {
                        $('#tabela-pacientes').replaceWith(data);
                    }
                });
            });
        });
    </script>
</body>

</html>
<?php
$conn->close();
?>