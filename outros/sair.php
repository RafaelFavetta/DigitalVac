<?php
session_start(); // Inicia a sessão

session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destroi a sessão atual

header("Location: ../index.php"); // Redireciona para a tela de login
exit;
?>