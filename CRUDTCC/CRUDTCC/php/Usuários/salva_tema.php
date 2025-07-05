<?php
// salva_tema.php
session_start();
include '../../config.php';

if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

$con = conectar();
$logid = $_SESSION['logid'];

// Atualiza o tema do usuário
if (isset($_POST['tema'])) {
    $tema = $_POST['tema'];
    $sql = "UPDATE clilogin SET tema = :tema WHERE logid = :logid";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':tema', $tema, PDO::PARAM_STR);
    $stmt->bindValue(':logid', $logid, PDO::PARAM_STR);
    $stmt->execute();
    $_SESSION['tema'] = $tema;
}
?>
