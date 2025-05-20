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

        /* Cards grid centralizado e agrupado (padrão medica/telainicio.php) */
        .cards-outer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* min-height: 60vh; */ /* Removido para evitar scroll extra */
            margin-top: 36px;
            margin-bottom: 16px;
            background: #fdfdfd;
        }

        .cards-container {
            background: #fdfdfd;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            padding: 32px 24px 24px 24px;
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

        .carousel-inner img {
            height: 360px !important;
            object-fit: cover;
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

    <div class="container mt-3" style="margin-top: 32px;">
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
                                style="height: 260px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="../img/imagem2.jpg" class="d-block w-100" alt="Imagem 2"
                                style="height: 260px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="../img/imagem3.jpg" class="d-block w-100" alt="Imagem 3"
                                style="height: 260px; object-fit: cover;">
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
    </div>

    <!-- Cards centralizados padrão medica/telainicio.php -->
    <div class="cards-outer">
        <div class="cards-container">
            <div class="row-cards">
                <a href="perfilU.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-person-fill" style="font-size: 50px;"></i>
                    Perfil
                </a>
                <a href="carteira_vac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-postcard-heart-fill" style="font-size: 50px;"></i>
                    Carteira de Vacina
                </a>
                <a href="proxima_vac.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-calendar2-week-fill" style="font-size: 50px;"></i>
                    Próximas Vacinas
                </a>
                <a href="atestado_medico.php"
                    class="btn btn-primary btn-lg d-flex flex-column align-items-center p-4 fw-bold shadow-sm card-btn">
                    <i class="bi bi-clipboard-heart-fill" style="font-size: 50px;"></i>
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