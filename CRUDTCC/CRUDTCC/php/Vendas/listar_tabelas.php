<?php
// Sempre use a função conectar() do config.php central
require_once __DIR__ . '/../../config.php';
$pdo = conectar();
if (!$pdo) {
    echo "Erro ao conectar ao banco de dados.";
    exit;
}
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
echo '<pre>';
print_r($tables);
echo '</pre>';
