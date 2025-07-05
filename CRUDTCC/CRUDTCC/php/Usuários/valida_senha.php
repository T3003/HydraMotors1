<?php
session_start();
include '../config.php';

if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
    $con = conectar();
    $logid = $_SESSION['logid'];
    $sql = "SELECT logsenha FROM clilogin WHERE logid = :logid LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':logid', $logid, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $_POST['senha'] === $user['logsenha']) {
        $_SESSION['valida_perfil'] = true;
        header('Location: PerfilEdit.php');
        exit;
    } else {
        $msg = 'Senha incorreta.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Validação de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="container mt-5" style="max-width:400px;">
    <div class="card p-4">
        <h2 class="mb-3">Confirme sua senha</h2>
        <?php if ($msg): ?><div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="post">
            <input type="password" name="senha" class="form-control mb-3" required placeholder="Digite sua senha">
            <button type="submit" class="btn btn-primary w-100">Validar</button>
        </form>
        <a href="esqueci_senha.php" class="btn btn-link mt-2">Esqueci minha senha</a>
    </div>
</div>
</body>
</html>
