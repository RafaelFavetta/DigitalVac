<?php
include('../outros/db_connect.php');
include('../Recebedados/validacoes.php'); // Include validation functions
session_start();

if (!isset($_SESSION['id_medico'])) {
    header("Location: login.php");
    exit();
}

// Campo de pesquisa por nome da vacina
$nome_vacina = '';
if (isset($_GET['nome_vacina'])) {
    $nome_vacina = trim($_GET['nome_vacina']);
}

// Monta a consulta SQL com filtro por nome da vacina, se fornecido
if (!empty($nome_vacina)) {
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, via_adimicao, n_dose, intervalo_dose, estoque, idade_reco, sus 
            FROM vacina WHERE nome_vaci LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_param = '%' . $nome_vacina . '%';
    $stmt->bind_param('s', $like_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id_vaci, nome_vaci, fabri_vaci, lote_vaci, via_adimicao, n_dose, intervalo_dose, estoque, idade_reco, sus 
            FROM vacina";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Erro ao buscar vacinas: " . $conn->error);
}

// Após obter $result, crie arrays separadas para obrigatórias e opcionais
$vacinas_obrigatorias = [];
$vacinas_opcionais = [];
while ($row = $result->fetch_assoc()) {
    $nome = $row['nome_vaci'];
    $idade_reco = isset($row['idade_reco']) ? mb_strtolower(trim($row['idade_reco'])) : '';
    if (
        stripos($nome, 'VSR') !== false ||
        stripos($nome, 'Raiva') !== false ||
        stripos($nome, 'viajantes') !== false ||
        $idade_reco === 'a qualquer momento'
    ) {
        $idade_ordenacao = -2; // "A qualquer momento"
    } elseif (
        ($row['id_vaci'] == 22 || $row['id_vaci'] == 23) || $idade_reco === 'ao nascer' || $idade_reco === '0 meses'
    ) {
        $idade_ordenacao = -1; // "Ao nascer"
    } elseif (
        stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false || $idade_reco === '50 anos'
    ) {
        $idade_ordenacao = 50 * 12; // 50 anos
    } elseif (
        stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false || $idade_reco === '10 anos'
    ) {
        $idade_ordenacao = 10 * 12; // 10 anos
    } elseif (
        stripos($nome, 'HPV') !== false || $idade_reco === '9 anos'
    ) {
        $idade_ordenacao = 9 * 12; // 9 anos
    } elseif (
        stripos($nome, 'Influenza') !== false || $idade_reco === '9 anos'
    ) {
        $idade_ordenacao = 9 * 12; // 9 anos
    } elseif (
        stripos($nome, 'dTpa (adulto/gestante)') !== false || $idade_reco === '18 anos'
    ) {
        $idade_ordenacao = 18 * 12; // 18 anos
    } elseif (
        stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
    ) {
        $idade_ordenacao = 18 * 12; // 18 anos
    } else {
        if (preg_match('/(\d+)\s*mes/i', $idade_reco, $m)) {
            $idade_ordenacao = intval($m[1]);
        } elseif (preg_match('/(\d+)\s*ano/i', $idade_reco, $m)) {
            $idade_ordenacao = intval($m[1]) * 12;
        } else {
            $idade_ordenacao = 9999;
        }
    }
    $row['idade_ordenacao'] = $idade_ordenacao;

    // Classifica em obrigatória ou opcional
    if (isset($row['sus']) ? intval($row['sus']) === 1 : false) {
        $vacinas_obrigatorias[] = $row;
    } else {
        $vacinas_opcionais[] = $row;
    }
}
// Ordena cada grupo por idade_ordenacao
usort($vacinas_obrigatorias, function($a, $b) {
    return $a['idade_ordenacao'] <=> $b['idade_ordenacao'];
});
usort($vacinas_opcionais, function($a, $b) {
    return $a['idade_ordenacao'] <=> $b['idade_ordenacao'];
});
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }

        .table {
            background: #FDFDFD;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table thead th {
            background-color: #0d6efd !important;
            color: #FDFDFD !important;
            font-weight: bold;
        }

        .bg-fdfdfd { background-color: #FDFDFD !important; }
        .table-secondary { background-color: #f3f4f6 !important; }
    </style>
</head>

<body>
    <!-- Navbar padronizada -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active fs-6 fw-bold" href="telainicio.php">
                        <i class="bi bi-house-fill" style="font-size: 20px"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.php">
                        <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill" style="font-size: 20px"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="listavac.php">
                        <i class="bi bi-list" style="font-size: 20px"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill" style="font-size: 20px"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.php">
                        <i class="bi bi-clipboard2-plus-fill" style="font-size: 20px"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill" style="font-size: 20px"></i> Meus Atestados
                    </a>
                </div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger fw-bold px-2 py-1" style="font-size: 15px; min-width: 70px;"
                            href="../outros/sair.php">
                            <i class="bi bi-box-arrow-right" style="font-size: 18px;"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center text-primary fw-bold">Lista de Vacinas</h2>
        <div class="container-fluid col-md-6 mt-4">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-vacina">
                <input class="form-control me-2 border border-primary" type="search" name="nome_vacina"
                    placeholder="Digite o nome da vacina" value="<?php echo htmlspecialchars($nome_vacina); ?>"
                    id="input-nome-vacina" autocomplete="off">
            </form>
        </div>
        <br>
        <div id="tabela-vacinas" class="d-flex justify-content-center">
            <div class="w-100">
                <?php if (count($vacinas_obrigatorias) > 0): ?>
                <div class="border border-primary rounded-3 mb-4 p-2 shadow-sm" style="background-color: #eaf4ff;">
                    <h5 class="text-primary text-center mb-2 fw-bold">
                        <i class="bi bi-shield-check"></i> Vacinas Obrigatórias (SUS)
                    </h5>
                    <table class="table table-bordered text-center mx-auto">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Fabricante</th>
                                <th>Lote</th>
                                <th>Idade Aplicação</th>
                                <th>Via de Administração</th>
                                <th>Número de Doses</th>
                                <th>Intervalo entre Doses (meses)</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            usort($vacinas_obrigatorias, function($a, $b) {
                                return $a['idade_ordenacao'] <=> $b['idade_ordenacao'];
                            });
                            $rowIndex = 0;
                            foreach ($vacinas_obrigatorias as $row):
                                $rowClass = ($rowIndex % 2 === 0) ? 'bg-fdfdfd' : 'table-secondary';
                                $modalId = "modalVacina" . $row['id_vaci'];
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['lote_vaci']); ?></td>
                                <td>
                                    <?php
                                        // Exibe "A qualquer momento" ou idade especial para vacinas específicas
                                        $nome = $row['nome_vaci'];
                                        $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                        if (
                                            stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                        ) {
                                            echo "50 anos";
                                        } elseif (
                                            stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                        ) {
                                            echo "10 anos";
                                        } elseif (
                                            stripos($nome, 'HPV') !== false
                                        ) {
                                            echo "9 anos";
                                        } elseif (
                                            stripos($nome, 'Influenza') !== false
                                        ) {
                                            echo "9 anos";
                                        } elseif (
                                            stripos($nome, 'Hepatite B (adulto)') !== false
                                        ) {
                                            echo "18 anos";
                                        } elseif (
                                            stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                        ) {
                                            echo "18 anos";
                                        } elseif (
                                            stripos($nome, 'Febre amarela') !== false
                                        ) {
                                            echo "5 anos";
                                        } elseif (
                                            stripos($nome, 'Pneumocócica 23-valente') !== false
                                        ) {
                                            echo "5 anos";
                                        } elseif (
                                            stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                        ) {
                                            echo "2 meses";
                                        } elseif (
                                            stripos($nome, 'dT') !== false
                                        ) {
                                            echo "7 anos";
                                        } elseif (
                                            stripos($nome, 'VSR') !== false ||
                                            stripos($nome, 'Raiva') !== false ||
                                            stripos($nome, 'viajantes') !== false
                                        ) {
                                            echo "A qualquer momento";
                                        } elseif (
                                            mb_strtolower($idade_reco) === '0 meses'
                                        ) {
                                            echo "Ao nascer";
                                        } else {
                                            // Exibe idade recomendada da vacina, sem cálculos extras
                                            echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                        }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['via_adimicao']); ?></td>
                                <td><?php echo htmlspecialchars($row['n_dose']); ?></td>
                                <td><?php echo htmlspecialchars($row['intervalo_dose']); ?></td>
                                <td><?php echo htmlspecialchars($row['estoque']); ?></td>
                                <td>
                                    <div style="display: flex; flex-direction: row; gap: 6px; justify-content: center; align-items: center;">
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editVacina<?php echo $row['id_vaci']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                    <!-- Modal de informações -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="label<?php echo $modalId; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                          <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                            <h5 class="modal-title fw-bold" id="label<?php echo $modalId; ?>">
                                                <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                                <span class="ms-2">Informações da Vacina</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body px-2 px-md-4 py-3">
                                            <div class="row g-3 g-md-4">
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Nome:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['nome_vaci']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-building"></i> Fabricante:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['fabri_vaci']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Lote:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['lote_vaci']); ?></span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Aplicação:</span><br>
                                                  <span class="fs-6">
                                                  <?php
                                                      $nome = $row['nome_vaci'];
                                                      $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                                      if (
                                                          stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                                      ) {
                                                          echo "50 anos";
                                                      } elseif (
                                                          stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                                      ) {
                                                          echo "10 anos";
                                                      } elseif (
                                                          stripos($nome, 'HPV') !== false
                                                      ) {
                                                          echo "9 anos";
                                                      } elseif (
                                                          stripos($nome, 'Influenza') !== false
                                                      ) {
                                                          echo "9 anos";
                                                      } elseif (
                                                          stripos($nome, 'Hepatite B (adulto)') !== false
                                                      ) {
                                                          echo "18 anos";
                                                      } elseif (
                                                          stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                                      ) {
                                                          echo "18 anos";
                                                      } elseif (
                                                          stripos($nome, 'Febre amarela') !== false
                                                      ) {
                                                          echo "5 anos";
                                                      } elseif (
                                                          stripos($nome, 'Pneumocócica 23-valente') !== false
                                                      ) {
                                                          echo "5 anos";
                                                      } elseif (
                                                          stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                                      ) {
                                                          echo "2 meses";
                                                      } elseif (
                                                          stripos($nome, 'dT') !== false
                                                      ) {
                                                          echo "7 anos";
                                                      } elseif (
                                                          stripos($nome, 'VSR') !== false ||
                                                          stripos($nome, 'Raiva') !== false ||
                                                          stripos($nome, 'viajantes') !== false
                                                      ) {
                                                          echo "A qualquer momento";
                                                      } elseif (
                                                          mb_strtolower($idade_reco) === '0 meses'
                                                      ) {
                                                          echo "Ao nascer";
                                                      } else {
                                                          echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                      }
                                                  ?>
                                                  </span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-diagram-3"></i> Via de Administração:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['via_adimicao']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Número de Doses:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['n_dose']); ?></span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre Doses:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-box-seam"></i> Estoque:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['estoque']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                  <span class="fs-6"><?php echo ($row['sus'] == 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <!-- Modal de edição: layout igual ao de informações, mas campos editáveis -->
                                    <div class="modal fade" id="editVacina<?php echo $row['id_vaci']; ?>" tabindex="-1" aria-labelledby="editLabel<?php echo $row['id_vaci']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <form method="post" action="listavac.php" class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                                <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                                    <h5 class="modal-title fw-bold" id="editLabel<?php echo $row['id_vaci']; ?>">
                                                        <i class="bi bi-pencil" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                                        <span class="ms-2">Editar Vacina</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                </div>
                                                <div class="modal-body px-2 px-md-4 py-3">
                                                    <input type="hidden" name="id_vaci" value="<?php echo $row['id_vaci']; ?>">
                                                    <div class="row g-3 g-md-4">
                                                        <!-- COLUNA 1 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Nome:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['nome_vaci']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-building"></i> Fabricante:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['fabri_vaci']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Lote:</span><br>
                                                                <input type="text" class="form-control" name="lote_vaci" value="<?php echo htmlspecialchars($row['lote_vaci']); ?>" maxlength="50" required>
                                                            </div>
                                                        </div>
                                                        <!-- COLUNA 2 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Aplicação:</span><br>
                                                                <span class="fs-6">
                                                                <?php
                                                                    $nome = $row['nome_vaci'];
                                                                    $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                                                    if (
                                                                        stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                                                    ) {
                                                                        echo "50 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                                                    ) {
                                                                        echo "10 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'HPV') !== false
                                                                    ) {
                                                                        echo "9 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Influenza') !== false
                                                                    ) {
                                                                        echo "9 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Hepatite B (adulto)') !== false
                                                                    ) {
                                                                        echo "18 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                                                    ) {
                                                                        echo "18 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Febre amarela') !== false
                                                                    ) {
                                                                        echo "5 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Pneumocócica 23-valente') !== false
                                                                    ) {
                                                                        echo "5 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                                                    ) {
                                                                        echo "2 meses";
                                                                    } elseif (
                                                                        stripos($nome, 'dT') !== false
                                                                    ) {
                                                                        echo "7 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'VSR') !== false ||
                                                                        stripos($nome, 'Raiva') !== false ||
                                                                        stripos($nome, 'viajantes') !== false
                                                                    ) {
                                                                        echo "A qualquer momento";
                                                                    } elseif (
                                                                        mb_strtolower($idade_reco) === '0 meses'
                                                                    ) {
                                                                        echo "Ao nascer";
                                                                    } else {
                                                                        echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                                    }
                                                                ?>
                                                                </span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-diagram-3"></i> Via de Administração:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['via_adimicao']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Número de Doses:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['n_dose']); ?></span>
                                                            </div>
                                                        </div>
                                                        <!-- COLUNA 3 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre Doses:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-box-seam"></i> Estoque:</span><br>
                                                                <input type="number" class="form-control" name="estoque" value="<?php echo htmlspecialchars($row['estoque']); ?>" min="0" required>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                                <span class="fs-6"><?php echo ($row['sus'] == 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="background: #eaf4ff; border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                                                    <button type="submit" name="salvar_edicao" class="btn btn-primary px-5 py-2 rounded-pill fw-bold" style="font-size: 1.1rem;">Salvar</button>
                                                    <button type="button" class="btn btn-outline-secondary px-5 py-2 rounded-pill" style="font-size: 1.1rem;" data-bs-dismiss="modal">Cancelar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $rowIndex++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php if (count($vacinas_opcionais) > 0): ?>
                <div class="border border-warning rounded-3 mb-4 p-2 shadow-sm" style="background-color: #fffbe6;">
                    <h5 class="text-primary text-center mb-2 mt-4 fw-bold">
                        <i class="bi bi-patch-question"></i> Vacinas Opcionais
                    </h5>
                    <table class="table table-bordered text-center mx-auto">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Fabricante</th>
                                <th>Lote</th>
                                <th>Idade Aplicação</th>
                                <th>Via de Administração</th>
                                <th>Número de Doses</th>
                                <th>Intervalo entre Doses (meses)</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            usort($vacinas_opcionais, function($a, $b) {
                                return $a['idade_ordenacao'] <=> $b['idade_ordenacao'];
                            });
                            $rowIndex = 0;
                            foreach ($vacinas_opcionais as $row):
                                $rowClass = ($rowIndex % 2 === 0) ? 'bg-fdfdfd' : 'table-secondary';
                                $modalId = "modalVacina" . $row['id_vaci'];
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo htmlspecialchars($row['nome_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['fabri_vaci']); ?></td>
                                <td><?php echo htmlspecialchars($row['lote_vaci']); ?></td>
                                <td>
                                    <?php
                                        // Exibe "A qualquer momento" ou idade especial para vacinas específicas
                                        $nome = $row['nome_vaci'];
                                        $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                        if (
                                            stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                        ) {
                                            echo "50 anos";
                                        } elseif (
                                            stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                        ) {
                                            echo "10 anos";
                                        } elseif (
                                            stripos($nome, 'HPV') !== false
                                        ) {
                                            echo "9 anos";
                                        } elseif (
                                            stripos($nome, 'Influenza') !== false
                                        ) {
                                            echo "9 anos";
                                        } elseif (
                                            stripos($nome, 'Hepatite B (adulto)') !== false
                                        ) {
                                            echo "18 anos";
                                        } elseif (
                                            stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                        ) {
                                            echo "18 anos";
                                        } elseif (
                                            stripos($nome, 'Febre amarela') !== false
                                        ) {
                                            echo "5 anos";
                                        } elseif (
                                            stripos($nome, 'Pneumocócica 23-valente') !== false
                                        ) {
                                            echo "5 anos";
                                        } elseif (
                                            stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                        ) {
                                            echo "2 meses";
                                        } elseif (
                                            stripos($nome, 'dT') !== false
                                        ) {
                                            echo "7 anos";
                                        } elseif (
                                            stripos($nome, 'VSR') !== false ||
                                            stripos($nome, 'Raiva') !== false ||
                                            stripos($nome, 'viajantes') !== false
                                        ) {
                                            echo "A qualquer momento";
                                        } elseif (
                                            mb_strtolower($idade_reco) === '0 meses'
                                        ) {
                                            echo "Ao nascer";
                                        } else {
                                            // Exibe idade recomendada da vacina, sem cálculos extras
                                            echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                        }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['via_adimicao']); ?></td>
                                <td><?php echo htmlspecialchars($row['n_dose']); ?></td>
                                <td><?php echo htmlspecialchars($row['intervalo_dose']); ?></td>
                                <td><?php echo htmlspecialchars($row['estoque']); ?></td>
                                <td>
                                    <div style="display: flex; flex-direction: row; gap: 6px; justify-content: center; align-items: center;">
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editVacina<?php echo $row['id_vaci']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                    <!-- Modal de informações -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="label<?php echo $modalId; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                          <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                            <h5 class="modal-title fw-bold" id="label<?php echo $modalId; ?>">
                                                <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                                <span class="ms-2">Informações da Vacina</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body px-2 px-md-4 py-3">
                                            <div class="row g-3 g-md-4">
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Nome:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['nome_vaci']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-building"></i> Fabricante:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['fabri_vaci']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Lote:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['lote_vaci']); ?></span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Aplicação:</span><br>
                                                  <span class="fs-6">
                                                  <?php
                                                      $nome = $row['nome_vaci'];
                                                      $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                                      if (
                                                          stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                                      ) {
                                                          echo "50 anos";
                                                      } elseif (
                                                          stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                                      ) {
                                                          echo "10 anos";
                                                      } elseif (
                                                          stripos($nome, 'HPV') !== false
                                                      ) {
                                                          echo "9 anos";
                                                      } elseif (
                                                          stripos($nome, 'Influenza') !== false
                                                      ) {
                                                          echo "9 anos";
                                                      } elseif (
                                                          stripos($nome, 'Hepatite B (adulto)') !== false
                                                      ) {
                                                          echo "18 anos";
                                                      } elseif (
                                                          stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                                      ) {
                                                          echo "18 anos";
                                                      } elseif (
                                                          stripos($nome, 'Febre amarela') !== false
                                                      ) {
                                                          echo "5 anos";
                                                      } elseif (
                                                          stripos($nome, 'Pneumocócica 23-valente') !== false
                                                      ) {
                                                          echo "5 anos";
                                                      } elseif (
                                                          stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                                      ) {
                                                          echo "2 meses";
                                                      } elseif (
                                                          stripos($nome, 'dT') !== false
                                                      ) {
                                                          echo "7 anos";
                                                      } elseif (
                                                          stripos($nome, 'VSR') !== false ||
                                                          stripos($nome, 'Raiva') !== false ||
                                                          stripos($nome, 'viajantes') !== false
                                                      ) {
                                                          echo "A qualquer momento";
                                                      } elseif (
                                                          mb_strtolower($idade_reco) === '0 meses'
                                                      ) {
                                                          echo "Ao nascer";
                                                      } else {
                                                          echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                      }
                                                  ?>
                                                  </span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-diagram-3"></i> Via de Administração:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['via_adimicao']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Número de Doses:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['n_dose']); ?></span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre Doses:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-box-seam"></i> Estoque:</span><br>
                                                  <span class="fs-6"><?php echo htmlspecialchars($row['estoque']); ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                  <span class="fs-6"><?php echo ($row['sus'] == 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <!-- Modal de edição: layout igual ao de informações, mas campos editáveis -->
                                    <div class="modal fade" id="editVacina<?php echo $row['id_vaci']; ?>" tabindex="-1" aria-labelledby="editLabel<?php echo $row['id_vaci']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <form method="post" action="listavac.php" class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                                <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                                    <h5 class="modal-title fw-bold" id="editLabel<?php echo $row['id_vaci']; ?>">
                                                        <i class="bi bi-pencil" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                                        <span class="ms-2">Editar Vacina</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                </div>
                                                <div class="modal-body px-2 px-md-4 py-3">
                                                    <input type="hidden" name="id_vaci" value="<?php echo $row['id_vaci']; ?>">
                                                    <div class="row g-3 g-md-4">
                                                        <!-- COLUNA 1 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Nome:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['nome_vaci']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-building"></i> Fabricante:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['fabri_vaci']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Lote:</span><br>
                                                                <input type="text" class="form-control" name="lote_vaci" value="<?php echo htmlspecialchars($row['lote_vaci']); ?>" maxlength="50" required>
                                                            </div>
                                                        </div>
                                                        <!-- COLUNA 2 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Aplicação:</span><br>
                                                                <span class="fs-6">
                                                                <?php
                                                                    $nome = $row['nome_vaci'];
                                                                    $idade_reco = isset($row['idade_reco']) ? trim($row['idade_reco']) : '';
                                                                    if (
                                                                        stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                                                    ) {
                                                                        echo "50 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                                                    ) {
                                                                        echo "10 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'HPV') !== false
                                                                    ) {
                                                                        echo "9 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Influenza') !== false
                                                                    ) {
                                                                        echo "9 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Hepatite B (adulto)') !== false
                                                                    ) {
                                                                        echo "18 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                                                    ) {
                                                                        echo "18 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Febre amarela') !== false
                                                                    ) {
                                                                        echo "5 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Pneumocócica 23-valente') !== false
                                                                    ) {
                                                                        echo "5 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'Penta (DTP/Hib/Hepatite B)') !== false
                                                                    ) {
                                                                        echo "2 meses";
                                                                    } elseif (
                                                                        stripos($nome, 'dT') !== false
                                                                    ) {
                                                                        echo "7 anos";
                                                                    } elseif (
                                                                        stripos($nome, 'VSR') !== false ||
                                                                        stripos($nome, 'Raiva') !== false ||
                                                                        stripos($nome, 'viajantes') !== false
                                                                    ) {
                                                                        echo "A qualquer momento";
                                                                    } elseif (
                                                                        mb_strtolower($idade_reco) === '0 meses'
                                                                    ) {
                                                                        echo "Ao nascer";
                                                                    } else {
                                                                        echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                                    }
                                                                ?>
                                                                </span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-diagram-3"></i> Via de Administração:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['via_adimicao']); ?></span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Número de Doses:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['n_dose']); ?></span>
                                                            </div>
                                                        </div>
                                                        <!-- COLUNA 3 -->
                                                        <div class="col-12 col-md-4">
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre Doses:</span><br>
                                                                <span class="fs-6"><?php echo htmlspecialchars($row['intervalo_dose']); ?> meses</span>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-box-seam"></i> Estoque:</span><br>
                                                                <input type="number" class="form-control" name="estoque" value="<?php echo htmlspecialchars($row['estoque']); ?>" min="0" required>
                                                            </div>
                                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                                <span class="fs-6"><?php echo ($row['sus'] == 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="background: #eaf4ff; border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                                                    <button type="submit" name="salvar_edicao" class="btn btn-primary px-5 py-2 rounded-pill fw-bold" style="font-size: 1.1rem;">Salvar</button>
                                                    <button type="button" class="btn btn-outline-secondary px-5 py-2 rounded-pill" style="font-size: 1.1rem;" data-bs-dismiss="modal">Cancelar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $rowIndex++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pesquisa automática AJAX
        document.getElementById('input-nome-vacina').addEventListener('input', function () {
            const nome = this.value;
            const tabela = document.getElementById('tabela-vacinas');
            const params = new URLSearchParams({ nome_vacina: nome });
            fetch('listavac.php?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const novaTabela = temp.querySelector('#tabela-vacinas');
                    if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
                });
        });
    </script>
</body>

</html>

<?php
// Processa edição de lote e estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_edicao'])) {
    $id_vaci = intval($_POST['id_vaci']);
    $lote_vaci = trim($_POST['lote_vaci']);
    $estoque = intval($_POST['estoque']);
    if ($id_vaci > 0 && $lote_vaci !== '' && $estoque >= 0) {
        $sql_update = "UPDATE vacina SET lote_vaci = ?, estoque = ? WHERE id_vaci = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sii", $lote_vaci, $estoque, $id_vaci);
        $stmt_update->execute();
        // Redireciona para evitar reenvio do formulário
        header("Location: listavac.php?nome_vacina=" . urlencode($nome_vacina));
        exit();
    }
}