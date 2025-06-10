<?php
session_start();
require_once '../outros/db_connect.php'; // Ajuste o caminho para o arquivo de conexão com o banco

// Função para calcular próxima dose e atraso corretamente, mostrando idade recomendada em meses (<16) ou anos (>=16)
function calcular_proxima_dose($vacina, $aplicada, $idade_meses_usuario, $data_nascimento) {
    $n_obrig = isset($vacina['n_dose']) ? intval($vacina['n_dose']) : 0;
    $doses_tomadas = $aplicada && isset($aplicada['doses_tomadas']) ? intval($aplicada['doses_tomadas']) : 0;
    $prox_dose = $doses_tomadas + 1;

    $idade_anos_reco = isset($vacina['idade_anos_reco']) ? intval($vacina['idade_anos_reco']) : 0;
    $idade_meses_reco = isset($vacina['idade_meses_reco']) ? intval($vacina['idade_meses_reco']) : 0;
    $idade_recomendada_meses = $idade_anos_reco * 12 + $idade_meses_reco;

    // Função para calcular a data correta somando anos e meses separadamente
    $calcularData = function($data_nascimento, $anos, $meses) {
        $dt = new DateTime($data_nascimento);
        if ($anos > 0) $dt->add(new DateInterval('P' . $anos . 'Y'));
        if ($meses > 0) $dt->add(new DateInterval('P' . $meses . 'M'));
        return $dt->format('d/m/Y');
    };

    // Se nunca tomou nenhuma dose
    if ($doses_tomadas == 0) {
        if ($idade_anos_reco === 0 && $idade_meses_reco === 0) {
            if ($idade_meses_usuario > 0) {
                return ['Atrasada', true];
            }
            return ['Ao nascer', false];
        }
        if ($idade_meses_usuario >= $idade_recomendada_meses) {
            $data_prevista = $calcularData($data_nascimento, $idade_anos_reco, $idade_meses_reco);
            return [$data_prevista . ' (Atrasada)', true];
        } else {
            $data_prevista = $calcularData($data_nascimento, $idade_anos_reco, $idade_meses_reco);
            return [$data_prevista, false];
        }
    } else {
        // Próxima dose: intervalo a partir da última aplicação
        $intervalo = isset($vacina['intervalo_dose']) ? intval($vacina['intervalo_dose']) : 0;
        if ($intervalo > 0 && !empty($aplicada['ultima_data'])) {
            $dt = new DateTime($aplicada['ultima_data']);
            $dt->add(new DateInterval('P' . $intervalo . 'M'));
            $data_proxima = $dt->format('d/m/Y');
            $hoje = date('Y-m-d');
            if ($dt < new DateTime($hoje)) {
                return [$data_proxima . ' (Atrasada)', true];
            }
            return [$data_proxima, false];
        }
        return ['-', false];
    }
}


// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID do usuário da sessão
$user_id = $_SESSION['id_usuario'];

// Buscar a data de nascimento e gênero do usuário
$query_user = "SELECT naci_usuario, genero_usuario FROM usuario WHERE id_usuario = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();

$vacinas_obrigatorias_pendentes = [];
$vacinas_opcionais_nao_tomadas = [];

