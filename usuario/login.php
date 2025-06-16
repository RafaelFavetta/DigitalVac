<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove símbolos do CPF
    $senha = $_POST['senha'];

    error_log("Tentativa de login: CPF = $cpf, Senha = $senha"); // Log para depuração

    if (!validarCPF($cpf)) {
        $erro = "CPF inválido!";
    } else {
        $stmt = $conn->prepare("SELECT id_usuario, senha FROM usuario WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Senha armazenada no banco (criptografada): " . $row['senha']); // Log para depuração

            if (password_verify($senha, $row['senha'])) { // Verifica a senha criptografada
                error_log("Login bem-sucedido para CPF: $cpf"); // Log para depuração
                $_SESSION['id_usuario'] = $row['id_usuario'];
                header("Location: telainicioU.php");
                exit();
            } else {
                error_log("Senha inválida para CPF: $cpf"); // Log para depuração
                $erro = "CPF ou senha inválidos!";
            }
        } else {
            error_log("CPF não encontrado: $cpf"); // Log para depuração
            $erro = "CPF ou senha inválidos!";
        }

        $stmt->close();
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
        /* ...existing styles... */
        body {
            background: #FDFDFD !important;
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
            <a href="../index.php" class="btn btn-link text-decoration-none text-primary "
                style="position: absolute; top: 10px; left: 10px;">
                <i class="bi bi-caret-left-fill" style="font-size: 20px;"></i> Voltar
            </a>
            <!-- Logo -->
            <img src="../img/logo.png" alt="Logo" class="mx-auto d-block mb-4" style="width: 100px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Login Usuário</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Digite seu CPF" required
                        maxlength="14" inputmode="numeric" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha"
                        required minlength="6" maxlength="20">
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                <div class="text-center mt-3">
                    <a href="../outros/esquecisenhaU.php" class="text-decoration-none">Redefinir senha</a>
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
    <script>
        // Faz o usuário não conseguir voltar após logout
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script>
        // Aplica a máscara ao campo CPF
        const cpfField = new Cleave('#cpf', {
            delimiters: ['.', '.', '-'],
            blocks: [3, 3, 3, 2],
            numericOnly: true
        });

        // Máscara para senha: apenas impede colar espaços e limita tamanho
        document.getElementById('senha').addEventListener('input', function () {
            this.value = this.value.replace(/\s/g, '').slice(0, 20);
        });

        // Remove a máscara antes de enviar o formulário
        document.querySelector("form").addEventListener("submit", function (event) {
            const cpfInput = document.getElementById("cpf");
            cpfInput.value = cpfInput.value.replace(/\D/g, ""); // Remove tudo que não for número
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>   
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script> 
</body>

</html>