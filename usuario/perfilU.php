<?php
// filepath: c:\xampp\htdocs\site7.0\usuario\perfilU.php

// Inicie a sessão para acessar o ID do usuário
session_start();

// Inclua o arquivo de conexão com o banco de dados
require_once '../outros/db_connect.php';

// Verifique se o ID do usuário está definido na sessão
if (!isset($_SESSION['id_usuario'])) {
    // Redirecione para a página de login se o usuário não estiver autenticado
    header("Location: login.php");
    exit();
}

// Obtenha o ID do usuário da sessão
$userId = $_SESSION['id_usuario'];

// Busque os dados do usuário no banco de dados
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifique se o usuário foi encontrado
if (!$user) {
    die("Usuário não encontrado.");
}

// Armazene os dados do usuário em variáveis
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
$endereco = buscarEnderecoPorCEP($cep);
$numero_casa = $user['nc_usuario'];

// Função para formatar CPF
function formatarCPF($cpf)
{
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
}

// Função para formatar telefone
function formatarTelefone($telefone)
{
    if (strlen($telefone) == 11) {
        return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $telefone);
    } elseif (strlen($telefone) == 10) {
        return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $telefone);
    }
    return $telefone; // Retorna sem formatação se não for 10 ou 11 dígitos
}

if ($genero == 'M') {
    $genero = 'Masculino';
} elseif ($genero == 'F') {
    $genero = 'Feminino';
} else {
    $genero = 'Outro';
}

// Função para buscar endereço pelo CEP usando a API ViaCEP
function buscarEnderecoPorCEP($cep)
{
    $cep = preg_replace('/[^0-9]/', '', $cep); // Remove caracteres não numéricos
    $url = "https://viacep.com.br/ws/{$cep}/json/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($response, true);

    // Verifique se a resposta contém os dados esperados
    if (isset($dados['erro']) || !isset($dados['logradouro'])) {
        return "CEP inválido ou não encontrado.";
    }

    return "{$dados['logradouro']}, {$dados['bairro']}, {$dados['localidade']} - {$dados['uf']}";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }
    </style>
</head>

<body>
    <!-- Toast Bootstrap -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 1080; top: 80px;">
        <div id="toast-alert" class="toast align-items-center text-bg-primary border-0" role="alert"
            aria-live="assertive" aria-atomic="true" style="min-width:350px; max-width:500px;">
            <div class="d-flex">
                <div class="toast-body" id="toast-alert-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

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
                        <i class="bi bi-house-fill"></i> Inicio
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
                    <a href="editarPefilU.php" class="btn btn-outline-primary fw-bold mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg> Editar perfil
                    </a>
                    <a href="../outros/sair.php" class="btn btn-danger fw-bold mt-3 px-2 py-1"
                        style="font-size: 15px; min-width: 70px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-box-arrow-right" viewBox="0 0 16 16" style="vertical-align: middle;">
                            <path fill-rule="evenodd"
                                d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                            <path fill-rule="evenodd"
                                d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                        </svg> Sair
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Dados</h3>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars(formatarCPF($cpf)); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars(formatarTelefone($telefone)); ?></p>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($genero); ?></p>
                    <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($dataNascimentoFormatada); ?>
                    </p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco); ?> Nº
                        <?php echo htmlspecialchars($numero_casa); ?></p>
                    <p><strong>Peso:</strong> <?php echo htmlspecialchars($peso); ?> Kg</p>
                    <p><strong>Tipo Sanguíneo:</strong> <?php echo htmlspecialchars($tipoSanguineo); ?></p>
                    <p><strong>Alergias:</strong> <?php echo htmlspecialchars($alergias); ?></p>
                    <p><strong>Doenças:</strong> <?php echo htmlspecialchars($doencas); ?></p>
                    <p><strong>Medicamentos:</strong> <?php echo htmlspecialchars($medicamentos); ?></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toast Bootstrap
        function showAlert(type, message) {
            const toastEl = document.getElementById('toast-alert');
            const toastBody = document.getElementById('toast-alert-body');
            toastBody.textContent = message;
            toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-primary');
            if (type === 'success') {
                toastEl.classList.add('text-bg-success');
            } else if (type === 'error') {
                toastEl.classList.add('text-bg-danger');
            } else {
                toastEl.classList.add('text-bg-primary');
            }
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }

        // Exibe toast se houver mensagem na URL
        (function() {
            const params = new URLSearchParams(window.location.search);
            const msg = params.get('toast');
            const type = params.get('toastType') || 'primary';
            if (msg) showAlert(type, msg);
        })();
    </script>
</body>
</html>