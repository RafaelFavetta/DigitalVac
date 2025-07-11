<?php
session_start();
include('../outros/db_connect.php');

// Recupera nome e coren_crm do médico logado
$nome_medico = '';
$coren_crm = '';
if (isset($_SESSION['id_medico'])) {
    $id_medico = $_SESSION['id_medico'];
    $stmt = $conn->prepare("SELECT nome_medico, coren_crm FROM medico WHERE id_medico = ?");
    $stmt->bind_param("i", $id_medico);
    $stmt->execute();
    $stmt->bind_result($nome_medico, $coren_crm);
    $stmt->fetch();
    $stmt->close();
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
            max-width: 500px;
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
                    <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="cadastroaplic.php">
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
                    <a class="nav-link active fs-6 fw-bold" href="cadastroatestado.php">
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
            <h2 class="text-center text-primary fw-bold">Cadastro de Aplicação</h2>
            <form id="cadastroAplicacaoForm" action="../Recebedados/recebedadosaplic.php" method="POST">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="nome-vacina" class="form-label fw-bold">Nome da Vacina</label>
                        <input type="text" class="form-control" name="nome_vacina" id="nome-vacina" autocomplete="off"
                            autocapitalize="off" spellcheck="false" required placeholder="Digite o nome da vacina">
                        <div id="autocomplete-nome-vacina" class="autocomplete-suggestions"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="data-aplica" class="form-label fw-bold">Data Aplicação</label>
                        <input type="date" class="form-control" name="data_aplica" id="data-aplica" required>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-12">
                        <label for="cpf-paciente" class="form-label fw-bold">CPF do Paciente</label>
                        <input type="search" class="form-control" name="cpf_paciente" id="cpf-paciente" autocomplete="off"
                            autocapitalize="off" spellcheck="false" required placeholder="Digite o CPF do paciente">
                        <div id="autocomplete-cpf-paciente" class="autocomplete-suggestions"></div>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label for="nome-posto" class="form-label fw-bold">Posto</label>
                        <input type="text" class="form-control" name="nome_posto" id="nome-posto" autocomplete="off"
                            autocapitalize="off" spellcheck="false" required placeholder="Digite o nome do posto">
                        <div id="autocomplete-nome-posto" class="autocomplete-suggestions"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="coren-crm" class="form-label fw-bold">COREN/CRM</label>
                        <input type="text" class="form-control" name="coren_crm" id="coren-crm" autocomplete="off"
                            autocapitalize="off" spellcheck="false" required placeholder="Digite ou selecione o COREN/CRM"
                            value="<?php echo htmlspecialchars($coren_crm); ?>">
                        <div id="autocomplete-coren-crm" class="autocomplete-suggestions"></div>
                    </div>
                </div>
                <input type="hidden" name="origem" value="medica">
                <div class="row g-3 mt-2 text-center">
                    <button type="submit" class="btn btn-primary fw-bold">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    <script src="../bootstrap/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Removido: Máscara para CPF
        // new Cleave('#cpf-paciente', {
        //     delimiters: ['.', '.', '-'],
        //     blocks: [3, 3, 3, 2],
        //     numericOnly: true
        // });

        // Máscara dinâmica para COREN/CRM
        const corenCrmInput = document.getElementById('coren-crm');
        corenCrmInput.addEventListener('input', function (e) {
            let value = corenCrmInput.value.replace(/\s+/g, '').toUpperCase();
            let formatted = '';
            if (value.startsWith('CRM')) {
                // CRM-UF 000000
                let match = value.match(/^CRM([A-Z]{2})?(\d{0,6})$/);
                if (match) {
                    formatted = 'CRM';
                    if (match[1]) {
                        formatted += '-' + match[1];
                    } else if (value.length > 3) {
                        formatted += '-';
                    }
                    if (match[2]) {
                        formatted += ' ' + match[2];
                    }
                } else {
                    // Tenta formatar parcialmente
                    formatted = value.replace(/^CRM/, 'CRM-');
                    if (formatted.length > 6) {
                        formatted = formatted.slice(0, 6) + ' ' + formatted.slice(6, 12);
                    }
                }
            } else if (value.startsWith('COREN')) {
                // COREN-UF 000000
                let match = value.match(/^COREN([A-Z]{2})?(\d{0,6})$/);
                if (match) {
                    formatted = 'COREN';
                    if (match[1]) {
                        formatted += '-' + match[1];
                    } else if (value.length > 5) {
                        formatted += '-';
                    }
                    if (match[2]) {
                        formatted += ' ' + match[2];
                    }
                } else {
                    // Tenta formatar parcialmente
                    formatted = value.replace(/^COREN/, 'COREN-');
                    if (formatted.length > 8) {
                        formatted = formatted.slice(0, 8) + ' ' + formatted.slice(8, 14);
                    }
                }
            } else {
                // fallback: mantém o valor original
                formatted = corenCrmInput.value;
            }
            corenCrmInput.value = formatted.trim();
        });

        // Toast Bootstrap
        function showAlert(type, message) {
            const toastEl = document.getElementById('toast-alert');
            const toastBody = document.getElementById('toast-alert-body');
            toastBody.textContent = message;
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

        document.getElementById("cadastroAplicacaoForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json().catch(() => ({ success: false, message: "Erro inesperado do servidor." })))
                .then((data) => {
                    if (data.success) {
                        showAlert('success', data.message || "Cadastro realizado com sucesso!");
                        this.reset();
                    } else {
                        showAlert('error', data.message || "Erro ao cadastrar.");
                    }
                })
                .catch((error) => {
                    console.error("Erro ao cadastrar:", error);
                    showAlert('error', "Ocorreu um erro ao cadastrar. Tente novamente.");
                });
        });

        document.getElementById("cadastroAplicacaoForm").addEventListener("submit", function (event) {
            const dose = document.getElementById("dose").value;

            if (dose <= 0) {
                alert("Erro: A dose deve ser maior que 0.");
                event.preventDefault();
                return;
            }
        });

        function validarCPF(cpf) {
            cpf = cpf.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            return resto === parseInt(cpf.substring(10, 11));
        }

        document.getElementById("cadastroAplicacaoForm").addEventListener("submit", function (event) {
            // Remover máscara do CPF antes de enviar
            const cpfInput = document.getElementById("cpf-paciente");
            cpfInput.value = cpfInput.value.replace(/\D/g, "");
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
                // Só busca se tiver pelo menos 3 caracteres
                if (query.length < 3) {
                    container.innerHTML = '';
                    container.style.display = 'none';
                    return;
                }
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

        // Inicialize autocomplete para todos os campos necessários
        setupAutocomplete('nome-vacina', '../ajax/autocomplete_vacina.php');
        setupAutocomplete('cpf-paciente', '../ajax/autocomplete_paciente.php');
        setupAutocomplete('nome-posto', '../ajax/autocomplete_posto.php');
        setupAutocomplete('coren-crm', '../ajax/autocomplete_medico.php');
    </script>
</body>

</html>