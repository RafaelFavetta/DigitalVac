<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: auto;
        }

        /* Remover linha azul dos inputs ao focar */
        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            box-shadow: none !important;
            border-color: #ced4da !important;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .img-select {
            opacity: 25%;
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
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.html">
                        <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px;"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill" style="font-size: 20px;"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastrovac.html">
                        <i class="bi bi-capsule" style="font-size: 20px;"></i> Cadastrar Vacinas
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="cadastroenf.php">
                        <i class="bi bi-person-badge" style="font-size: 20px;"></i> Cadastrar Enfermeiro
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroposto.html">
                        <i class="bi bi-building-fill-add" style="font-size: 20px;"></i> Cadastrar Posto
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

    <div class="container d-flex align-items-center justify-content-center">
        <div class="form-container mx-auto">
            <!-- Toast Bootstrap -->
            <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3"
                style="z-index: 1080; top: 80px;">
                <div id="toast-alert"
                    class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" style="min-width:350px; max-width:500px;">
                    <div class="d-flex">
                        <div class="toast-body" id="toast-alert-body"></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <h2 class="text-center text-primary fw-bold">Cadastro de Médico</h2>
            <form id="cadastroEnfForm" action="../Recebedados/recebedadosenfermeiro.php" method="POST">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="nome_medico" class="form-label fw-bold">Nome Completo</label>
                        <input type="text" id="nome_medico" name="nome_medico" class="form-control"
                            placeholder="Nome Completo" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cpf" class="form-label fw-bold">CPF</label>
                        <input type="text" id="cpf" name="cpf" class="form-control" placeholder="CPF" required maxlength="14"
                            minlength="14">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="telefone" class="form-label fw-bold">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control" placeholder="Telefone" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="data_nascimento" class="form-label fw-bold">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="coren_crm" class="form-label fw-bold">COREN/CRM</label>
                        <input type="text" id="coren_crm" name="coren_crm" maxlength="15" minlength="13"
                            class="form-control" placeholder="COREN/CRM-UF 000000" required>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="tipo_medico" class="form-label fw-bold">Tipo de Médico</label>
                        <select id="tipo_medico" name="tipo_medico" class="form-select" required>
                            <option value="" type="select"></option>
                            <option value="Cardiologista">Cardiologista</option>
                            <option value="Clínico Geral">Clínico Geral</option>
                            <option value="Dermatologista">Dermatologista</option>
                            <option value="Endocrinologista">Endocrinologista</option>
                            <option value="Enfermeiro">Enfermeiro</option>
                            <option value="Ginecologista">Ginecologista</option>
                            <option value="Neurologista">Neurologista</option>
                            <option value="Oftalmologista">Oftalmologista</option>
                            <option value="Ortopedista">Ortopedista</option>
                            <option value="Pediatra">Pediatra</option>
                            <option value="Psiquiatra">Psiquiatra</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_posto_trabalho" class="form-label fw-bold">Posto de Trabalho</label>
                        <select id="id_posto_trabalho" name="posto_trabalho" class="form-select" required>
                            <option value=""></option>
                            <?php
                            include('../outros/db_connect.php');
                            if ($conn) {
                                $sql = "SELECT id_posto, nome_posto FROM posto ORDER BY nome_posto";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['id_posto']) . "'>" . htmlspecialchars($row['nome_posto']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Nenhum posto disponível</option>";
                                    error_log("Nenhum resultado encontrado na tabela 'posto'.");
                                }
                                $conn->close();
                            } else {
                                echo "<option value=''>Erro ao conectar ao banco de dados</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="origem" value="admin">
                <div class="row g-3 mt-2 text-center">
                    <button type="submit" class="btn btn-primary fw-bold">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script>
        // Máscara para CPF
        new Cleave('#cpf', {
            delimiters: ['.', '.', '-'],
            blocks: [3, 3, 3, 2],
            numericOnly: true
        });

        // Máscara para telefone (igual ao cadastropac.html)
        new Cleave('#telefone', {
            delimiters: ['(', ') ', '-'],
            blocks: [0, 2, 5, 4],
            numericOnly: true
        });
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

        // Exibe toast se houver mensagem na URL (para redirecionamentos)
        (function () {
            const params = new URLSearchParams(window.location.search);
            const msg = params.get('toast');
            const type = params.get('type') || 'primary';
            if (msg) showAlert(type, msg);
        })();

        document.getElementById("cadastroEnfForm").addEventListener("submit", function (event) {
            event.preventDefault();
            const form = this;
            const formData = new FormData(form);
            fetch(form.action, {
                method: "POST",
                body: formData
            })
            .then(response => response.json().catch(() => ({success: false, message: "Erro inesperado do servidor."})))
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => { form.reset(); }, 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', "Ocorreu um erro ao cadastrar. Tente novamente.");
            });
        });
    </script>
</body>

</html>