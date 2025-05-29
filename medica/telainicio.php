<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

$erro = "";

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
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
    <style>
        .navbar {
            position: relative;
        }

        .navbar-logo-center {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 55px;
            z-index: 2;
        }

        .navbar-hr-left,
        .navbar-hr-right {
            border-top: 2px solid #fff;
            opacity: 0.5;
            margin: 0 105px;
            height: 0;
        }

        .navbar-hr-left {
            flex: 1 1 0%;
            margin-right: 24px;
        }

        .navbar-hr-right {
            flex: 1 1 0%;
            margin-left: 24px;
        }

        .navbar-content-center {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            position: relative;
        }

        @media (max-width: 991.98px) {
            .navbar-content-center {
                flex-direction: column;
            }

            .navbar-hr-left,
            .navbar-hr-right {
                display: none;
            }
        }

        /* Cards grid centralizado e agrupado */
        .cards-outer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70vh;
            margin-top: 90px;
            background: #fdfdfd;
        }

        .cards-container {
            background: #fdfdfd;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            padding: 40px 32px 32px 32px;
            max-width: 800px;
            margin: 0 auto;
        }

        .row-cards {
            display: flex;
            justify-content: center;
            gap: 32px;
            margin-bottom: 32px;
        }

        .row-cards:last-child {
            margin-bottom: 0;
        }

        .card-btn {
            min-width: 220px;
            max-width: 260px;
            width: 100%;
            margin: 0;
        }

        @media (max-width: 991.98px) {
            .cards-container {
                padding: 24px 8px;
            }

            .row-cards {
                flex-direction: column;
                gap: 20px;
                align-items: center;
            }

            .card-btn {
                min-width: 0;
                max-width: 100%;
            }
        }

        .navbar-nav.ms-auto.position-absolute.end-0.me-3 {
            top: 50% !important;
            transform: translateY(-50%);
            right: 12px;
            left: auto;
            bottom: auto;
        }

        .navbar .btn-danger {
            padding: 4px 10px !important;
            font-size: 15px !important;
            min-width: 70px;
        }

        .navbar .nav-link {
            white-space: nowrap;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .navbar-brand {
            font-size: 1.25rem !important;
        }

        .navbar-nav .nav-link {
            font-size: 1rem !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                font-size: 1rem !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
        }
    </style>
</head>

<body style="background: #fdfdfd;">
    <?php if (isset($_GET['atestado_id']) && is_numeric($_GET['atestado_id'])): ?>
    <div id="atestado-toast" style="position: fixed; top: 24px; right: 24px; z-index: 2000; min-width: 320px; max-width: 400px;">
        <div class="alert alert-success alert-dismissible fade show shadow" role="alert" style="display: flex; align-items: center;">
            <div style="flex:1;">
                <strong>Atestado cadastrado!</strong><br>
                O atestado foi gerado com sucesso.
            </div>
            <div class="ms-3 d-flex flex-column gap-1">
                <a href="download_atestado.php?id=<?php echo intval($_GET['atestado_id']); ?>" class="btn btn-primary btn-sm">
                    Baixar atestado
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-1" onclick="document.getElementById('atestado-toast').remove();">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="40" height="40">
                <a class="navbar-brand fs-5 fw-bold ms-2">DigitalVac</a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-semibold px-2" href="telainicio.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="cadastroaplic.html">
                        <i class="bi bi-clipboard2-heart-fill"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="listavac.php">
                        <i class="bi bi-list"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="cadastroatestado.html">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-semibold px-2" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Meus Atestados
                    </a>
                </div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger fw-bold px-2 py-1 ms-2" style="font-size: 15px; min-width: 70px;" href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="cards-outer">
        <div class="cards-container">
            <div class="row-cards">
                <a href="cadastroaplic.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-clipboard2-heart-fill" style="font-size: 50px;"></i>
                    Aplicação de Vacinas
                </a>
                <a href="cadastropac.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-person-plus-fill" style="font-size: 50px;"></i>
                    Cadastrar Pacientes
                </a>
                <a href="listavac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-list" style="font-size: 50px;"></i>
                    Lista de Vacinas
                </a>
            </div>
            <div class="row-cards">
                <a href="pesquisa_paciente.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-person-lines-fill" style="font-size: 50px;"></i>
                    Pesquisar Pacientes
                </a>
                <a href="cadastroatestado.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-clipboard2-plus-fill" style="font-size: 50px;"></i>
                    Cadastrar Atestado
                </a>
                <a href="atestado_medico.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-clipboard-heart-fill" style="font-size: 50px;"></i>
                    Meus Atestados
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>