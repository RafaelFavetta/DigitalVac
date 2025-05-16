<!-- filepath: c:\xampp\htdocs\site 6.0\Recebedados\recebedadosatestado.php -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../outros/db_connect.php'); // Incluindo o arquivo de conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebendo os dados do formulário
    $nome_paciente = htmlspecialchars($_POST['nome_paciente']); // Nome do paciente
    $nome_medico = htmlspecialchars($_POST['nome_medico']); // Nome do médico
    $data_inicio = htmlspecialchars($_POST['data_inicio']); // Data de início do atestado
    $data_fim = htmlspecialchars($_POST['data_termino']); // Data de término do atestado
    $justificativa = htmlspecialchars($_POST['justificativa']); // Justificativa do atestado
    $origem = isset($_POST['origem']) ? htmlspecialchars($_POST['origem']) : 'medica';

    // Validações de campos obrigatórios
    if (empty($nome_paciente)) {
        die("Erro: O campo 'Nome do Paciente' é obrigatório.");
    }
    if (empty($nome_medico)) {
        die("Erro: O campo 'Nome do Médico' é obrigatório.");
    }
    if (empty($data_inicio)) {
        die("Erro: O campo 'Data de Início' é obrigatório.");
    }
    if (empty($data_fim)) {
        die("Erro: O campo 'Data de Término' é obrigatório.");
    }
    if (empty($justificativa)) {
        die("Erro: O campo 'Justificativa' é obrigatório.");
    }

    // Buscar o ID do paciente pelo nome na tabela `usuario`
    $query_paciente = "SELECT id_usuario FROM usuario WHERE nome_usuario = ?";
    $stmt_paciente = $conn->prepare($query_paciente);
    $stmt_paciente->bind_param("s", $nome_paciente);
    $stmt_paciente->execute();
    $result_paciente = $stmt_paciente->get_result();

    if ($result_paciente->num_rows > 0) {
        $row_paciente = $result_paciente->fetch_assoc();
        $id_paci = $row_paciente['id_usuario'];
    } else {
        die("Erro: Paciente não encontrado.");
    }

    // Buscar o ID do médico pelo nome na tabela `medico`
    $query_medico = "SELECT id_medico FROM medico WHERE nome_medico = ?";
    $stmt_medico = $conn->prepare($query_medico);
    $stmt_medico->bind_param("s", $nome_medico);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();

    if ($result_medico->num_rows > 0) {
        $row_medico = $result_medico->fetch_assoc();
        $id_medico = $row_medico['id_medico'];
    } else {
        die("Erro: Médico não encontrado.");
    }

    // Preparando a consulta SQL para inserir os dados no banco de dados
    $sql = "INSERT INTO atestado (id_paci, id_medico, data_inicio, data_fim, justificativa) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $id_paci, $id_medico, $data_inicio, $data_fim, $justificativa);

    // Executando a consulta
    if ($stmt->execute()) {
        echo "<script>
                alert('Cadastro de atestado realizado com sucesso!');
                window.location.href = '../$origem/telainicio.php';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao cadastrar atestado: " . $stmt->error . "');
                window.location.href = '../cadastroatestado.html';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>