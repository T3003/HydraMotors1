<?php
session_start();
if (isset($_SESSION['logid'])) {
    header('Location: /CRUDTCC/index.php');
    exit;
}
include_once(__DIR__ . '/../config.php');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $con = conectar();
        $sql = "SELECT * FROM clilogin WHERE logemail = :email LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && ($user['logsenha'] === $senha || (function_exists('password_verify') && password_verify($senha, $user['logsenha'])))) {
            $_SESSION['logid'] = $user['logid'];
            $_SESSION['logname'] = $user['logname'];
            header('Location: /CRUDTCC/index.php');
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - HydraMotors</title>
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
    </style>
</head>
<body>
    <div class="login-logo">
      <img id="loginLogoImg" src="/CRUDTCC/images/LOGO.png" alt="HYDRA">
    </div>
    <div class="login-container">
        <h2>Entrar no HydraMotors</h2>
        <?php if ($erro): ?>
            <div class="msg error"><?php echo $erro; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'ok'): ?>
            <div class="msg success">Cadastro realizado com sucesso! Faça login.</div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autocomplete="username">
            <label for="password">Senha</label>
            <input type="password" name="password" id="password" required autocomplete="current-password">
            <button type="submit">Entrar</button>
        </form>
        <div style="margin-top:18px;text-align:center;">
            <a href="esqueci_senha.php" style="color:#1976d2;text-decoration:underline;">Esqueci minha senha</a>
            <br>
            <a> Não possui conta </a>
            <a href="crudpf.php" class="btn-login-small">Criar Conta</a>
        </div>
    </div>
    <script>
      // Herdar darkmode do header.php e trocar logo conforme tema
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
