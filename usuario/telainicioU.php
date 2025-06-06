<?php
require_once("../outros/db_connect.php");
$imagens = [];
$hoje = date('Y-m-d');
$sql = "SELECT imagem FROM campanha WHERE imagem IS NOT NULL AND imagem <> '' AND data_fim >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hoje);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagens[] = $row['imagem'];
    }
}
$conn->close();
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
        /* Apenas ajuste para padronizar tamanho das imagens do carrossel */
        .carousel-inner img,
        .campanha-img-unica {
            width: 100% !important;
            max-width: 100% !important;
            height: 340px !important;
            object-fit: cover !important;
            border-radius: 8px;
        }

        /* Cards grid centralizado e agrupado (igual medica/telainicio.php) */
        .cards-outer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70vh;
            margin-top: 40px;
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
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: nowrap;
        }

        .row-cards:last-child {
            margin-bottom: 0;
        }

        .card-btn {
            min-width: 180px;
            max-width: 220px;
            width: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 160px;
            padding: 0;
        }

        .card-btn i {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .card-btn span {
            display: block;
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
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
                flex-wrap: wrap;
            }

            .card-btn {
                min-width: 0;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary navbar-dark">
        <div class="container-fluid">
            <div class="d-flex flex-grow-1 align-items-center justify-content-center position-relative">
                <div class="d-none d-md-block flex-grow-1 border-top border-2 border-white opacity-50 me-3"></div>
                <div class="d-flex align-items-center justify-content-center" style="height:55px;z-index:2;">
                    <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="50" height="50" class="me-3">
                </div>
                <div class="d-none d-md-block flex-grow-1 border-top border-2 border-white opacity-50 ms-3"></div>
            </div>
            <ul class="navbar-nav ms-auto position-absolute end-0 me-3" style="z-index:2; top:16px;">
                <li class="nav-item">
                    <a class="btn btn-danger fw-bold px-2 py-1" style="font-size: 15px; min-width: 70px;"
                        href="../outros/sair.php">
                        <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <?php if (count($imagens) === 1): ?>
                    <img src="../img/<?php echo htmlspecialchars($imagens[0]); ?>" alt=""
                        class="campanha-img-unica shadow-sm">
                <?php elseif (count($imagens) > 1): ?>
                    <div id="carouselCampanha" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                        <div class="carousel-indicators">
                            <?php foreach ($imagens as $idx => $img): ?>
                                <button type="button" data-bs-target="#carouselCampanha" data-bs-slide-to="<?php echo $idx; ?>"
                                    <?php if ($idx === 0)
                                        echo 'class="active" aria-current="true"'; ?>
                                    aria-label="Slide <?php echo $idx + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($imagens as $idx => $img): ?>
                                <div class="carousel-item<?php if ($idx === 0)
                                    echo ' active'; ?>">
                                    <img src="../img/<?php echo htmlspecialchars($img); ?>" class="d-block w-100"
                                        alt="Campanha <?php echo $idx + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselCampanha"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselCampanha"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cards centralizados padrão medica/telainicio.php -->
    <?php $semImagem = count($imagens) === 0; ?>
    <div class="cards-outer<?php if ($semImagem) echo ' min-vh-100'; ?>">
        <div class="cards-container">
            <div class="row-cards">
                <a href="perfilU.php"
                    class="btn btn-primary btn-lg card-btn shadow-sm">
                    <i class="bi bi-person-fill"></i>
                    <span>Perfil</span>
                </a>
                <a href="carteira_vac.php"
                    class="btn btn-primary btn-lg card-btn shadow-sm">
                    <i class="bi bi-postcard-heart-fill"></i>
                    <span>Carteira de Vacina</span>
                </a>
                <a href="proxima_vac.php"
                    class="btn btn-primary btn-lg card-btn shadow-sm">
                    <i class="bi bi-calendar2-week-fill"></i>
                    <span>Próximas Vacinas</span>
                </a>
                <a href="atestado_medico.php"
                    class="btn btn-primary btn-lg card-btn shadow-sm">
                    <i class="bi bi-clipboard-heart-fill"></i>
                    <span>Atestados</span>
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