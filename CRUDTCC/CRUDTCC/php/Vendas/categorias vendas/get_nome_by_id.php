<?php
require_once __DIR__ . '/../../config.php';
$con = conectar();
$id = isset($argv[1]) ? intval($argv[1]) : 0;
if ($id > 0) {
    $stmt = $con->prepare('SELECT nome FROM veiculos_venda WHERE id = ?');
    $stmt->execute([$id]);
    $nome = $stmt->fetchColumn();
    echo $nome ? $nome : 'NOT_FOUND';
} else {
    echo 'NO_ID';
}
