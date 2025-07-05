<?php
// Script para terminal: remove fotos de perfil de usuários que não existem mais no banco
// Execute: php limpa_fotos_orfas.php

include 'config.php';
include 'mysqlexecuta.php';

$con = conectar();
mysqli_select_db($con, 'hydramotors');

$dir = __DIR__ . '/Fotos de perfil/';
if (!is_dir($dir)) {
    echo "Diretório de fotos não encontrado.\n";
    exit(1);
}

$arquivos = scandir($dir);
$removidos = 0;
$total = 0;
foreach ($arquivos as $arq) {
    if ($arq === '.' || $arq === '..') continue;
    $total++;
    // Extrai o logid do nome do arquivo (antes do primeiro ponto)
    $id = preg_replace('/\..+$/', '', $arq);
    if (strpos($id, 'user_') !== 0) continue; // só arquivos de usuário
    $sql = "SELECT 1 FROM clilogin WHERE logid = '" . mysqli_real_escape_string($con, $id) . "' LIMIT 1";
    $res = mysqli_query($con, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        // Não existe mais, pode remover
        if (unlink($dir . $arq)) {
            echo "Removido: $arq\n";
            $removidos++;
        } else {
            echo "Erro ao remover: $arq\n";
        }
    }
}
echo "\nTotal de arquivos verificados: $total\n";
echo "Total de fotos removidas: $removidos\n";
