<?php
session_start();
include_once("../outros/db_connect.php");
$nome_medico = '';
if (isset($_SESSION['id_medico'])) {
    $id_medico = $_SESSION['id_medico'];
    $stmt = $conn->prepare("SELECT nome_medico FROM medico WHERE id_medico = ?");
    $stmt->bind_param("i", $id_medico);
    $stmt->execute();
    $stmt->bind_result($nome_medico);
    $stmt->fetch();
    $stmt->close();
}
$coren_crm = '';
if (isset($_SESSION['id_medico'])) {
    $id_medico = $_SESSION['id_medico'];
    $stmt2 = $conn->prepare("SELECT coren_crm FROM medico WHERE id_medico = ?");
    $stmt2->bind_param("i", $id_medico);
    $stmt2->execute();
    $stmt2->bind_result($coren_crm);
    $stmt2->fetch();
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-container {
            background: #FDFDFD;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: auto;
            margin-top: 32px;
            /* Adicionado para afastar da navbar */
        }

        /* Remover linha azul dos inputs ao focar */
        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            box-shadow: none !important;
            border-color: #ced4da !important;
        }

        /* AUTOCOMPLETE PADRÃO */
        .autocomplete-suggestions {
            position: absolute;
            z-index: 1000;
            width: 100%;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0 0 0.5rem 0.5rem;
            max-height: 220px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-top: -2px;
            display: none;
        }

        .autocomplete-suggestion {
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 1rem;
            color: #212529;
            background: #fff;
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.15s;
        }

        .autocomplete-suggestion:last-child {
            border-bottom: none;
        }

        .autocomplete-suggestion.active,
        .autocomplete-suggestion:hover {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .autocomplete-suggestion:focus {
            outline: none;
            background: #e7f1ff;
            color: #0d6efd;
        }

        .position-relative {
            position: relative !important;
        }

        /* Negrito para parte digitada */
        .autocomplete-bold {
            font-weight: bold;
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

        .navbar .btn-danger {
            padding: 4px 10px !important;
            font-size: 15px !important;
            min-width: 70px;
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
                    <a class="nav-link active fs-6 fw-bold" href="listavac.php">
                        <i class="bi bi-list"></i> Lista de Vacinas
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="pesquisa_paciente.php">
                        <i class="bi bi-person-lines-fill"></i> Pesquisar Pacientes
                    </a>
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="cadastroatestado.php">
                        <i class="bi bi-clipboard2-plus-fill"></i> Cadastrar Atestado
                    </a>
                    <a class="nav-link active fs-6 fw-bold" href="atestado_medico.php">
                        <i class="bi bi-clipboard-heart-fill"></i> Meus Atestados
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

    <!-- Formulário -->
    <div class="container d-flex align-items-center justify-content-center">
        <div class="form-container mx-auto">
            <!-- ALERTAS PERSONALIZADOS -->
            <!-- Toasts Bootstrap -->
            <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3"
                style="z-index: 1080; top: 80px;">
                <div id="toast-alert" class="toast align-items-center text-bg-primary border-0" role="alert"
                    aria-live="assertive" aria-atomic="true" style="min-width:350px; max-width:500px;">
                    <div class="d-flex">
                        <div class="toast-body" id="toast-alert-body"></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <h2 class="form-title text-center text-primary fw-bold mb-4">Cadastro de Atestado</h2>
            <form id="cadastroAtestadoForm" action="../Recebedados/recebedadosatestado.php" method="POST">
                <input type="hidden" name="origem" value="medica">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nome_paciente" class="form-label fw-bold">Nome do Paciente</label>
                        <input type="text" id="nome_paciente" name="nome_paciente" class="form-control" required
                            autocomplete="off" autocapitalize="off" spellcheck="false"
                            placeholder="Digite o nome do paciente">
                        <div id="autocomplete-nome_paciente" class="autocomplete-suggestions"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="nome_medico" class="form-label fw-bold">Nome do Médico</label>
                        <input type="text" id="nome_medico" name="nome_medico" class="form-control"
                            value="<?php echo htmlspecialchars($nome_medico); ?>" required autocomplete="off"
                            autocapitalize="off" spellcheck="false">
                        <div id="autocomplete-nome_medico" class="autocomplete-suggestions"></div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="coren_crm" class="form-label fw-bold">COREN/CRM</label>
                        <input type="text" id="coren_crm" name="coren_crm" class="form-control"
                            value="<?php echo htmlspecialchars($coren_crm); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="data_inicio" class="form-label fw-bold">Data de Início</label>
                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" required>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="data_termino" class="form-label fw-bold">Data de Término</label>
                        <input type="date" id="data_termino" name="data_termino" class="form-control" required>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label for="justificativa" class="form-label fw-bold">Justificativa</label>
                        <textarea id="justificativa" name="justificativa" class="form-control" rows="3"
                            required></textarea>
                        <small class="text-danger fw-bold">* Não coloque ponto final. Ele será adicionado automaticamente no
                            documento.</small>
                    </div>
                </div>
                <div class="row g-3 mt-2 text-center">
                    <button type="submit" class="btn btn-primary fw-bold">Finalizar o Cadastro</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script>
        // Toast Bootstrap
        function showAlert(type, message, atestadoId = null) {
            const toastEl = document.getElementById('toast-alert');
            const toastBody = document.getElementById('toast-alert-body');
            toastBody.innerHTML = message;

            // Adiciona botão de download se houver atestadoId
            if (type === 'success' && atestadoId) {
                // Botão para baixar o atestado sem abrir nova guia
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-success mt-2 fw-bold';
                btn.textContent = 'Baixar Atestado';
                btn.onclick = function () {
                    window.location = '../medica/download_atestado.php?id=' + encodeURIComponent(atestadoId);
                    setTimeout(() => {
                        // Fecha o toast após o clique no botão
                        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                        toast.hide();
                    }, 500);
                };
                toastBody.appendChild(document.createElement('br'));
                toastBody.appendChild(btn);
            }

            toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-primary');
            if (type === 'success') {
                toastEl.classList.add('text-bg-success');
            } else if (type === 'error') {
                toastEl.classList.add('text-bg-danger');
            } else {
                toastEl.classList.add('text-bg-primary');
            }
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }

        document.getElementById("cadastroAtestadoForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Impede o comportamento padrão do formulário

            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json().catch(() => ({ success: false, message: "Erro inesperado do servidor." })))
                .then((data) => {
                    if (data.success) {
                        // Exibe toast de sucesso com botão de download
                        showAlert('success', data.message || "Atestado cadastrado com sucesso!", data.atestado_id);
                        this.reset();
                    } else {
                        showAlert('error', data.message || "Erro ao cadastrar atestado.");
                    }
                })
                .catch((error) => {
                    console.error("Erro ao enviar o formulário:", error);
                    showAlert('error', "Ocorreu um erro ao cadastrar. Verifique os dados e tente novamente.");
                });
        });

        // AUTOCOMPLETE PADRÃO
        function setupAutocomplete(inputId, endpoint) {
            const input = document.getElementById(inputId);
            const container = document.getElementById('autocomplete-' + inputId);
            input.parentElement.classList.add('position-relative');
            let suggestions = [];
            let selectedIndex = -1;

            function highlightMatch(text, query) {
                if (!query) return text;
                const idx = text.toLowerCase().indexOf(query.toLowerCase());
                if (idx === -1) return text;
                const before = text.substring(0, idx);
                const match = text.substring(idx, idx + query.length);
                const after = text.substring(idx + query.length);
                return before + '<span class="autocomplete-bold">' + match + '</span>' + after;
            }

            function fetchSuggestions(query = '') {
                fetch(endpoint + '?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        // Ordena sugestões alfabeticamente ou numericamente
                        if (data.length > 0 && !isNaN(data[0])) {
                            suggestions = data.sort((a, b) => Number(a) - Number(b));
                        } else {
                            suggestions = data.sort((a, b) => a.localeCompare(b, 'pt-BR', { sensitivity: 'base' }));
                        }
                        renderSuggestions();
                    });
            }

            function renderSuggestions() {
                container.innerHTML = '';
                if (suggestions.length === 0) {
                    container.style.display = 'none';
                    return;
                }
                suggestions.forEach((item, idx) => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-suggestion' + (idx === selectedIndex ? ' active' : '');
                    div.innerHTML = highlightMatch(item, input.value);
                    div.onclick = () => {
                        input.value = item;
                        container.innerHTML = '';
                        container.style.display = 'none';
                    };
                    container.appendChild(div);
                });
                container.style.display = 'block';
            }

            input.addEventListener('input', function () {
                selectedIndex = -1;
                fetchSuggestions(this.value);
            });

            input.addEventListener('focus', function () {
                selectedIndex = -1;
                fetchSuggestions(''); // Mostra todas as sugestões ordenadas ao focar
            });

            input.addEventListener('keydown', function (e) {
                if (container.style.display !== 'block') return;
                if (e.key === 'ArrowDown') {
                    selectedIndex = (selectedIndex + 1) % suggestions.length;
                    renderSuggestions();
                    e.preventDefault();
                } else if (e.key === 'ArrowUp') {
                    selectedIndex = (selectedIndex - 1 + suggestions.length) % suggestions.length;
                    renderSuggestions();
                    e.preventDefault();
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && suggestions[selectedIndex]) {
                        input.value = suggestions[selectedIndex];
                        container.innerHTML = '';
                        container.style.display = 'none';
                        e.preventDefault();
                    }
                } else if (e.key === 'Escape') {
                    container.innerHTML = '';
                    container.style.display = 'none';
                }
            });

            document.addEventListener('click', function (e) {
                if (!container.contains(e.target) && e.target !== input) {
                    container.innerHTML = '';
                    container.style.display = 'none';
                }
            });
        }

        // Autocomplete apenas para nome do paciente
        setupAutocomplete('nome_paciente', '../ajax/autocomplete_nomepaciente.php');
        // Adicionado autocomplete para nome_medico
        setupAutocomplete('nome_medico', '../ajax/autocomplete_nomemedico.php');
    </script>
</body>

</html>