if ($user_data) {
    $data_nascimento = $user_data['naci_usuario'];
    $genero_usuario = $user_data['genero_usuario'];
    $dt_nasc = new DateTime($data_nascimento);
    $dt_hoje = new DateTime();
    $idade_anos = $dt_hoje->diff($dt_nasc)->y;
    $idade_meses_usuario = $dt_hoje->diff($dt_nasc)->y * 12 + $dt_hoje->diff($dt_nasc)->m;
    $idade = $idade_anos;

    // Buscar vacinas disponíveis (tabela vacina)
    $sql_vacinas = "SELECT * FROM vacina";
    $result_vacinas = $conn->query($sql_vacinas);
    $vacinas_fisicas = [];
    while ($row = $result_vacinas->fetch_assoc()) {
        $row['sus'] = isset($row['sus']) ? intval($row['sus']) : 0;
        $row['nome_vacina'] = $row['nome_vaci'];
        $vacinas_fisicas[$row['id_vaci']] = $row;
    }

    // Buscar vacinas já aplicadas ao usuário (inclui datas)
    $sql_aplicadas = "SELECT id_vaci, COUNT(*) as doses_tomadas, MAX(data_aplica) as ultima_data FROM aplicacao WHERE id_usuario = ? GROUP BY id_vaci";
    $stmt_aplicadas = $conn->prepare($sql_aplicadas);
    $stmt_aplicadas->bind_param("i", $user_id);
    $stmt_aplicadas->execute();
    $result_aplicadas = $stmt_aplicadas->get_result();
    $vacinas_aplicadas = [];
    while ($row = $result_aplicadas->fetch_assoc()) {
        $vacinas_aplicadas[$row['id_vaci']] = [
            'doses_tomadas' => $row['doses_tomadas'],
            'ultima_data' => $row['ultima_data']
        ];
    }

   //Filtro por pesquisa (AJAX)
    $pesquisa = '';
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
        isset($_GET['pesquisa'])
    ) {
        $pesquisa = trim($_GET['pesquisa']);
    }

    // Verifica vacinas obrigatórias com doses pendentes
    foreach ($vacinas_fisicas as $id_vaci => $vacina) {
        if ($vacina['sus'] != 1) continue;
        $n_obrig = isset($vacina['n_dose']) ? intval($vacina['n_dose']) : 0;
        if ($n_obrig <= 0) continue;

        if (stripos($vacina['nome_vacina'], 'gestante') !== false && $genero_usuario !== 'F') continue;

        $doses_tomadas = isset($vacinas_aplicadas[$id_vaci]) ? $vacinas_aplicadas[$id_vaci]['doses_tomadas'] : 0;
        if ($doses_tomadas < $n_obrig) {
            if ($pesquisa === '' || stripos($vacina['nome_vacina'], $pesquisa) !== false) {
                $vacina['doses_tomadas'] = $doses_tomadas;
                $vacina['n_obrig'] = $n_obrig;
                $vacinas_obrigatorias_pendentes[] = $vacina;
            }
        }
    }

    // Vacinas opcionais (SUS=0) que o usuário nunca tomou
    foreach ($vacinas_fisicas as $id_vaci => $vacina) {
        if ($vacina['sus'] != 0) continue;
        if (stripos($vacina['nome_vacina'], 'gestante') !== false && $genero_usuario !== 'F') continue;
        if (empty($vacinas_aplicadas[$id_vaci])) {
            if ($pesquisa === '' || stripos($vacina['nome_vacina'], $pesquisa) !== false) {
                $vacinas_opcionais_nao_tomadas[] = $vacina;
            }
        }
    }
}

