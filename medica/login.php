<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coren_crm = htmlspecialchars(trim($_POST['coren_crm'])); // Sanitize and trim input
    $senha = $_POST['senha'];

    // Verificar se os campos estão preenchidos
    if (empty($coren_crm) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    }
    // Verificação para o usuário administrador
    elseif ($coren_crm === 'administrador' && $senha === 'admin') {
        header('Location: ../admin/cadastroaplic.php');
        exit();
    } else {
        $stmt = $conn->prepare("SELECT id_medico, coren_crm, senha FROM medico WHERE coren_crm = ?");
        if (!$stmt) {
            error_log("Erro ao preparar a consulta: " . $conn->error); // Log de erro
            $erro = "Erro interno. Tente novamente mais tarde.";
        } else {
            $stmt->bind_param("s", $coren_crm);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                error_log("COREN/CRM encontrado: " . $row['coren_crm']); // Log para depuração

                // Verificar se a senha é criptografada
                if (password_verify($senha, $row['senha'])) { // Caso seja criptografada
                    $_SESSION['id_medico'] = $row['id_medico']; // Store id_medico in session
                    error_log("Login bem-sucedido com senha criptografada."); // Log de sucesso
                    header("Location: telainicio.php");
                    exit();
                } elseif ($senha === $row['senha']) { // Caso não seja criptografada
                    $_SESSION['id_medico'] = $row['id_medico']; // Store id_medico in session
                    error_log("Login bem-sucedido com senha não criptografada."); // Log de sucesso
                    header("Location: telainicio.php");
                    exit();
                } else {
                    error_log("Senha inválida para COREN/CRM: " . $coren_crm); // Log para depuração
                    $erro = "COREN/CRM ou senha inválidos!";
                }
            } else {
                error_log("COREN/CRM não encontrado: " . $coren_crm); // Log para depuração
                $erro = "COREN/CRM ou senha inválidos!";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/telalogin.css">
    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php if ($erro): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>



    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px; position: relative;">
            <!-- Botão de Voltar -->
            <a href="javascript:history.back()" class="btn btn-link text-decoration-none text-primary"
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Login Médico</h2>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="coren_crm" class="form-label">COREN ou CRM</label>
                    <input type="text" name="coren_crm" id="coren_crm" class="form-control"
                        placeholder="COREN/CRM-UF 000000" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha"
                        required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                <div class="text-center mt-3">
                    <a href="../outros/esquecisenha.php" class="text-decoration-none">Redefinir senha</a>
                </div>
            </form>
            <div class="text-center mt-2">
                <a href="#" class="small text-muted" data-bs-toggle="modal" data-bs-target="#termosModal">Termos e Condições</a>
            </div>
        </div>
    </div>
    <!-- Modal Termos e Condições -->
    <div class="modal fade" id="termosModal" tabindex="-1" aria-labelledby="termosModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="termosModalLabel">Termos e Condições</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body" style="max-height: 32em; overflow-y: auto;">
            <strong>Política de Privacidade</strong>
            <p>
              A sua privacidade é uma prioridade para a plataforma DigitalVac. Este documento descreve de forma clara e objetiva como as informações dos usuários são coletadas, utilizadas, armazenadas e protegidas.
            </p>
            <ol>
              <li>
                <strong>Coleta de Dados</strong>
                <p>
                  A plataforma DigitalVac coleta apenas os dados essenciais para seu funcionamento. As informações armazenadas incluem:
                </p>
                <ul>
                  <li>Dados de vacinação: Informações registradas por profissionais de saúde, como tipo de vacina, dose, data de aplicação e local de vacinação.</li>
                  <li>Dados de identificação: Quando necessário para o uso correto do sistema, podem ser armazenados dados como nome completo e CPF para associar corretamente os registros vacinais ao cidadão.</li>
                </ul>
                <p><strong>Importante:</strong> Nenhum dado sensível é solicitado diretamente ao usuário sem justificativa funcional no sistema.</p>
              </li>
              <li>
                <strong>Finalidade do Uso das Informações</strong>
                <p>Os dados coletados são utilizados exclusivamente para:</p>
                <ul>
                  <li>Gerenciamento da carteira de vacinação digital: Registrar, organizar e permitir a visualização do histórico vacinal do usuário.</li>
                  <li>Acesso profissional autorizado: Permitir que profissionais da saúde visualizem e atualizem as informações de vacinação.</li>
                  <li>Divulgação de campanhas de vacinação: Exibir ao usuário campanhas relevantes com base em seu histórico de imunização.</li>
                </ul>
                <p>Não utilizamos as informações para fins comerciais ou publicitários.</p>
              </li>
              <li>
                <strong>Compartilhamento de Dados</strong>
                <p>A DigitalVac adota uma política restritiva quanto ao compartilhamento de informações:</p>
                <ul>
                  <li>Os dados só são acessíveis por profissionais de saúde devidamente autorizados, no contexto de atendimento e atualização do histórico vacinal.</li>
                  <li>Não há compartilhamento com terceiros, exceto quando exigido por lei ou determinação judicial.</li>
                </ul>
              </li>
              <li>
                <strong>Armazenamento e Segurança</strong>
                <p>Medidas de segurança aplicadas:</p>
                <ul>
                  <li>Proteção de banco de dados contra acesso não autorizado;</li>
                  <li>Validação de dados inseridos por profissionais de saúde;</li>
                  <li>Camadas de autenticação para acesso ao painel administrativo (quando aplicável).</li>
                </ul>
                <p>
                  Embora sejam aplicadas boas práticas de segurança, nenhuma plataforma é 100% imune. Em caso de incidente, serão tomadas medidas rápidas para mitigar impactos e proteger os dados dos usuários.
                </p>
              </li>
              <li>
                <strong>Direitos do Usuário</strong>
                <p>O usuário possui pleno controle sobre suas informações e poderá:</p>
                <ul>
                  <li>Solicitar atualização ou correção dos dados de identificação, caso estejam incorretos;</li>
                  <li>Solicitar a exclusão dos dados do sistema, respeitando as obrigações legais e o vínculo com o sistema de saúde;</li>
                  <li>Consultar integralmente seu histórico vacinal digital, a qualquer momento.</li>
                </ul>
                <p>
                  Para exercer esses direitos, o usuário deverá entrar em contato com a equipe responsável pelo sistema, por meio dos canais disponibilizados na própria plataforma.
                </p>
              </li>
              <li>
                <strong>Alterações nesta Política</strong>
                <p>
                  Esta política pode ser atualizada para refletir eventuais mudanças técnicas ou legais. Em caso de alterações relevantes, os usuários serão informados por meio do próprio site.
                </p>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>