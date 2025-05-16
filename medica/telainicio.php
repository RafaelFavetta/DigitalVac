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
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center w-100 justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="40" height="40">
                    <a class="navbar-brand fs-4 fw-bold ms-2 text-white">DigitalVac</a>
                </div>
                <div class="mx-auto text-white fw-bold" style="font-size: 1.2rem;">
                    Bem-vindo ao DigitalVac
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

    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="row row-cols-1 row-cols-md-2 g-4 w-100" style="max-width: 700px;">
            <div class="col d-flex justify-content-center">
                <a href="cadastroaplic.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm w-100">
                    <i class="bi bi-clipboard2-heart-fill" style="font-size: 50px;"></i>
                    Aplicação de Vacinas
                </a>
            </div>
            <div class="col d-flex justify-content-center">
                <a href="cadastropac.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm w-100">
                    <i class="bi bi-person-plus" style="font-size: 50px;"></i>
                    Cadastrar Pacientes
                </a>
            </div>
            <div class="col d-flex justify-content-center">
                <a href="listavac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm w-100">
                    <i class="bi bi-list" style="font-size: 50px;"></i>
                    Lista de Vacinas
                </a>
            </div>
            <div class="col d-flex justify-content-center">
                <a href="pesquisa_paciente.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm w-100">
                    <i class="bi bi-person-lines-fill" style="font-size: 50px;"></i>
                    Pesquisar Pacientes
                </a>
            </div>
            <div class="col d-flex justify-content-center">
                <a href="cadastroatestado.html"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm w-100">
                    <i class="bi bi-clipboard2-plus-fill" style="font-size: 50px;"></i>
                    Cadastrar Atestado
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>