<?php
// Sempre use a função conectar() do config.php central
require_once __DIR__ . '/../../config.php';
$pdo = conectar();
if (!$pdo) {
    echo "Erro ao conectar ao banco de dados.";
    exit;
}

// Consultar as categorias
$sql = "SELECT * FROM Category";
$stmt = $pdo->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>