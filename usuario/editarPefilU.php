<!-- filepath: c:\xampp\htdocs\DigitalVac 1.2\usuario\editarPerfilU.php -->
<?php
session_start();
require_once '../outros/db_connect.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id_usuario'];

$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}
$nome = $user['nome_usuario'];
$telefone = $user['tel_usuario'];
$genero = $user['genero_usuario'];
$email = $user['email_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - DigitalVac</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../img/logo_vetor.png" alt="Logo DigitalVac" width="55" height="55">
                <a class="navbar-brand fs-4 fw-bold ms-2">DigitalVac</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg p-4 text-center">
                    <h3 class="mt-3"><?php echo htmlspecialchars($nome); ?></h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-primary fw-bold">Editar Perfil</h3>
                    <form action="salvarPerfil.php" method="POST">

                        <div class="mb-3">
                            <label for="telefone" class="form-label"><strong>Telefone:</strong></label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                value="<?php echo htmlspecialchars($telefone); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="genero" class="form-label"><strong>Gênero:</strong></label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="masculino" <?php echo $genero == 'Masculino' ? 'selected' : ''; ?>>
                                    Masculino</option>
                                <option value="feminino" <?php echo $genero == 'Feminino' ? 'selected' : ''; ?>>Feminino
                                </option>
                                <option value="outro" <?php echo $genero == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>E-mail:</strong></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold">Salvar Alterações</button>
                        <a href="perfilU.php" class="btn btn-danger fw-bold">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const telefone = document.getElementById('telefone').value;
            const telefoneRegex = /^[0-9]{11}$/;

            if (!telefoneRegex.test(telefone)) {
                alert('Telefone inválido. Certifique-se de que contém 11 dígitos.');
                e.preventDefault();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>