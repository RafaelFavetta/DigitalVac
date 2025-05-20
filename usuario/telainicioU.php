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

        .navbar-nav.ms-auto.position-absolute.end-0.me-3 {
            top: 50% !important;
            transform: translateY(-50%);
            right: 24px;
            left: auto;
            bottom: auto;
        }
    </style>
</head>

<body>
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>

                    <!-- Slides -->
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="../img/imagem1.jpg" class="d-block w-100" alt="Imagem 1"
                                style="height: 400px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="../img/imagem2.jpg" class="d-block w-100" alt="Imagem 2"
                                style="height: 400px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="../img/imagem3.jpg" class="d-block w-100" alt="Imagem 3"
                                style="height: 400px; object-fit: cover;">
                        </div>
                    </div>

                    <!-- Controles -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="row text-center mt-5" style="margin-top: 4cm !important;">
            <div class="col-md-3">
                <a href="perfilU.php" class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                        class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg> Perfil
                </a>
            </div>
            <div class="col-md-3">
                <a href="carteira_vac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                        class="bi bi-postcard-heart-fill" viewBox="0 0 16 16">
                        <path
                            d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm6 2.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0m3.5.878c1.482-1.42 4.795 1.392 0 4.622-4.795-3.23-1.482-6.043 0-4.622M2 5.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                    </svg>
                    Carteira de vacina
                </a>
            </div>
            <div class="col-md-3">
                <a href="proxima_vac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                        class="bi bi-calendar2-week-fill" viewBox="0 0 16 16">
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5m9.954 3H2.545c-.3 0-.545.224-.545.5v1c0 .276.244.5.545.5h10.91c.3 0 .545-.224.545-.5v-1c0-.276-.244-.5-.546-.5M8.5 7a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM3 10.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5m3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                    </svg>
                    Próximas vacinas
                </a>
            </div>
            <div class="col-md-3">
                <a href="atestado_medico.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                        class="bi bi-clipboard-heart-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z" />
                        <path fill-rule="evenodd"
                            d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zm4 5.982c1.664-1.673 5.825 1.254 0 5.018-5.825-3.764-1.664-6.69 0-5.018" />
                    </svg>
                    Atestados
                </a>
            </div>
        </div>
    </div>
    <script>
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>