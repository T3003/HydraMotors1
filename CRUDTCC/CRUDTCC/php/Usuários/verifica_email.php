<?php
session_start();
include '../config.php';

if (!isset($_SESSION['logid']) || !isset($_SESSION['codigo_verificacao_email']) || !isset($_SESSION['novo_email'])) {
    header('Location: PerfilEdit.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verificar_codigo'])) {
    $codigo = $_POST['codigo'] ?? '';
    if (isset($_SESSION['codigo_verificacao_email']) && $codigo == $_SESSION['codigo_verificacao_email']) {
        $con = conectar();
        $logid = $_SESSION['logid'];
        $novo_nome = $_SESSION['novo_nome'] ?? '';
        $novo_email = $_SESSION['novo_email'];
        $nova_senha = $_SESSION['nova_senha'] ?? '';
        $foto = '';
        // Busca foto atual
        $sqlUser = "SELECT logpfp FROM clilogin WHERE logid = :logid LIMIT 1";
        $stmtUser = $con->prepare($sqlUser);
        $stmtUser->bindValue(':logid', $logid, PDO::PARAM_STR);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $foto = $user['logpfp'] ?? 'default.jpg';
        // Atualiza dados
        $sqlUpdate = "UPDATE clilogin SET logname=:nome, logemail=:email, logsenha=:senha WHERE logid = :logid";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bindValue(':nome', $novo_nome, PDO::PARAM_STR);
        $stmtUpdate->bindValue(':email', $novo_email, PDO::PARAM_STR);
        $stmtUpdate->bindValue(':senha', $nova_senha, PDO::PARAM_STR);
        $stmtUpdate->bindValue(':logid', $logid, PDO::PARAM_STR);
        if ($stmtUpdate->execute()) {
            // Código só pode ser usado uma vez
            unset($_SESSION['codigo_verificacao_email'], $_SESSION['novo_email'], $_SESSION['novo_nome'], $_SESSION['nova_senha']);
            $msg = 'Email atualizado com sucesso!';
            header('Location: PerfilEdit.php?email_atualizado=1');
            exit;
        } else {
            $msg = 'Erro ao atualizar email.';
        }
    } else {
        // Código inválido, remove para não permitir nova tentativa
        unset($_SESSION['codigo_verificacao_email']);
        $msg = 'Código incorreto ou expirado. Solicite um novo código.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificar Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow p-4" style="max-width: 400px; margin: 0 auto;">
        <h2 class="mb-3">Verificação de Email</h2>
        <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="codigo" class="form-label">Digite o código enviado para seu novo email:</label>
                <input type="text" name="codigo" id="codigo" class="form-control" required>
            </div>
            <button type="submit" name="verificar_codigo" class="btn btn-primary">Verificar</button>
            <a href="PerfilEdit.php" class="btn btn-secondary ms-2">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
