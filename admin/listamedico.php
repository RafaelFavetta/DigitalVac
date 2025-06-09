<?php
include('../outros/db_connect.php');
session_start();

$erro = "";


// Verifica se o formulário foi enviado
$cpf = '';
if (isset($_GET['cpf'])) {
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf']); // Remove caracteres não numéricos
}

// Consulta SQL para buscar médicos
$sql = "SELECT * FROM medico";
if (!empty($cpf)) {
    $sql = "SELECT * FROM medico WHERE cpf LIKE CONCAT('%', ?, '%')";
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
    <div id="tabela-medicos">
        <table class="table table-bordered text-center mx-auto">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>COREN/CRM</th>
                    <th>Tipo</th>
                    <th>Data de Nascimento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rowIndex = 0;
                while ($row = $result->fetch_assoc()):
                    $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo htmlspecialchars($row['nome_medico']); ?></td>
                        <td>
                            <?php
                            $cpf = $row['cpf'];
                            echo preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['email_medico']); ?></td>
                        <td>
                            <?php
                            $tel = $row['tel_medico'];
                            if (strlen($tel) == 11) {
                                echo preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $tel);
                            } elseif (strlen($tel) == 10) {
                                echo preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $tel);
                            } else {
                                echo htmlspecialchars($tel);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['coren_crm']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_medico']); ?></td>
                        <td>
                            <?php
                            $data = $row['naci_medico'];
                            echo date('d/m/Y', strtotime($data));
                            ?>
                        </td>
                        <td>
                            <!-- Ajuste os links de ação conforme necessário -->
                            <a href="editar_medico.php?id=<?php echo $row['id_medico']; ?>" class="btn btn-sm btn-info mb-1"
                                title="Editar Informações">
                                <i class="bi bi-pencil-square"></i>
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

        .table td,
        .table th {
            background-color: transparent;
        }

        .btn-outline-danger {
            border-color: red;
            color: red;
            width: 220px;
        }

        .btn-outline-danger:hover {
            background-color: red;
            color: white;
        }

        .btn-outline-success {
            width: 220px;
        }

        .table thead th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Navbar padronizada -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3" />
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.php">
                        <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px"></i>
                        Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill" style="font-size: 20px"></i>
                        Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" aria-disabled="true" href="cadastrovac.html">
                        <i class="bi bi-capsule" style="font-size: 20px"></i> Cadastrar
                        Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroenf.php">
                        <i class="bi bi-person-badge" style="font-size: 20px"></i>
                        Cadastrar Enfermeiro
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroposto.html">
                        <i class="bi bi-building-fill-add" style="font-size: 20px"></i>
                        Cadastrar Posto
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastrocampanha.html">
                        <i class="bi bi-megaphone-fill" style="font-size: 20px;"></i>
                        Cadastrar Campanha
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" href="listamedico.php">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 20px"></i>
                        Listar Medicos
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
        <h2 class="text-center text-primary fw-bold">Pesquisar Médicos</h2>
        <div class="container-fluid col-md-6 mt-4">
            <form class="d-flex position-relative" role="search" method="get" action="listamedico.php"
                id="form-pesquisa-cpf">
                <input class="form-control me-2 border border-primary" type="search" name="cpf" id="cpf"
                    placeholder="Digite o CPF" aria-label="CPF"
                    value="<?php echo isset($cpf) ? htmlspecialchars($cpf) : ''; ?>" autocomplete="off">
            </form>
        </div>
        <br>
        <div id="tabela-medicos" class="d-flex justify-content-center">
            <table class="table table-bordered text-center mx-auto">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>COREN/CRM</th>
                        <th>Tipo</th>
                        <th>Data de Nascimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowIndex = 0;
                    while ($row = $result->fetch_assoc()):
                        $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo htmlspecialchars($row['nome_medico']); ?></td>
                            <td>
                                <?php
                                $cpf = $row['cpf'];
                                echo preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['email_medico']); ?></td>
                            <td>
                                <?php
                                $tel = $row['tel_medico'];
                                if (strlen($tel) == 11) {
                                    echo preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $tel);
                                } elseif (strlen($tel) == 10) {
                                    echo preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $tel);
                                } else {
                                    echo htmlspecialchars($tel);
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['coren_crm']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipo_medico']); ?></td>
                            <td>
                                <?php
                                $data = $row['naci_medico'];
                                echo date('d/m/Y', strtotime($data));
                                ?>
                            </td>
                            <td>
                                <a href="editar_medico.php?id=<?php echo $row['id_medico']; ?>"
                                    class="btn btn-sm btn-info mb-1" title="Editar Informações">
                                    <i class="bi bi-pencil-square"></i>
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
    <script>
        $(document).ready(function () {
            $('#cpf').mask('000.000.000-00');
            $('#cpf').on('input', function () {
                var cpf = $(this).val();
                $.ajax({
                    url: 'listamedico.php',
                    type: 'GET',
                    data: { cpf: cpf },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (data) {
                        $('#tabela-medicos').replaceWith(data);
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