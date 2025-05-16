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

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="form-container">
            <h2 class="text-primary fw-bold text-center">Cadastro de Médico</h2>
            <form action="../Recebedados/recebedadosenfermeiro.php" method="POST">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="nome_medico" class="form-label fw-bold">Nome Completo</label>
                        <input type="text" id="nome_medico" name="nome_medico" class="form-control"
                            placeholder="Nome Completo" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telefone" class="form-label fw-bold">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" maxlength="11" minlength="11"
                            class="form-control" placeholder="Telefone" required>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="data_nascimento" class="form-label fw-bold">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="coren_crm" class="form-label fw-bold">COREN/CRM</label>
                        <input type="text" id="coren_crm" name="coren_crm" maxlength="15" minlength="13"
                            class="form-control" placeholder="COREN/CRM-UF 000000" required>
                    </div>
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
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="id_posto_trabalho" class="form-label fw-bold">Posto de Trabalho</label>
                        <select id="id_posto_trabalho" name="posto_trabalho" class="form-select" required>
                            <option value=""></option>
                            <?php
                            include('../outros/db_connect.php'); // Ensure the database connection file is included
                            
                            // Check if the connection is successful
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

                                $conn->close(); // Close the database connection
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
</body>

</html>