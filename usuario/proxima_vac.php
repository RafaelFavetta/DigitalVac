<?php
session_start();
include('../outros/db_connect.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Busca dados do usuário (data de nascimento)
$stmt = $conn->prepare("SELECT naci_usuario FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($naci_usuario);
$stmt->fetch();
$stmt->close();

$naci_usuario = $naci_usuario ?? date('Y-m-d');

// Busca todas as vacinas (agora incluindo o campo 'sus')
$sql = "SELECT id_vaci, nome_vaci, idade_reco, n_dose, intervalo_dose, sus FROM vacina";
$result = $conn->query($sql);

$vacinas = [];
while ($row = $result->fetch_assoc()) {
    $vacinas[] = $row;
}

// Busca todas as aplicações do usuário
$sql_aplic = "SELECT id_vaci, MAX(data_aplica) as ultima_data, MAX(dose_aplicad) as ultima_dose, COUNT(*) as total_doses
              FROM aplicacao
              WHERE id_usuario = ?
              GROUP BY id_vaci";
$stmt = $conn->prepare($sql_aplic);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res_aplic = $stmt->get_result();
$aplicacoes = [];
while ($row = $res_aplic->fetch_assoc()) {
    $aplicacoes[$row['id_vaci']] = $row;
}
$stmt->close();

// Função para calcular próxima dose
function calcularProximaDose($vacina, $aplicacao, $naci_usuario) {
    $idade_reco = trim($vacina['idade_reco']);
    $intervalo = intval($vacina['intervalo_dose']);
    $n_dose = intval($vacina['n_dose']);

    // Se nunca tomou nenhuma dose
    if (!$aplicacao) {
        // Calcular data da primeira dose: data de nascimento + idade recomendada
        if (preg_match('/(\d+)\s*mes/i', $idade_reco, $m)) {
            $meses = intval($m[1]);
            $data = date('Y-m-d', strtotime("+$meses months", strtotime($naci_usuario)));
            return $data;
        } elseif (preg_match('/(\d+)\s*ano/i', $idade_reco, $m)) {
            $anos = intval($m[1]);
            $data = date('Y-m-d', strtotime("+$anos years", strtotime($naci_usuario)));
            return $data;
        } else {
            return $naci_usuario; // "Ao nascer" ou "A qualquer momento"
        }
    } else {
        // Já tomou alguma dose
        $ultima_data = $aplicacao['ultima_data'];
        $ultima_dose = intval($aplicacao['ultima_dose']);
        if ($ultima_dose >= $n_dose) {
            return "Esquema completo";
        }
        // Próxima dose: última aplicação + intervalo
        if ($intervalo > 0) {
            $data = date('Y-m-d', strtotime("+$intervalo months", strtotime($ultima_data)));
            return $data;
        } else {
            // Se não há intervalo, próxima dose é indefinida
            return "Consultar profissional";
        }
    }
}

function formatarIdade($idade_reco) {
    if (preg_match('/(\d+)\s*mes/i', $idade_reco, $m)) {
        // Se for 0 meses, exibe "Ao nascer"
        if (intval($m[1]) === 0) {
            return "Ao nascer";
        }
        return $m[1] . " meses";
    } elseif (preg_match('/(\d+)\s*ano/i', $idade_reco, $m)) {
        return $m[1] . " anos";
    } else {
        return $idade_reco;
    }
}

// Função de ordenação personalizada para idade recomendada
function ordenarIdadeRecomendada($a, $b) {
    $getOrder = function($idade) {
        $idade = trim(mb_strtolower($idade));
        if ($idade === 'a qualquer momento') {
            return [0, 0];
        }
        if ($idade === 'ao nascer' || $idade === '0 meses' || $idade === '0 mes') {
            return [1, 0];
        }
        if (preg_match('/(\d+)\s*mes/i', $idade, $m)) {
            return [2, intval($m[1])];
        }
        if (preg_match('/(\d+)\s*ano/i', $idade, $m)) {
            return [3, intval($m[1])];
        }
        // Caso não reconheça, joga para o final
        return [4, 999];
    };
    $ordA = $getOrder($a['idade_reco']);
    $ordB = $getOrder($b['idade_reco']);
    if ($ordA[0] !== $ordB[0]) return $ordA[0] - $ordB[0];
    return $ordA[1] - $ordB[1];
}

// AJAX: retorna só a tabela se for requisição AJAX
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    $pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
    // Filtra vacinas pelo nome se pesquisa for enviada
    $vacinas_filtradas = [];
    foreach ($vacinas as $vacina) {
        if ($pesquisa === '' || stripos($vacina['nome_vaci'], $pesquisa) !== false) {
            $vacinas_filtradas[] = $vacina;
        }
    }
    // Ordena as vacinas filtradas
    usort($vacinas_filtradas, 'ordenarIdadeRecomendada');
    // Separa vacinas obrigatórias (SUS) e opcionais
    $vacinas_obrigatorias = [];
    $vacinas_opcionais = [];
    foreach ($vacinas_filtradas as $vacina) {
        if (isset($vacina['sus']) && intval($vacina['sus']) === 1) {
            $vacinas_obrigatorias[] = $vacina;
        } else {
            $vacinas_opcionais[] = $vacina;
        }
    }
    ?>
    <div id="tabela-proximas-vacinas">
        <?php if (count($vacinas_obrigatorias) > 0): ?>
        <div class="border border-primary rounded-3 mb-4 p-2 shadow-sm" style="background-color: #eaf4ff;">
            <h5 class="text-primary text-center mb-2 fw-bold">
                <i class="bi bi-shield-check"></i> Vacinas Obrigatórias (SUS)
            </h5>
            <table class="table table-bordered text-center mx-auto">
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
                    <?php
                    $rowIndex = 0;
                    foreach ($vacinas_obrigatorias as $vacina):
                        $id_vaci = $vacina['id_vaci'];
                        $aplic = $aplicacoes[$id_vaci] ?? null;
                        $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                        $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                        $n_dose = intval($vacina['n_dose']);
                        $rowClass = ($rowIndex % 2 === 0) ? 'bg-white' : 'table-primary';
                    ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                            <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                            <td>
                                <?php
                                $isAQualquerMomento = (mb_strtolower(trim($vacina['idade_reco'])) === 'a qualquer momento');
                                $isSemNumero = !preg_match('/\d+/', $vacina['idade_reco'] ?? '');

                                if (isset($vacina['sus']) && intval($vacina['sus']) === 0 && ($isAQualquerMomento || $isSemNumero)) {
                                    // Vacina opcional e "a qualquer momento" ou sem número: não mostra nada
                                    echo '';
                                } elseif ($proxima_dose === "Esquema completo") {
                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                } elseif ($proxima_dose === "Consultar profissional") {
                                    echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                    echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                } else {
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                }
                                ?>
                            </td>
                            <td><?= $doses_tomadas . " / " . $n_dose ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVacina<?= $id_vaci ?>">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="modalVacina<?= $id_vaci ?>" tabindex="-1" aria-labelledby="modalLabel<?= $id_vaci ?>" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                      <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                        <h5 class="modal-title fw-bold" id="modalLabel<?= $id_vaci ?>">
                                          <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                          <span class="ms-2">Informações da Vacina</span>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                      </div>
                                      <div class="modal-body px-2 px-md-4 py-3">
                                        <div class="row g-3 g-md-4">
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Vacina:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars($vacina['nome_vaci']) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Doses do Esquema:</span><br>
                                              <span class="fs-6"><?= $n_dose ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre doses:</span><br>
                                              <span class="fs-6"><?= intval($vacina['intervalo_dose']) ?> meses</span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Recomendada:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar2-week"></i> Próxima Dose:</span><br>
                                              <span class="fs-6">
                                                <?php
                                                if ($proxima_dose === "Esquema completo") {
                                                  echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                                } elseif ($proxima_dose === "Consultar profissional") {
                                                  echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                                  echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                } else {
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                }
                                                ?>
                                              </span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-clipboard-check"></i> Doses Aplicadas:</span><br>
                                              <span class="fs-6"><?= $doses_tomadas . " / " . $n_dose ?></span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <?php if ($aplic): ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar-check"></i> Última aplicação:</span><br>
                                                <span class="fs-6"><?= date('d/m/Y', strtotime($aplic['ultima_data'])) ?></span>
                                              </div>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Dose aplicada:</span><br>
                                                <span class="fs-6"><?= intval($aplic['ultima_dose']) ?></span>
                                              </div>
                                            <?php else: ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-info-circle"></i> Situação:</span><br>
                                                <span class="fs-6"><em>Nenhuma dose aplicada ainda.</em></span>
                                              </div>
                                            <?php endif; ?>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                              <span class="fs-6"><?= (isset($vacina['sus']) && intval($vacina['sus']) === 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                    <?php $rowIndex++; endforeach; ?>
                    <?php if (count($vacinas_obrigatorias) === 0): ?>
                        <tr><td colspan="5">Nenhuma vacina encontrada.</td></tr>
                    <?php endif; ?>
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
                        <th>Vacina</th>
                        <th>Idade Recomendada</th>
                        <th>Próxima Dose</th>
                        <th>Doses</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowIndex = 0;
                    foreach ($vacinas_opcionais as $vacina):
                        $id_vaci = $vacina['id_vaci'];
                        $aplic = $aplicacoes[$id_vaci] ?? null;
                        $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                        $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                        $n_dose = intval($vacina['n_dose']);
                        $rowClass = ($rowIndex % 2 === 0) ? 'bg-white' : 'table-primary';
                    ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                            <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                            <td>
                                <?php
                                $isAQualquerMomento = (mb_strtolower(trim($vacina['idade_reco'])) === 'a qualquer momento');
                                $isSemNumero = !preg_match('/\d+/', $vacina['idade_reco'] ?? '');

                                if (isset($vacina['sus']) && intval($vacina['sus']) === 0 && ($isAQualquerMomento || $isSemNumero)) {
                                    // Vacina opcional e "a qualquer momento" ou sem número: não mostra nada
                                    echo '';
                                } elseif ($proxima_dose === "Esquema completo") {
                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                } elseif ($proxima_dose === "Consultar profissional") {
                                    echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                    echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                } else {
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                }
                                ?>
                            </td>
                            <td><?= $doses_tomadas . " / " . $n_dose ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVacina<?= $id_vaci ?>">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="modalVacina<?= $id_vaci ?>" tabindex="-1" aria-labelledby="modalLabel<?= $id_vaci ?>" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                      <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                        <h5 class="modal-title fw-bold" id="modalLabel<?= $id_vaci ?>">
                                          <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                          <span class="ms-2">Informações da Vacina</span>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                      </div>
                                      <div class="modal-body px-2 px-md-4 py-3">
                                        <div class="row g-3 g-md-4">
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Vacina:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars($vacina['nome_vaci']) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Doses do Esquema:</span><br>
                                              <span class="fs-6"><?= $n_dose ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre doses:</span><br>
                                              <span class="fs-6"><?= intval($vacina['intervalo_dose']) ?> meses</span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Recomendada:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar2-week"></i> Próxima Dose:</span><br>
                                              <span class="fs-6">
                                                <?php
                                                if ($proxima_dose === "Esquema completo") {
                                                  echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                                } elseif ($proxima_dose === "Consultar profissional") {
                                                  echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                                  echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                } else {
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                }
                                                ?>
                                              </span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-clipboard-check"></i> Doses Aplicadas:</span><br>
                                              <span class="fs-6"><?= $doses_tomadas . " / " . $n_dose ?></span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <?php if ($aplic): ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar-check"></i> Última aplicação:</span><br>
                                                <span class="fs-6"><?= date('d/m/Y', strtotime($aplic['ultima_data'])) ?></span>
                                              </div>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Dose aplicada:</span><br>
                                                <span class="fs-6"><?= intval($aplic['ultima_dose']) ?></span>
                                              </div>
                                            <?php else: ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-info-circle"></i> Situação:</span><br>
                                                <span class="fs-6"><em>Nenhuma dose aplicada ainda.</em></span>
                                              </div>
                                            <?php endif; ?>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                              <span class="fs-6"><?= (isset($vacina['sus']) && intval($vacina['sus']) === 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                    <?php $rowIndex++; endforeach; ?>
                    <?php if (count($vacinas_opcionais) === 0): ?>
                        <tr><td colspan="5">Nenhuma vacina encontrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php
    exit;
}

// Ordena as vacinas para exibição inicial
usort($vacinas, 'ordenarIdadeRecomendada');
// Separa vacinas obrigatórias (SUS) e opcionais para exibição inicial
$vacinas_obrigatorias = [];
$vacinas_opcionais = [];
foreach ($vacinas as $vacina) {
    if (isset($vacina['sus']) && intval($vacina['sus']) === 1) {
        $vacinas_obrigatorias[] = $vacina;
    } else {
        $vacinas_opcionais[] = $vacina;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Adicione esta linha para os ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
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

        .modal-header {
            background: #0d6efd;
            color: #fff;
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
        <h2 class="text-primary fw-bold mb-4 text-center">Próximas Vacinas</h2>
        <!-- Campo de pesquisa AJAX -->
        <div class="w-100 d-flex justify-content-center mb-3">
            <form class="d-flex position-relative" role="search" id="form-pesquisa-vacina" style="max-width:600px; width:100%;">
                <input class="form-control me-2 border border-primary" type="search" placeholder="Nome da vacina"
                    aria-label="Pesquisar" id="pesquisa-vacina" autocomplete="off" maxlength="50"
                    pattern="[A-Za-zÀ-ÿ\s]+">
            </form>
        </div>
        <div id="tabela-proximas-vacinas">
            <?php if (count($vacinas_obrigatorias) > 0): ?>
            <div class="border border-primary rounded-3 mb-4 p-2 shadow-sm" style="background-color: #eaf4ff;">
                <h5 class="text-primary text-center mb-2 fw-bold">
                    <i class="bi bi-shield-check"></i> Vacinas Obrigatórias (SUS)
                </h5>
                <table class="table table-bordered text-center mx-auto">
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
                        <?php foreach ($vacinas_obrigatorias as $vacina):
                            $id_vaci = $vacina['id_vaci'];
                            $aplic = $aplicacoes[$id_vaci] ?? null;
                            $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                            $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                            $n_dose = intval($vacina['n_dose']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                                <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                                <td>
                                    <?php
                                    $isAQualquerMomento = (mb_strtolower(trim($vacina['idade_reco'])) === 'a qualquer momento');
                                    $isSemNumero = !preg_match('/\d+/', $vacina['idade_reco'] ?? '');

                                    if (isset($vacina['sus']) && intval($vacina['sus']) === 0 && ($isAQualquerMomento || $isSemNumero)) {
                                        // Vacina opcional e "a qualquer momento" ou sem número: não mostra nada
                                        echo '';
                                    } elseif ($proxima_dose === "Esquema completo") {
                                        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                    } elseif ($proxima_dose === "Consultar profissional") {
                                        echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                    } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                        echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                        echo date('d/m/Y', strtotime($proxima_dose));
                                    } else {
                                        echo date('d/m/Y', strtotime($proxima_dose));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= $doses_tomadas . " / " . $n_dose ?>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVacina<?= $id_vaci ?>">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="modalVacina<?= $id_vaci ?>" tabindex="-1" aria-labelledby="modalLabel<?= $id_vaci ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                          <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                            <h5 class="modal-title fw-bold" id="modalLabel<?= $id_vaci ?>">
                                              <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                              <span class="ms-2">Informações da Vacina</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                          </div>
                                          <div class="modal-body px-2 px-md-4 py-3">
                                            <div class="row g-3 g-md-4">
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Vacina:</span><br>
                                                  <span class="fs-6"><?= htmlspecialchars($vacina['nome_vaci']) ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Doses do Esquema:</span><br>
                                                  <span class="fs-6"><?= $n_dose ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre doses:</span><br>
                                                  <span class="fs-6"><?= intval($vacina['intervalo_dose']) ?> meses</span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Recomendada:</span><br>
                                                  <span class="fs-6"><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar2-week"></i> Próxima Dose:</span><br>
                                                  <span class="fs-6">
                                                    <?php
                                                    if ($proxima_dose === "Esquema completo") {
                                                      echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                                    } elseif ($proxima_dose === "Consultar profissional") {
                                                      echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                                    } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                                      echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                                      echo date('d/m/Y', strtotime($proxima_dose));
                                                    } else {
                                                      echo date('d/m/Y', strtotime($proxima_dose));
                                                    }
                                                    ?>
                                                  </span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-clipboard-check"></i> Doses Aplicadas:</span><br>
                                                  <span class="fs-6"><?= $doses_tomadas . " / " . $n_dose ?></span>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md-4">
                                                <?php if ($aplic): ?>
                                                  <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                    <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar-check"></i> Última aplicação:</span><br>
                                                    <span class="fs-6"><?= date('d/m/Y', strtotime($aplic['ultima_data'])) ?></span>
                                                  </div>
                                                  <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                    <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Dose aplicada:</span><br>
                                                    <span class="fs-6"><?= intval($aplic['ultima_dose']) ?></span>
                                                  </div>
                                                <?php else: ?>
                                                  <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                    <span class="fw-semibold text-primary-emphasis"><i class="bi bi-info-circle"></i> Situação:</span><br>
                                                    <span class="fs-6"><em>Nenhuma dose aplicada ainda.</em></span>
                                                  </div>
                                                <?php endif; ?>
                                                <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                  <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                                  <span class="fs-6"><?= (isset($vacina['sus']) && intval($vacina['sus']) === 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($vacinas_obrigatorias) === 0): ?>
                        <tr><td colspan="5">Nenhuma vacina encontrada.</td></tr>
                    <?php endif; ?>
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
                        <th>Vacina</th>
                        <th>Idade Recomendada</th>
                        <th>Próxima Dose</th>
                        <th>Doses</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacinas_opcionais as $vacina):
                        $id_vaci = $vacina['id_vaci'];
                        $aplic = $aplicacoes[$id_vaci] ?? null;
                        $proxima_dose = calcularProximaDose($vacina, $aplic, $naci_usuario);
                        $doses_tomadas = $aplic ? intval($aplic['total_doses']) : 0;
                        $n_dose = intval($vacina['n_dose']);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($vacina['nome_vaci']) ?></td>
                            <td><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></td>
                            <td>
                                <?php
                                $isAQualquerMomento = (mb_strtolower(trim($vacina['idade_reco'])) === 'a qualquer momento');
                                $isSemNumero = !preg_match('/\d+/', $vacina['idade_reco'] ?? '');

                                if (isset($vacina['sus']) && intval($vacina['sus']) === 0 && ($isAQualquerMomento || $isSemNumero)) {
                                    // Vacina opcional e "a qualquer momento" ou sem número: não mostra nada
                                    echo '';
                                } elseif ($proxima_dose === "Esquema completo") {
                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                } elseif ($proxima_dose === "Consultar profissional") {
                                    echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                    echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                } else {
                                    echo date('d/m/Y', strtotime($proxima_dose));
                                }
                                ?>
                            </td>
                            <td><?= $doses_tomadas . " / " . $n_dose ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVacina<?= $id_vaci ?>">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="modalVacina<?= $id_vaci ?>" tabindex="-1" aria-labelledby="modalLabel<?= $id_vaci ?>" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 18px; background: linear-gradient(135deg, #e3f0ff 0%, #f8fbff 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                                      <div class="modal-header" style="background: linear-gradient(90deg, #3b82f6 60%, #60a5fa 100%); color: #fff; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                                        <h5 class="modal-title fw-bold" id="modalLabel<?= $id_vaci ?>">
                                          <i class="bi bi-info-circle" style="color: #fffbe6; font-size: 1.5rem;"></i>
                                          <span class="ms-2">Informações da Vacina</span>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                      </div>
                                      <div class="modal-body px-2 px-md-4 py-3">
                                        <div class="row g-3 g-md-4">
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-capsule"></i> Vacina:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars($vacina['nome_vaci']) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-collection"></i> Doses do Esquema:</span><br>
                                              <span class="fs-6"><?= $n_dose ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-arrow-repeat"></i> Intervalo entre doses:</span><br>
                                              <span class="fs-6"><?= intval($vacina['intervalo_dose']) ?> meses</span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-hourglass-split"></i> Idade Recomendada:</span><br>
                                              <span class="fs-6"><?= htmlspecialchars(formatarIdade($vacina['idade_reco'])) ?></span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar2-week"></i> Próxima Dose:</span><br>
                                              <span class="fs-6">
                                                <?php
                                                if ($proxima_dose === "Esquema completo") {
                                                  echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Esquema completo</span>';
                                                } elseif ($proxima_dose === "Consultar profissional") {
                                                  echo '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Consultar profissional</span>';
                                                } elseif (strtotime($proxima_dose) < strtotime(date('Y-m-d'))) {
                                                  echo '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Atrasada</span> ';
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                } else {
                                                  echo date('d/m/Y', strtotime($proxima_dose));
                                                }
                                                ?>
                                              </span>
                                            </div>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-clipboard-check"></i> Doses Aplicadas:</span><br>
                                              <span class="fs-6"><?= $doses_tomadas . " / " . $n_dose ?></span>
                                            </div>
                                          </div>
                                          <div class="col-12 col-md-4">
                                            <?php if ($aplic): ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-calendar-check"></i> Última aplicação:</span><br>
                                                <span class="fs-6"><?= date('d/m/Y', strtotime($aplic['ultima_data'])) ?></span>
                                              </div>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-123"></i> Dose aplicada:</span><br>
                                                <span class="fs-6"><?= intval($aplic['ultima_dose']) ?></span>
                                              </div>
                                            <?php else: ?>
                                              <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                                <span class="fw-semibold text-primary-emphasis"><i class="bi bi-info-circle"></i> Situação:</span><br>
                                                <span class="fs-6"><em>Nenhuma dose aplicada ainda.</em></span>
                                              </div>
                                            <?php endif; ?>
                                            <div class="mb-3 pb-2 border-bottom border-2 border-primary-subtle">
                                              <span class="fw-semibold text-primary-emphasis"><i class="bi bi-shield-check"></i> Obrigatória SUS:</span><br>
                                              <span class="fs-6"><?= (isset($vacina['sus']) && intval($vacina['sus']) === 1) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>'; ?></span>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($vacinas_opcionais) === 0): ?>
                        <tr><td colspan="5">Nenhuma vacina encontrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Permite apenas letras e espaços no campo de pesquisa
        document.getElementById('pesquisa-vacina').addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0, 50);
        });

        // AJAX para atualizar tabela conforme digita
        const inputVacina = document.getElementById('pesquisa-vacina');
        const tabela = document.getElementById('tabela-proximas-vacinas');
        function atualizarTabelaProximasVacinas() {
            const termo = inputVacina.value;
            fetch('proxima_vac.php?pesquisa=' + encodeURIComponent(termo), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const novaTabela = temp.querySelector('#tabela-proximas-vacinas');
                    if (novaTabela) tabela.innerHTML = novaTabela.innerHTML;
                });
        }
        inputVacina.addEventListener('input', atualizarTabelaProximasVacinas);
        inputVacina.addEventListener('focus', function () {
            if (!this.value) atualizarTabelaProximasVacinas();
        });
    </script>
</body>
</html>