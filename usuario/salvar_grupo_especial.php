<?php
header('Content-Type: application/json');
session_start();

// Simula sucesso sem salvar nada
echo json_encode(['success' => true]);
exit;
?>
