<?php
session_start();
require_once '../outros/db_connect.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id_usuario'];

$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}

function formatarCPF($cpf) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
}

function formatarTelefone($telefone) {
    if (strlen($telefone) == 11) {
        return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $telefone);
    } elseif (strlen($telefone) == 10) {
        return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $telefone);
    }
    return $telefone;
}

function formatarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) === 8) {
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    return $cep;
}

function buscarEnderecoPorCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $dados = json_decode($response, true);
    if (isset($dados['erro']) || !isset($dados['logradouro'])) {
        return "CEP inválido ou não encontrado.";
    }
    return "{$dados['logradouro']}, {$dados['bairro']}, {$dados['localidade']} - {$dados['uf']}";
}

$nome = $user['nome_usuario'];
$cpf = $user['cpf'];
$telefone = $user['tel_usuario'];
$genero = $user['genero_usuario'];
$dataNascimento = $user['naci_usuario'];
$dataNascimentoFormatada = date('d/m/Y', strtotime($dataNascimento));
$email = $user['email_usuario'];
$cep = $user['cep_usuario'];
$peso = $user['peso_usuario'];
$tipoSanguineo = $user['tipo_sang_usuario'];
$alergias = $user['ale_usuario'];
$doencas = $user['doen_usuario'];
$medicamentos = $user['med_usuario'];
$numero_casa = $user['nc_usuario'];
$cidade = $user['cidade'] ?? '';
$endereco_db = $user['endereco'] ?? '';

$generoTexto = ($genero == 'M') ? 'Masculino' : (($genero == 'F') ? 'Feminino' : 'Outro');

if (!$endereco_db) {
    $endereco = buscarEnderecoPorCEP($cep);
} else {
    $endereco = $endereco_db;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil Usuário - DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }
        /* Mesmas medidas do botão editar do ver_paciente.php */
        .btn-editar {
            font-weight: bold;
            margin-top: 1rem;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            min-width: 140px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3" />
                <a class="navbar-brand" href="#">DigitalVac</a>
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
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="perfilU.php">
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
                        <a class="btn btn-danger fw-bold px-2 py-1" style="font-size: 15px; min-width: 70px;" href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg p-4 text-center">
                    <h3 class="mt-3"><?php echo htmlspecialchars($nome); ?></h3>

                    <!-- Botão editar com as mesmas medidas do ver_paciente -->
                    <a href="editarPefilU.php" class="btn btn-outline-primary btn-editar">
                        <i class="bi bi-pencil-square"></i> Editar perfil
                    </a>

                    <a href="../outros/sair.php" class="btn btn-danger fw-bold mt-3 px-2 py-1" style="font-size: 15px; min-width: 70px;">
                        <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Dados</h3>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars(formatarCPF($cpf)); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars(formatarTelefone($telefone)); ?></p>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($generoTexto); ?></p>
                    <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($dataNascimentoFormatada); ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars(formatarCEP($cep)); ?></p>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco); ?> Nº <?php echo htmlspecialchars($numero_casa); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($cidade); ?></p>
                    <p><strong>Peso:</strong> <?php echo htmlspecialchars($peso); ?> Kg</p>
                    <p><strong>Tipo Sanguíneo:</strong> <?php echo htmlspecialchars($tipoSanguineo); ?></p>
                    <p><strong>Alergias:</strong> <?php echo htmlspecialchars($alergias); ?></p>
                    <p><strong>Doenças:</strong> <?php echo htmlspecialchars($doencas); ?></p>
                    <p><strong>Medicamentos:</strong> <?php echo htmlspecialchars($medicamentos); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
