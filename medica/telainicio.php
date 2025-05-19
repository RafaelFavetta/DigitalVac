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
            min-height: 80px;
        }

        .navbar-logo-center {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80px;
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
            right: 24px;
            left: auto;
            bottom: auto;
        }
    </style>
</head>

<body style="background: #fdfdfd;">
    <!-- Decoração temática de saúde/vacinação -->
    <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:0;">
        <!-- Faixa azul clara superior -->
        <div style="position:absolute;top:48px;left:0;width:100vw;height:32px;
            background:linear-gradient(90deg,#0d6efd 80%,rgba(13,110,253,0.2));
            opacity:0.92;"></div>
        <!-- Ícone seringa canto superior esquerdo -->
        <i class="bi bi-syringe" style="position:absolute;top:86px;left:24px;font-size:48px;color:#0d6efd;opacity:0.38;"></i>
        <!-- Ícone escudo canto superior direito -->
        <i class="bi bi-shield-plus" style="position:absolute;top:86px;right:32px;font-size:54px;color:#0d6efd;opacity:0.38;"></i>
        <!-- Ícone coração centro-esquerda topo -->
        <i class="bi bi-heart-pulse" style="position:absolute;top:118px;left:90px;font-size:44px;color:#0d6efd;opacity:0.22;"></i>
        <!-- Ícone vacina centro-direita topo -->
        <i class="bi bi-capsule" style="position:absolute;top:118px;right:90px;font-size:44px;color:#0d6efd;opacity:0.22;"></i>
        <!-- Linha azul suave diagonal topo esquerda -->
        <div style="position:absolute;top:138px;left:-40px;width:260px;height:5px;
            background:linear-gradient(90deg,rgba(13,110,253,0.25),#0d6efd 80%,rgba(13,110,253,0.1));
            opacity:0.32;transform:rotate(-8deg);border-radius:3px;"></div>
        <!-- Linha azul suave diagonal topo direita -->
        <div style="position:absolute;top:168px;right:-40px;width:260px;height:5px;
            background:linear-gradient(270deg,rgba(13,110,253,0.25),#0d6efd 80%,rgba(13,110,253,0.1));
            opacity:0.32;transform:rotate(8deg);border-radius:3px;"></div>
        <!-- Ícone escudo canto inferior direito -->
        <i class="bi bi-shield-plus" style="position:absolute;bottom:38px;right:32px;font-size:54px;color:#0d6efd;opacity:0.38;"></i>
        <!-- Ícone coração centro-esquerda -->
        <i class="bi bi-heart-pulse" style="position:absolute;top:55%;left:18px;font-size:44px;color:#0d6efd;opacity:0.28;"></i>
        <!-- Ícone vacina centro-direita -->
        <i class="bi bi-capsule" style="position:absolute;top:70%;right:18px;font-size:44px;color:#0d6efd;opacity:0.28;"></i>
        <!-- Linha azul suave diagonal meio esquerda -->
        <div style="position:absolute;top:60%;left:-60px;width:320px;height:5px;
            background:linear-gradient(90deg,rgba(13,110,253,0.25),#0d6efd 80%,rgba(13,110,253,0.1));
            opacity:0.32;transform:rotate(-8deg);border-radius:3px;"></div>
        <!-- Linha azul suave diagonal meio direita -->
        <div style="position:absolute;top:75%;right:-60px;width:320px;height:5px;
            background:linear-gradient(270deg,rgba(13,110,253,0.25),#0d6efd 80%,rgba(13,110,253,0.1));
            opacity:0.32;transform:rotate(8deg);border-radius:3px;"></div>
    </div>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="navbar-content-center">
                <div class="d-none d-md-flex navbar-hr-left"></div>
                <div class="navbar-logo-center">
                    <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="50" height="50">
                </div>
                <div class="d-none d-md-flex navbar-hr-right"></div>
            </div>
            <ul class="navbar-nav ms-auto position-absolute end-0 me-3" style="z-index:2; top:16px;">
                <li class="nav-item">
                    <a class="btn btn-danger fw-bold" href="../outros/sair.php">
                        <i class="bi bi-box-arrow-right" style="font-size: 20px;"></i> Sair
                    </a>
                </li>
            </ul>
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
                    <i class="bi bi-person-plus" style="font-size: 50px;"></i>
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
                    <i class="bi bi-file-earmark-medical" style="font-size: 50px;"></i>
                    Cadastrar Atestado
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>