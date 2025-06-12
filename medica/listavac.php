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
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table thead th {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }
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
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroaplic.php">
                        <i class="bi bi-clipboard2-heart-fill"></i> Aplicação de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
                        <i class="bi bi-person-plus-fill"></i> Cadastrar Pacientes
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="listavac.php">
                        <i class="bi bi-list"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.php">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart"></i> Meus Atestados
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
                                $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
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
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="label<?php echo $modalId; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                          <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="label<?php echo $modalId; ?>">Informações da Vacina</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body text-start">
                                            <strong>Nome:</strong> <?php echo htmlspecialchars($row['nome_vaci']); ?><br>
                                            <strong>Fabricante:</strong> <?php echo htmlspecialchars($row['fabri_vaci']); ?><br>
                                            <strong>Lote:</strong> <?php echo htmlspecialchars($row['lote_vaci']); ?><br>
                                            <strong>Idade Aplicação:</strong> <?php
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
                                                } else {
                                                    // Exibe idade recomendada da vacina, sem cálculos extras
                                                    echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                }
                                            ?><br>
                                            <strong>Via de Administração:</strong> <?php echo htmlspecialchars($row['via_adimicao']); ?><br>
                                            <strong>Número de Doses:</strong> <?php echo htmlspecialchars($row['n_dose']); ?><br>
                                            <strong>Intervalo entre Doses:</strong> <?php echo htmlspecialchars($row['intervalo_dose']); ?> meses<br>
                                            <strong>Estoque:</strong> <?php echo htmlspecialchars($row['estoque']); ?><br>
                                            <strong>Obrigatória SUS:</strong> Sim
                                          </div>
                                        </div>
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
                                $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
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
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="label<?php echo $modalId; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                          <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="label<?php echo $modalId; ?>">Informações da Vacina</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body text-start">
                                            <strong>Nome:</strong> <?php echo htmlspecialchars($row['nome_vaci']); ?><br>
                                            <strong>Fabricante:</strong> <?php echo htmlspecialchars($row['fabri_vaci']); ?><br>
                                            <strong>Lote:</strong> <?php echo htmlspecialchars($row['lote_vaci']); ?><br>
                                            <strong>Idade Aplicação:</strong> <?php
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
                                                } else {
                                                    // Exibe idade recomendada da vacina, sem cálculos extras
                                                    echo htmlspecialchars($idade_reco !== '' ? $idade_reco : "Ao nascer");
                                                }
                                            ?><br>
                                            <strong>Via de Administração:</strong> <?php echo htmlspecialchars($row['via_adimicao']); ?><br>
                                            <strong>Número de Doses:</strong> <?php echo htmlspecialchars($row['n_dose']); ?><br>
                                            <strong>Intervalo entre Doses:</strong> <?php echo htmlspecialchars($row['intervalo_dose']); ?> meses<br>
                                            <strong>Estoque:</strong> <?php echo htmlspecialchars($row['estoque']); ?><br>
                                            <strong>Obrigatória SUS:</strong> Não
                                          </div>
                                        </div>
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