<?php
session_start();
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
$stmt->close(); 

// Verifica se o usuário já respondeu à pesquisa do grupo especial
$mostrar_modal_grupo = false;
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $sql_grupo = "SELECT grupo FROM grupo_especial WHERE id_usuario = ?";
    $stmt_grupo = $conn->prepare($sql_grupo);
    $stmt_grupo->bind_param("i", $id_usuario);
    $stmt_grupo->execute();
    $res_grupo = $stmt_grupo->get_result();
    if ($res_grupo->num_rows === 0) {
        $mostrar_modal_grupo = true;
    }
    $stmt_grupo->close();
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
        html,
        body {
            /* overflow: hidden; */ /* Remova para permitir rolagem se necessário */
            height: 100%;
        }

        body {
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
        }

        .cards-outer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 0;
            margin-top: 0;
            /* Remove o espaçamento extra */
            background: #fdfdfd;
            width: 100%;
            height: auto;
        }

        .container.mt-4 {
            margin-bottom: 0 !important;
            /* Garante que não haja espaço extra após o carrossel */
        }

        .cards-container {
            background: #fdfdfd;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            padding: 40px 32px 32px 32px;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            margin-top: 16px;
            /* Aproxima os cards do carrossel */
        }

        .row-cards {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: nowrap;
            width: 100%;
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

        @media (max-width: 1200px) {
            .cards-container {
                max-width: 98vw;
                padding: 24px 4px;
            }
            .row-cards {
                gap: 16px;
            }
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

        .navbar {
            position: relative;
        }

        .navbar-content-center {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 100%;
            gap: 32px;
            /* Espaço igual entre linhas e logo */
        }

        .navbar-logo-center {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 55px;
            z-index: 2;
            /* Remover margin da logo */
        }

        .navbar-logo-center img {
            margin: 0 !important;
        }

        .navbar-hr-left,
        .navbar-hr-right {
            border-top: 2px solid #fff;
            opacity: 0.5;
            height: 0;
            flex: 1 1 0%;
            min-width: 0;
            max-width: 500px;
            width: 100%;
        }

        /* Remover margin-right e margin-left das linhas */
        .navbar-hr-left,
        .navbar-hr-right {
            margin: 0 !important;
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
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
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

<body>
    <!-- Navbar igual à de medica/telainicio.php -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="navbar-content-center">
                <div class="d-none d-md-flex navbar-hr-left"></div>
                <div class="navbar-logo-center">
                    <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="50" height="50" class="me-3">
                </div>
                <div class="d-none d-md-flex navbar-hr-right"></div>
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

    <!-- Modal Grupo Especial -->
    <div class="modal fade" id="modalGrupoEspecial" tabindex="-1" aria-labelledby="modalGrupoEspecialLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="form-grupo-especial">
            <div class="modal-header">
              <h5 class="modal-title" id="modalGrupoEspecialLabel">Grupo Especial</h5>
            </div>
            <div class="modal-body">
              <p class="mb-3">Você faz parte de algum grupo especial?</p>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="grupo_especial" id="grupoImunodeprimido" value="Imunodeprimido" required>
                <label class="form-check-label" for="grupoImunodeprimido">
                  Imunodeprimido
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="grupo_especial" id="grupoGestante" value="Gestante" required>
                <label class="form-check-label" for="grupoGestante">
                  Gestante
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="grupo_especial" id="grupoIndigena" value="Indígena" required>
                <label class="form-check-label" for="grupoIndigena">
                  Indígena
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="grupo_especial" id="grupoRenal" value="Doença renal crônica" required>
                <label class="form-check-label" for="grupoRenal">
                  Doença renal crônica
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="grupo_especial" id="grupoNenhum" value="Nenhum" required>
                <label class="form-check-label" for="grupoNenhum">
                  Não me encaixo nesses grupos
                </label>
              </div>
              <div id="grupo-erro" class="text-danger mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary w-100">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };

        // Exibe o modal se necessário
        <?php if ($mostrar_modal_grupo): ?>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = new bootstrap.Modal(document.getElementById('modalGrupoEspecial'), { backdrop: 'static', keyboard: false });
            modal.show();

            document.getElementById('form-grupo-especial').addEventListener('submit', function (e) {
                e.preventDefault();
                var grupo = document.querySelector('input[name="grupo_especial"]:checked');
                var erroDiv = document.getElementById('grupo-erro');
                erroDiv.style.display = 'none';
                if (!grupo) {
                    erroDiv.textContent = "Selecione uma opção.";
                    erroDiv.style.display = 'block';
                    return;
                }
                var valor = grupo.value;
                fetch('salvar_grupo_especial.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'grupo=' + encodeURIComponent(valor)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        modal.hide();
                    } else {
                        erroDiv.textContent = data.message || "Erro ao salvar.";
                        erroDiv.style.display = 'block';
                    }
                })
                .catch(() => {
                    erroDiv.textContent = "Erro ao salvar.";
                    erroDiv.style.display = 'block';
                });
            });
        });
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>