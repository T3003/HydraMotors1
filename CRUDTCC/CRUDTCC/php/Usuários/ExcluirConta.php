<?php
session_start();
include '../config.php';

if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

$con = conectar();
$logid = $_SESSION['logid'];

// Exclui o usuário
$sql = "DELETE FROM clilogin WHERE logid = :logid";
$stmt = $con->prepare($sql);
$stmt->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmt->execute();

// Destroi a sessão
session_destroy();

// Redireciona para a página inicial
header('Location: /CRUDTCC/index.php');
exit;