// Se for AJAX, retorna só o <tbody>
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    ?>
    <tbody>
        <tr>
            <?php if (!empty($vacinas_obrigatorias_pendentes)): ?>
                <th colspan="5" class="table-primary text-center">Vacinas Obrigatórias Pendentes</th>
            <?php endif; ?>
        </tr>
        <?php
        // Adicione este bloco antes do foreach das vacinas obrigatórias pendentes
        // Ordena as vacinas obrigatórias pendentes pela data da próxima dose (idade recomendada)
        usort($vacinas_obrigatorias_pendentes, function($a, $b) use ($vacinas_aplicadas, $idade_meses_usuario, $data_nascimento) {
            $aplicadaA = isset($vacinas_aplicadas[$a['id_vaci']]) ? $vacinas_aplicadas[$a['id_vaci']] : null;
            $aplicadaB = isset($vacinas_aplicadas[$b['id_vaci']]) ? $vacinas_aplicadas[$b['id_vaci']] : null;
            list($dataA, ) = calcular_proxima_dose($a, $aplicadaA, $idade_meses_usuario, $data_nascimento);
            list($dataB, ) = calcular_proxima_dose($b, $aplicadaB, $idade_meses_usuario, $data_nascimento);

            // Extrai a data (pode vir com " (Atrasada)")
            $dataA = preg_replace('/ \(Atrasada\)$/', '', $dataA);
            $dataB = preg_replace('/ \(Atrasada\)$/', '', $dataB);

            // Datas "Ao nascer" ou "-" vão para o início
            if ($dataA === 'Ao nascer') return -1;
            if ($dataB === 'Ao nascer') return 1;
            if ($dataA === '-') return 1;
            if ($dataB === '-') return -1;

            // Converte para timestamp para comparar
            $tsA = strtotime(str_replace('/', '-', $dataA));
            $tsB = strtotime(str_replace('/', '-', $dataB));
            return $tsA <=> $tsB;
        });

        $rowIndex = 0;
        foreach ($vacinas_obrigatorias_pendentes as $vacina):
            $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
            $proxima_dose = $vacina['doses_tomadas'] + 1;
            $aplicada = isset($vacinas_aplicadas[$vacina['id_vaci']]) ? $vacinas_aplicadas[$vacina['id_vaci']] : null;
            list($proxima_dose_valor, $atrasada) = calcular_proxima_dose($vacina, $aplicada, $idade_meses_usuario, $data_nascimento);
            ?>
            <tr class="<?php echo $rowClass; ?>" style="height:38px;">
                <td style="vertical-align:middle;"><?= htmlspecialchars($vacina['nome_vacina']) ?></td>
                <td style="vertical-align:middle; text-align:center;">
                    <?php
                        // Exibe idade recomendada fixa da vacina
                        $idade_meses = isset($vacina['idade_meses_reco']) ? intval($vacina['idade_meses_reco']) : 0;
                        $idade_anos = isset($vacina['idade_anos_reco']) ? intval($vacina['idade_anos_reco']) : 0;
                        $total_meses = $idade_anos * 12 + $idade_meses;
                        if ($total_meses === 0) {
                            echo "Ao nascer";
                        } elseif ($total_meses < 24) {
                            echo $total_meses . " meses";
                        } else {
                            $anos = floor($total_meses / 12);
                            echo $anos . " anos";
                        }
                    ?>
                </td>
                <td style="vertical-align:middle; text-align:center;">
                    <?php if ($atrasada): ?>
                        <span class="badge bg-danger" style="font-size:1em;">
                            <?= htmlspecialchars($proxima_dose_valor) ?>
                        </span>
                    <?php else: ?>
                        <?= htmlspecialchars($proxima_dose_valor) ?>
                    <?php endif; ?>
                </td>
                <td style="vertical-align:middle; text-align:center;">
                    <div style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:8px; height:100%;">
                        <span style="font-size:1.02em;"><?= $vacina['doses_tomadas'] . "/" . $vacina['n_obrig'] ?></span>
                        <span class="badge bg-primary" style="font-size:0.90em; min-width:80px; padding:4px 6px;">
                            Próxima: Dose <?= $proxima_dose ?>
                        </span>
                    </div>
                </td>
                <td style="vertical-align:middle; text-align:center; padding-top:4px; padding-bottom:4px;">
                    <a href="ver_vacinaU.php?id_vaci=<?= urlencode($vacina['id_vaci']) ?>"
                        class="btn btn-primary btn-sm" style="padding:2px 8px; font-size:0.95em;">
                        <i class="bi bi-info-circle"></i> Sobre a vacina
                    </a>
                </td>
            </tr>
            <?php $rowIndex++; endforeach; ?>
        <tr>
            <?php if (!empty($vacinas_opcionais_nao_tomadas)): ?>
                <th colspan="5" class="table-warning text-center">Vacinas Opcionais Disponíveis</th>
            <?php endif; ?>
        </tr>
        <?php
        $rowIndex = 0;
        foreach ($vacinas_opcionais_nao_tomadas as $vacina):
            $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
            $nome = isset($vacina['nome_vacina']) ? $vacina['nome_vacina'] : '';
            $idade_label = '';
            if (
                stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
            ) {
                $idade_label = "50 anos";
            } elseif (
                stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
            ) {
                $idade_label = "10 anos";
            } elseif (
                stripos($nome, 'HPV') !== false
            ) {
                $idade_label = "9 anos";
            } elseif (
                stripos($nome, 'Influenza') !== false
            ) {
                $idade_label = "18 anos";
            } elseif (
                stripos($nome, 'Hepatite B (adulto)') !== false
            ) {
                $idade_label = "18 anos";
            } elseif (
                stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
            ) {
                $idade_label = "18 anos";
            } elseif (
                stripos($nome, 'VSR') !== false ||
                stripos($nome, 'Raiva') !== false ||
                stripos($nome, 'viajantes') !== false
            ) {
                $idade_label = "A qualquer momento";
            } else {
                $idade_anos = isset($vacina['idade_anos_reco']) ? intval($vacina['idade_anos_reco']) : 0;
                $idade_meses = isset($vacina['idade_meses_reco']) ? intval($vacina['idade_meses_reco']) : 0;
                // Exibe idade apenas se houver valor, senão mostra "-"
                if ($idade_anos > 0) {
                    $idade_label = $idade_anos . " anos";
                } elseif ($idade_meses > 0) {
                    $idade_label = $idade_meses . " meses";
                } else {
                    $idade_label = "-";
                }
            }
            ?>
            <tr class="<?php echo $rowClass; ?>" style="height:38px;">
                <td style="vertical-align:middle;"><?= htmlspecialchars($vacina['nome_vacina']) ?></td>
                <td style="vertical-align:middle; text-align:center;">
                    <?= $idade_label ?>
                </td>
                <td style="vertical-align:middle; text-align:center;">-</td>
                <td style="vertical-align:middle; text-align:center;">
                    <span class="badge bg-warning text-dark" style="font-size:0.95em; min-width:80px; padding:4px 6px;">
                        Opcional
                    </span>
                </td>
                <td style="vertical-align:middle; text-align:center; padding-top:4px; padding-bottom:4px;">
                    <a href="ver_vacinaU.php?id_vaci=<?= urlencode($vacina['id_vaci']) ?>"
                        class="btn btn-primary btn-sm" style="padding:2px 8px; font-size:0.95em;">
                        <i class="bi bi-info-circle"></i> Sobre a vacina
                    </a>
                </td>
            </tr>
            <?php $rowIndex++; endforeach; ?>
        <?php if (empty($vacinas_obrigatorias_pendentes) && empty($vacinas_opcionais_nao_tomadas)): ?>
            <tr>
                <td colspan="5">Nenhuma vacina pendente.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            margin-left: 0.5rem !important;
        }

        .container {
            max-width: 95%;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            min-width: 700px;
            /* Defina o min-width apenas aqui */
        }

        .table thead th {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Remove o X nativo do input type search no Chrome/Edge */
        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
        }

        /* Remove o X nativo do input type search no Firefox */
        input[type="search"]::-ms-clear {
            display: none;
            width: 0;
            height: 0;
        }

        /* Remove o X nativo do input type search no IE */
        input[type="search"]::-ms-reveal {
            display: none;
            width: 0;
            height: 0;
        }

        @media (max-width: 800px) {
            .table {
                min-width: 0;
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
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
                    <a class="nav-link active fs-6 fw-bold" href="telainicioU.php">
                        <i class="bi bi-house-fill"></i> Início
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="perfilU.php">
                        <i class="bi bi-person-fill"></i> Perfil
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="carteira_vac.php">
                        <i class="bi bi-postcard-heart-fill"></i> Carteira de Vacinas
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="proxima_vac.php">
                        <i class="bi bi-calendar2-week-fill"></i> Próximas Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Atestados
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
        <h2 class="text-center text-primary fw-bold">Vacinas a serem aplicadas</h2>
        <div class="w-100 d-flex justify-content-center">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-proxima-vacina"
                style="max-width:600px; width:100%;">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-proxima-vacina" autocomplete="off" maxlength="50"
                    pattern="[A-Za-zÀ-ÿ\s]+">
            </form>
        </div>
        <br>
        <div class="d-flex justify-content-end align-items-center mb-2" style="width:100%;">
            <a href="https://www.gov.br/saude/pt-br/vacinacao/calendario" target="_blank" class="custom-vac-link">
                <i class="bi bi-link-45deg me-1"></i> Lista de vacinas
            </a>
        </div>
        <div class="table-responsive d-flex justify-content-center" id="tabela-proxima-vacina">
            <div style="width: 100%;">
                <table class="table table-bordered text-center w-100 mx-auto">
                    <thead>
                        <tr>
                            <th>Vacina</th>
                            <th>Idade Recomendada</th>
                            <th>Próxima Dose</th>
                            <th>Doses</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th colspan="5" class="table-primary text-center">Vacinas Obrigatórias Pendentes</th>
                        </tr>
                        <?php if (!empty($vacinas_obrigatorias_pendentes)): ?>
                            <?php
                            // Adicione este bloco antes do foreach das vacinas obrigatórias pendentes
                            // Ordena as vacinas obrigatórias pendentes pela data da próxima dose (idade recomendada)
                            usort($vacinas_obrigatorias_pendentes, function($a, $b) use ($vacinas_aplicadas, $idade_meses_usuario, $data_nascimento) {
                                $aplicadaA = isset($vacinas_aplicadas[$a['id_vaci']]) ? $vacinas_aplicadas[$a['id_vaci']] : null;
                                $aplicadaB = isset($vacinas_aplicadas[$b['id_vaci']]) ? $vacinas_aplicadas[$b['id_vaci']] : null;
                                list($dataA, ) = calcular_proxima_dose($a, $aplicadaA, $idade_meses_usuario, $data_nascimento);
                                list($dataB, ) = calcular_proxima_dose($b, $aplicadaB, $idade_meses_usuario, $data_nascimento);

                                // Extrai a data (pode vir com " (Atrasada)")
                                $dataA = preg_replace('/ \(Atrasada\)$/', '', $dataA);
                                $dataB = preg_replace('/ \(Atrasada\)$/', '', $dataB);

                                // Datas "Ao nascer" ou "-" vão para o início
                                if ($dataA === 'Ao nascer') return -1;
                                if ($dataB === 'Ao nascer') return 1;
                                if ($dataA === '-') return 1;
                                if ($dataB === '-') return -1;

                                // Converte para timestamp para comparar
                                $tsA = strtotime(str_replace('/', '-', $dataA));
                                $tsB = strtotime(str_replace('/', '-', $dataB));
                                return $tsA <=> $tsB;
                            });

                            $rowIndex = 0;
                            foreach ($vacinas_obrigatorias_pendentes as $vacina):
                                $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
                                $proxima_dose = $vacina['doses_tomadas'] + 1;
                                $aplicada = isset($vacinas_aplicadas[$vacina['id_vaci']]) ? $vacinas_aplicadas[$vacina['id_vaci']] : null;
                                list($proxima_dose_valor, $atrasada) = calcular_proxima_dose($vacina, $aplicada, $idade_meses_usuario, $data_nascimento);
                                ?>
                                <tr class="<?php echo $rowClass; ?>" style="height:38px;">
                                    <td style="vertical-align:middle;"><?= htmlspecialchars($vacina['nome_vacina']) ?></td>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <?php
                                            // Exibe idade recomendada fixa da vacina
                                            $idade_meses = isset($vacina['idade_meses_reco']) ? intval($vacina['idade_meses_reco']) : 0;
                                            $idade_anos = isset($vacina['idade_anos_reco']) ? intval($vacina['idade_anos_reco']) : 0;
                                            $total_meses = $idade_anos * 12 + $idade_meses;
                                            if ($total_meses === 0) {
                                                echo "Ao nascer";
                                            } elseif ($total_meses < 24) {
                                                echo $total_meses . " meses";
                                            } else {
                                                $anos = floor($total_meses / 12);
                                                echo $anos . " anos";
                                            }
                                        ?>
                                    </td>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <?php if ($atrasada): ?>
                                            <span class="badge bg-danger" style="font-size:1em;">
                                                <?= htmlspecialchars($proxima_dose_valor) ?>
                                            </span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($proxima_dose_valor) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <div
                                            style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:8px; height:100%;">
                                            <span
                                                style="font-size:1.02em;"><?= $vacina['doses_tomadas'] . "/" . $vacina['n_obrig'] ?></span>
                                            <span class="badge bg-primary"
                                                style="font-size:0.90em; min-width:80px; padding:4px 6px;">
                                                Próxima: Dose <?= $proxima_dose ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td style="vertical-align:middle; text-align:center; padding-top:4px; padding-bottom:4px;">
                                        <a href="ver_vacinaU.php?id_vaci=<?= urlencode($vacina['id_vaci']) ?>"
                                            class="btn btn-primary btn-sm" style="padding:2px 8px; font-size:0.95em;">
                                            <i class="bi bi-info-circle"></i> Sobre a vacina
                                        </a>
                                    </td>
                                </tr>
                                <?php $rowIndex++; endforeach; ?>
                        <?php endif; ?>
                        <tr>
                            <th colspan="5" class="table-warning text-center">Vacinas Opcionais Disponíveis</th>
                        </tr>
                        <?php if (!empty($vacinas_opcionais_nao_tomadas)): ?>
                            <?php
                            $rowIndex = 0;
                            foreach ($vacinas_opcionais_nao_tomadas as $vacina):
                                $rowClass = ($rowIndex === 0) ? 'bg-white' : (($rowIndex % 2 === 1) ? 'table-secondary' : 'bg-white');
                                $nome = isset($vacina['nome_vacina']) ? $vacina['nome_vacina'] : '';
                                $idade_label = '';
                                if (
                                    stripos($nome, 'Herpes-zóster') !== false || stripos($nome, 'RZV') !== false
                                ) {
                                    $idade_label = "50 anos";
                                } elseif (
                                    stripos($nome, 'Dengue') !== false || stripos($nome, 'Qdenga') !== false
                                ) {
                                    $idade_label = "10 anos";
                                } elseif (
                                    stripos($nome, 'HPV') !== false
                                ) {
                                    $idade_label = "9 anos";
                                } elseif (
                                    stripos($nome, 'Influenza') !== false
                                ) {
                                    $idade_label = "18 anos";
                                } elseif (
                                    stripos($nome, 'Hepatite B (adulto)') !== false
                                ) {
                                    $idade_label = "18 anos";
                                } elseif (
                                    stripos($nome, 'Hepatite B') !== false && stripos($nome, 'adulto') !== false
                                ) {
                                    $idade_label = "18 anos";
                                } elseif (
                                    stripos($nome, 'VSR') !== false ||
                                    stripos($nome, 'Raiva') !== false ||
                                    stripos($nome, 'viajantes') !== false
                                ) {
                                    $idade_label = "A qualquer momento";
                                } else {
                                    $idade_anos = isset($vacina['idade_anos_reco']) ? intval($vacina['idade_anos_reco']) : 0;
                                    $idade_meses = isset($vacina['idade_meses_reco']) ? intval($vacina['idade_meses_reco']) : 0;
                                    // Exibe idade apenas se houver valor, senão mostra "-"
                                    if ($idade_anos > 0) {
                                        $idade_label = $idade_anos . " anos";
                                    } elseif ($idade_meses > 0) {
                                        $idade_label = $idade_meses . " meses";
                                    } else {
                                        $idade_label = "-";
                                    }
                                }
                                ?>
                                <tr class="<?php echo $rowClass; ?>" style="height:38px;">
                                    <td style="vertical-align:middle;"><?= htmlspecialchars($vacina['nome_vacina']) ?></td>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <?= $idade_label ?>
                                    </td>
                                    <td style="vertical-align:middle; text-align:center;">-</td>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <span class="badge bg-warning text-dark" style="font-size:0.95em; min-width:80px; padding:4px 6px;">
                                            Opcional
                                        </span>
                                    </td>
                                    <td style="vertical-align:middle; text-align:center; padding-top:4px; padding-bottom:4px;">
                                        <a href="ver_vacinaU.php?id_vaci=<?= urlencode($vacina['id_vaci']) ?>"
                                            class="btn btn-primary btn-sm" style="padding:2px 8px; font-size:0.95em;">
                                            <i class="bi bi-info-circle"></i> Sobre a vacina
                                        </a>
                                    </td>
                                </tr>
                                <?php $rowIndex++; endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($vacinas_obrigatorias_pendentes) && empty($vacinas_opcionais_nao_tomadas)): ?>
                            <tr>
                                <td colspan="5">Nenhuma vacina pendente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Removido o botão/link abaixo da tabela -->
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Pesquisa automática AJAX para vacinas a serem aplicadas
    document.getElementById('pesquisa-proxima-vacina').addEventListener('input', function () {
        // Permite apenas letras e espaços no campo de pesquisa
        this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0, 50);
        const termo = this.value;
        fetch('proxima_vac.php?pesquisa=' + encodeURIComponent(termo), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                // Atualiza apenas o <tbody>
                const tbody = document.querySelector('#tabela-proxima-vacina tbody');
                if (tbody) {
                    tbody.outerHTML = html;
                }
            });
    });
</script>
<style>
    .custom-vac-link {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        background: #0d6efd;
        color: #fff !important;
        font-weight: 500;
        font-size: 0.98rem;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 110, 253, 0.08);
        transition: background 0.2s, box-shadow 0.2s, color 0.2s;
        margin-top: 0;
    }

    .custom-vac-link:hover,
    .custom-vac-link:focus {
        background: #084298;
        color: #fff !important;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 110, 253, 0.15);
        outline: none;
    }

    .custom-vac-link i {
        font-size: 1.1em;
        vertical-align: middle;
    }
</style>

</html>