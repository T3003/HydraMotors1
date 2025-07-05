<?php
session_start();
if (!isset($_SESSION['recupera_logid'])) {
    header('Location: esqueci_senha.php');
    exit;
}
// Verifica expiração do código (5 minutos)
if (!isset($_SESSION['recupera_codigo_time']) || (time() - $_SESSION['recupera_codigo_time'] > 300)) {
    unset($_SESSION['recupera_logid'], $_SESSION['recupera_email'], $_SESSION['recupera_codigo'], $_SESSION['recupera_codigo_time']);
    header('Location: esqueci_senha.php?expirado=1');
    exit;
}
$msg = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoInformado = trim($_POST['codigo_verificacao'] ?? '');
    $nova = $_POST['nova_senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';
    $codigoGerado = $_SESSION['recupera_codigo'] ?? '';
    if ($codigoInformado === '' || $nova === '' || $confirma === '') {
        $erro = 'Preencha todos os campos.';
    } elseif ($codigoInformado != $codigoGerado) {
        $erro = 'Código incorreto.';
    } elseif ($nova !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        include '../config.php';
        $con = conectar();
        $hash = password_hash($nova, PASSWORD_DEFAULT);
        $sql = "UPDATE clilogin SET logsenha = :senha WHERE logid = :logid";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':senha', $hash, PDO::PARAM_STR);
        $stmt->bindValue(':logid', $_SESSION['recupera_logid'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            unset($_SESSION['recupera_logid'], $_SESSION['recupera_email'], $_SESSION['recupera_codigo'], $_SESSION['recupera_codigo_time']);
            $msg = 'Senha redefinida com sucesso! <a href="login.php">Fazer login</a>';
        } else {
            $erro = 'Erro ao redefinir senha.';
        }
    }
}
$email = $_SESSION['recupera_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - HydraMotors</title>
    <link href="/CRUDTCC/css/login.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <style>
      .login-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 38px;
        margin-bottom: 18px;
      }
      .login-logo img {
        height: 110px;
        border-radius: 18px;
        background: #fff0;
        transition: filter 0.2s;
        filter: brightness(0.92) drop-shadow(0 0 2px #00e0ff);
      }
      body.dark-mode .login-logo img {
        filter: brightness(0.92) drop-shadow(0 0 2px #a259ff);
      }
      .btn-login-small {
        display: inline-block;
        padding: 7px 22px;
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        color: #fff !important;
        border: none;
        border-radius: 6px;
        font-size: 0.98rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        text-decoration: none;
        transition: background 0.2s;
      }
      .btn-login-small:hover {
        background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
        color: #fff !important;
        text-decoration: none;
      }
      .msg.error {
        background: #ffe3e3;
        color: #a33a3a;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 12px;
      }
      .msg.success, .alert-info {
        background: #e3ffe9;
        color: #2a7a4e;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 12px;
      }
      .form-control {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 12px;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-size: 1em;
        background: #f9f9f9;
        color: #222;
        transition: border 0.3s, background 0.3s, color 0.3s;
      }
      body.dark-mode .form-control {
        background: #23272b;
        color: #fff;
        border-color: #444;
      }
      .btn-primary {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        color: #fff;
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
      }
      .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
    </style>
</head>
<body>
<div class="login-logo">
  <img id="loginLogoImg" src="/CRUDTCC/images/LOGO.png" alt="HYDRA">
</div>
<div class="login-container">
    <h2>Redefinir Senha</h2>
    <p>Enviamos um código para o e-mail <b><?= htmlspecialchars($email) ?></b>.</p>
    <?php if ($msg): ?>
        <div class="alert-info msg success"><?= $msg ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="msg error"><?= $erro ?></div>
    <?php endif; ?>
    <form method="post">
        <div style="text-align:center;">
            <label for="codigo_verificacao" style="display:block;">Código de verificação</label>
            <input type="text" name="codigo_verificacao" id="codigo_verificacao" class="form-control mb-2" maxlength="6" pattern="[0-9]{6}" required placeholder="Código de 6 dígitos" style="max-width:220px; margin:0 auto 12px auto; text-align:center;">
        </div>
        <label for="nova_senha">Nova senha</label>
        <input type="password" name="nova_senha" id="nova_senha" class="form-control mb-2" required placeholder="Nova senha">
        <label for="confirma_senha">Confirme a nova senha</label>
        <input type="password" name="confirma_senha" id="confirma_senha" class="form-control mb-2" required placeholder="Confirme a nova senha">
        <button type="submit" class="btn-primary mt-2">Redefinir Senha</button>
    </form>
    <div style="margin-top:18px;text-align:center;">
        <button type="button" class="btn-login-small" onclick="history.back()">Voltar</button>
    </div>
</div>
<script>
// Herdar darkmode igual login.php e trocar logo
(function() {
  var isDark = localStorage.getItem('dark-mode') === '1';
  document.body.classList.toggle('dark-mode', isDark);
  document.body.classList.toggle('light-mode', !isDark);
  var logoImg = document.getElementById('loginLogoImg');
  if (logoImg) {
    logoImg.src = isDark ? '/CRUDTCC/images/LOGODM.png' : '/CRUDTCC/images/LOGO.png';
  }
})();
</script>
</body>
</html>
