<?php
include('../outros/db_connect.php');
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Usuário inválido.";
    exit;
}

// Atualização dos dados
$sucesso = false;
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = trim($_POST['nome_usuario']);
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $email_usuario = trim($_POST['email_usuario']);
    $tel_usuario = preg_replace('/\D/', '', $_POST['tel_usuario']);
    $genero_usuario = $_POST['genero_usuario'];
    $naci_usuario = $_POST['naci_usuario'];
    $peso_usuario = $_POST['peso_usuario'];
    $tipo_sang_usuario = $_POST['tipo_sang_usuario'];
    $ale_usuario = trim($_POST['ale_usuario']);
    $doen_usuario = trim($_POST['doen_usuario']);
    $med_usuario = trim($_POST['med_usuario']);
    $cep_usuario = preg_replace('/\D/', '', $_POST['cep_usuario']);
    $nc_usuario = $_POST['nc_usuario'];
    $endereco = trim($_POST['endereco']);
    $cidade = trim($_POST['cidade']);

    $sql = "UPDATE usuario SET 
        nome_usuario=?, cpf=?, email_usuario=?, tel_usuario=?, genero_usuario=?, naci_usuario=?, peso_usuario=?, tipo_sang_usuario=?, ale_usuario=?, doen_usuario=?, med_usuario=?, cep_usuario=?, nc_usuario=?, endereco=?, cidade=?
        WHERE id_usuario=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssdsssssssi",
        $nome_usuario, $cpf, $email_usuario, $tel_usuario, $genero_usuario, $naci_usuario, $peso_usuario, $tipo_sang_usuario,
        $ale_usuario, $doen_usuario, $med_usuario, $cep_usuario, $nc_usuario, $endereco, $cidade, $id
    );
    if ($stmt->execute()) {
        $sucesso = true;
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Busca os dados atualizados
$stmt = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Usuário não encontrado.";
    exit;
}
$user = $result->fetch_assoc();

// Funções utilitárias
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
        return "";
    }
    return "{$dados['logradouro']}, {$dados['bairro']}, {$dados['localidade']} - {$dados['uf']}";
}

// Dados do usuário
$nome = $user['nome_usuario'];
$cpf = $user['cpf'];
$telefone = $user['tel_usuario'];
$genero = $user['genero_usuario'];
$dataNascimento = $user['naci_usuario'];
$email = $user['email_usuario'];
$cep = $user['cep_usuario'];
$peso = $user['peso_usuario'];
$tipoSanguineo = $user['tipo_sang_usuario'];
$alergias = $user['ale_usuario'];
$doencas = $user['doen_usuario'];
$medicamentos = $user['med_usuario'];
$numero_casa = $user['nc_usuario'];
$endereco = $user['endereco'] ?: buscarEnderecoPorCEP($cep);
$cidade = $user['cidade'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg p-4 text-center">
                    <h3 class="mt-3"><?php echo htmlspecialchars($nome); ?></h3>
                    <a href="ver_paciente.php?id=<?php echo $id; ?>" class="btn btn-outline-primary fw-bold mt-3">
                        <i class="bi bi-info-circle"></i> Ver informações
                    </a>
                    <a href="pesquisa_paciente.php" class="btn btn-danger fw-bold mt-3 px-2 py-1" style="font-size: 15px; min-width: 70px;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Editar Dados</h3>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">Dados atualizados com sucesso!</div>
                    <?php elseif ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off" id="form-editar-paciente">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nome</label>
                                <input type="text" name="nome_usuario" class="form-control" required value="<?php echo htmlspecialchars($nome); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control" required maxlength="14" value="<?php echo htmlspecialchars(formatarCPF($cpf)); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">E-mail</label>
                                <input type="email" name="email_usuario" class="form-control" required value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telefone</label>
                                <input type="text" name="tel_usuario" id="tel_usuario" class="form-control" required maxlength="15" value="<?php echo htmlspecialchars(formatarTelefone($telefone)); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gênero</label>
                                <select name="genero_usuario" class="form-select" required>
                                    <option value="M" <?php if($genero=='M') echo 'selected'; ?>>Masculino</option>
                                    <option value="F" <?php if($genero=='F') echo 'selected'; ?>>Feminino</option>
                                    <option value="O" <?php if($genero=='O') echo 'selected'; ?>>Outro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Data de Nascimento</label>
                                <input type="date" name="naci_usuario" class="form-control" required value="<?php echo htmlspecialchars($dataNascimento); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Peso (kg)</label>
                                <input type="number" step="0.01" name="peso_usuario" class="form-control" required value="<?php echo htmlspecialchars($peso); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipo Sanguíneo</label>
                                <select name="tipo_sang_usuario" class="form-select" required>
                                    <?php
                                    $tipos = ['O+','O-','A+','A-','B+','B-','AB+','AB-'];
                                    foreach ($tipos as $tipo) {
                                        echo "<option value=\"$tipo\"".($tipoSanguineo==$tipo?' selected':'').">$tipo</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alergias</label>
                                <input type="text" name="ale_usuario" class="form-control" value="<?php echo htmlspecialchars($alergias); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doenças</label>
                                <input type="text" name="doen_usuario" class="form-control" value="<?php echo htmlspecialchars($doencas); ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Medicamentos</label>
                            <input type="text" name="med_usuario" class="form-control" value="<?php echo htmlspecialchars($medicamentos); ?>">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CEP</label>
                                <input type="text" name="cep_usuario" class="form-control" required maxlength="8" value="<?php echo htmlspecialchars($cep); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número da Casa</label>
                                <input type="text" name="nc_usuario" class="form-control" required maxlength="10" value="<?php echo htmlspecialchars($numero_casa); ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Endereço</label>
                            <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($endereco); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control" value="<?php echo htmlspecialchars($cidade); ?>">
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary fw-bold">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Máscara para CPF
        new Cleave('#cpf', {
            delimiters: ['.', '.', '-'],
            blocks: [3, 3, 3, 2],
            numericOnly: true
        });

        // Máscara para telefone (celular/fixo)
        new Cleave('#tel_usuario', {
            phone: true,
            phoneRegionCode: 'BR'
        });

        // Remove máscara antes de enviar o formulário
        document.getElementById('form-editar-paciente').addEventListener('submit', function(e) {
            var cpfInput = document.getElementById('cpf');
            cpfInput.value = cpfInput.value.replace(/\D/g, '');

            var telInput = document.getElementById('tel_usuario');
            telInput.value = telInput.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
