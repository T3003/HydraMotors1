<?php
include '../../header.php';
// Herança de modo escuro igual login.php
?>
<script>
(function() {
  var isDark = localStorage.getItem('dark-mode') === '1';
  document.body.classList.toggle('dark-mode', isDark);
  document.body.classList.toggle('light-mode', !isDark);
  // Troca logo se existir
  var logoImg = document.getElementById('loginLogoImg');
  if (logoImg) {
    logoImg.src = isDark ? '/CRUDTCC/images/LOGODM.png' : '/CRUDTCC/images/LOGO.png';
  }
})();
</script>
<?php
if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

$con = conectar();
$logid = $_SESSION['logid'];
$sqlUser = "SELECT logemail FROM clilogin WHERE logid = :logid LIMIT 1";
$stmtUser = $con->prepare($sqlUser);
$stmtUser->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$user_email = $user['logemail'] ?? '';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_email'])) {
    $novo_email = $_POST['novo_email'];
    if ($novo_email === $user_email) {
        $msg = 'O novo email deve ser diferente do atual.';
    } else {
        // Limite de envio: 1 e-mail a cada 4 minutos por endereço
        $limite_minutos = 4;
        $limite_segundos = $limite_minutos * 60;
        $lastSendKey = 'alterar_email_last_send_' . md5(strtolower($novo_email));
        $agora = time();
        if (isset($_SESSION[$lastSendKey]) && ($agora - $_SESSION[$lastSendKey]) < $limite_segundos) {
            $resta = $limite_segundos - ($agora - $_SESSION[$lastSendKey]);
            $min = floor($resta / 60);
            $seg = $resta % 60;
            $msg = 'Aguarde ' . ($min > 0 ? $min . ' min ' : '') . $seg . ' seg para reenviar o código.';
        } else {
            $codigo_verificacao = rand(100000, 999999);
            $_SESSION['novo_email'] = $novo_email;
            $_SESSION['codigo_verificacao_email'] = $codigo_verificacao;
            $_SESSION[$lastSendKey] = $agora;
            // Envia o código para o novo email
            require_once '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
            require_once '../../vendor/phpmailer/phpmailer/src/SMTP.php';
            require_once '../../vendor/phpmailer/phpmailer/src/Exception.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'hydra.0.motors@gmail.com';
                $mail->Password = 'rhbn asrn vwqp rrev';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
                $mail->addAddress($novo_email);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Código de verificação de email';
                $mail->Body = "<h2>Seu código de verificação é:</h2><p style='font-size:24px;'><strong>$codigo_verificacao</strong></p>";
                $mail->AltBody = "Seu código de verificação é: $codigo_verificacao";
                $mail->send();
                header('Location: verifica_email.php');
                exit;
            } catch (Exception $e) {
                $msg = 'Erro ao enviar código de verificação: ' . $mail->ErrorInfo;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Alterar Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body.dark-mode {
            background-color: #181a1b !important;
            color: #e8e6e3 !important;
        }
        .dark-mode .card {
            background-color: #23272b !important;
            color: #e8e6e3 !important;
        }
        .dark-mode .form-control {
            background-color: #23272b !important;
            color: #e8e6e3 !important;
            border-color: #444950 !important;
        }
        .dark-mode .btn-primary {
            background-color: #375a7f !important;
            border-color: #375a7f !important;
        }
        .dark-mode .btn-secondary {
            background-color: #444950 !important;
            border-color: #444950 !important;
        }
        .dark-mode .alert-info {
            background-color: #222b3a !important;
            color: #e8e6e3 !important;
            border-color: #375a7f !important;
        }
    </style>
</head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <div class="d-flex justify-content-end mb-2">
        <button id="toggle-dark" class="btn btn-outline-secondary btn-sm">Modo Escuro</button>
    </div>
    <div class="card p-4">
        <h2 class="mb-3">Alterar Email</h2>
        <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="novo_email" class="form-label">Novo Email</label>
                <input type="email" name="novo_email" id="novo_email" class="form-control" required placeholder="Digite o novo email">
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar Código</button>
            <a href="PerfilEdit.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </form>
    </div>
</div>
<script>
    // Herdar tema do header.php usando a mesma chave do localStorage
    const DARK_KEY = 'headerDarkMode';
    const btn = document.getElementById('toggle-dark');
    function setDarkMode(on) {
        document.body.classList.toggle('dark-mode', on);
        btn.textContent = on ? 'Modo Claro' : 'Modo Escuro';
        localStorage.setItem(DARK_KEY, on ? '1' : '0');
    }
    btn.addEventListener('click', () => {
        setDarkMode(!document.body.classList.contains('dark-mode'));
    });
    // Ajusta o texto do botão ao carregar, sem alterar o estado do tema
    document.addEventListener('DOMContentLoaded', function() {
        btn.textContent = document.body.classList.contains('dark-mode') ? 'Modo Claro' : 'Modo Escuro';
    });
</script>
</body>
</html>
