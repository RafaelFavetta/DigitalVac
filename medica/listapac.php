<?php
echo "<p style='color: red; font-weight: bold;'>Arquivo correto carregado!</p>";
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if (!isset($_SESSION['id_enfe'])) {
    header("Location: login.php");
    exit();
}
// Verifica se o formulário foi enviado
$cpf = '';
if (isset($_GET['cpf'])) {
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf']); // Remove caracteres não numéricos
}

// Debug temporário para verificar o CPF
// var_dump($cpf);

// Consulta SQL para buscar pacientes
$sql = "SELECT * FROM paciente";
if (!empty($cpf)) {
    $sql = "SELECT * FROM paciente WHERE cpf_paci = $cpf"; // Adiciona filtro de CPF
}

$stmt = $conn->prepare($sql);

if (!empty($cpf)) {
    $stmt->bind_param('s', $cpf); // Vincula o CPF como parâmetro
}

$stmt->execute();
$result = $stmt->get_result();

// Excluir paciente
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $delete_sql = "DELETE FROM paciente WHERE id_paci = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('i', $delete_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Paciente excluído com sucesso!'); window.location.href='listapac.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir paciente: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
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
                    <a class="nav-link active fs-6 fw-bold" href="cadastroenf.html">
                        <i class="bi bi-person-badge" style="font-size: 20px;"></i> Cadastrar Enfermeiro
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="listapac.php">
                        <i class="bi bi-person-lines-fill" style="font-size: 20px;"></i> Lista de Pacientes
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
        <h2 class="text-center text-primary fw-bold">Lista de Pacientes</h2>
        <div class="d-flex justify-content-center">
            <table class="table table-bordered text-center mx-auto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Gênero</th>
                        <th>Data de Nascimento</th>
                        <th>Peso</th>
                        <th>Tipo Sanguíneo</th>
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
                            <td><?php echo htmlspecialchars($row['id_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['primeiro_nome_paci'] . ' ' . $row['sobrenome_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['cpf_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['email_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefone_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['genero_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_nasci_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['peso_paci']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipo_sangue_paci']); ?></td>
                        </tr>
                    <?php $rowIndex++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>