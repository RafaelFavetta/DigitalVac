<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DigitalVac</title>
  <link rel="icon" href="../img/logo.png" type="image/png" />
  <link rel="stylesheet" href="../bootstrap/bootstrap-5.3.6-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    .navbar-brand {
      font-size: 1.5rem !important;
      font-weight: bold !important;
      margin-left: 0.5rem !important;
    }

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
      background: #FDFDFD;
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
      background: #FDFDFD;
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

    body {
      background: #FDFDFD;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container-fluid">
      <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55" class="me-3" />
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-link disabled fs-6 fw-bold" aria-disabled="true" href="cadastroaplic.php">
            <i class="bi bi-clipboard2-heart-fill" style="font-size: 20px"></i>
            Aplicação de Vacinas
          </a>
          <a class="nav-link active fs-6 fw-bold" href="cadastropac.html">
            <i class="bi bi-person-plus-fill" style="font-size: 20px"></i>
            Cadastrar Pacientes
          </a>
          <a class="nav-link active fs-6 fw-bold" href="cadastrovac.html">
            <i class="bi bi-capsule" style="font-size: 20px"></i> Cadastrar
            Vacinas
          </a>
          <a class="nav-link active fs-6 fw-bold" href="cadastroenf.php">
            <i class="bi bi-person-badge" style="font-size: 20px"></i>
            Cadastrar Enfermeiro
          </a>
          <a class="nav-link active fs-6 fw-bold" href="cadastroposto.html">
            <i class="bi bi-building-fill-add" style="font-size: 20px"></i>
            Cadastrar Posto
          </a>
          <a class="nav-link active fs-6 fw-bold" href="cadastrocampanha.html">
            <i class="bi bi-megaphone-fill" style="font-size: 20px"></i>
            Cadastrar Campanha
          </a>
          <a class="nav-link active fs-6 fw-bold" href="listamedico.php">
            <i class="bi bi-file-earmark-text-fill" style="font-size: 20px"></i>
            Listar Medicos
          </a>
          <a class="nav-link active fs-6 fw-bold" href="listavac.php">
            <i class="bi bi-list" style="font-size: 20px"></i>
            Listar Vacinas
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
            <input type="search" class="form-control" name="nome_vacina" id="nome-vacina"
              placeholder="Digite o nome da vacina" autocomplete="off" autocapitalize="off" spellcheck="false"
              required />
            <div id="autocomplete-nome-vacina" class="autocomplete-suggestions"></div>
          </div>
          <div class="col-md-6">
            <label for="data-aplica" class="form-label fw-bold">Data de Aplicação</label>
            <input type="date" class="form-control" name="data_aplica" id="data-aplica" required />
          </div>
        </div>
        <div class="row g-2 mt-2">
          <div class="col-md-12">
            <label for="cpf-paciente" class="form-label fw-bold">CPF do Paciente</label>
            <input type="search" class="form-control" name="cpf_paciente" id="cpf-paciente"
              placeholder="Digite o CPF do paciente" autocomplete="off" autocapitalize="off" spellcheck="false"
              required />
            <div id="autocomplete-cpf-paciente" class="autocomplete-suggestions"></div>
          </div>
        </div>
        <div class="row g-2 mt-2">
          <div class="col-md-6">
            <label for="nome-posto" class="form-label fw-bold">Posto</label>
            <input type="search" class="form-control" name="nome_posto" id="nome-posto"
              placeholder="Digite o nome do posto" autocomplete="off" autocapitalize="off" spellcheck="false"
              required />
            <div id="autocomplete-nome-posto" class="autocomplete-suggestions"></div>
          </div>
          <div class="col-md-6">
            <label for="coren-crm" class="form-label fw-bold">COREN/CRM</label>
            <input type="text" class="form-control" name="coren_crm" id="coren-crm" autocomplete="off"
              autocapitalize="off" spellcheck="false" required placeholder="Digite ou selecione o COREN/CRM">
            <div id="autocomplete-coren-crm" class="autocomplete-suggestions"></div>
          </div>
        </div>
        <input type="hidden" name="origem" value="admin">
        <div class="row g-3 mt-2 text-center">
          <button type="submit" class="btn btn-primary fw-bold">
            Cadastrar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
  <script>
    // Removido: Máscara para CPF
    // new Cleave('#cpf-paciente', {
    //   delimiters: ['.', '.', '-'],
    //   blocks: [3, 3, 3, 2],
    //   numericOnly: true
    // });

    // Máscara para COREN/CRM
    new Cleave('#coren-crm', {
      delimiters: ['-', ' '],
      blocks: [6, 2, 6],
      uppercase: true,
      numericOnly: false
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

    function validarCPF(cpf) {
      cpf = cpf.replace(/\D/g, ""); // Remove formatação
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
        if (inputId === 'cpf-paciente' && query.length < 3) {
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

      // Remova o fetchSuggestions do evento de focus para CPF
      if (inputId !== 'cpf-paciente') {
        input.addEventListener('focus', function () {
          selectedIndex = -1;
          fetchSuggestions('');
        });
      }

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

    setupAutocomplete('nome-vacina', '../ajax/autocomplete_vacina.php');
    setupAutocomplete('cpf-paciente', '../ajax/autocomplete_paciente.php');
    setupAutocomplete('nome-posto', '../ajax/autocomplete_posto.php');
    setupAutocomplete('coren-crm', '../ajax/autocomplete_medico.php');

    // Exibe toast se houver mensagem na URL (para redirecionamentos)
    (function () {
      const params = new URLSearchParams(window.location.search);
      const msg = params.get('toast');
      const type = params.get('type') || 'primary';
      if (msg) showAlert(type, msg);
    })();
  </script>
</body>

</html>