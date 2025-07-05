<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$erro = '';
$sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once(__DIR__ . '/../config.php');
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if ($email === '') {
        $erro = 'Informe o e-mail.';
    } else {
        $con = conectar();
        $sql = "SELECT * FROM clilogin WHERE logemail = :email LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $erro = 'E-mail não encontrado.';
        } else {
            // Limite de envio: 1 e-mail a cada 4 minutos por endereço
            $limite_minutos = 4;
            $limite_segundos = $limite_minutos * 60;
            $lastSendKey = 'recupera_last_send_' . md5(strtolower($email));
            $agora = time();
            if (isset($_SESSION[$lastSendKey]) && ($agora - $_SESSION[$lastSendKey]) < $limite_segundos) {
                $resta = $limite_segundos - ($agora - $_SESSION[$lastSendKey]);
                $min = floor($resta / 60);
                $seg = $resta % 60;
                $erro = 'Aguarde ' . ($min > 0 ? $min . ' min ' : '') . $seg . ' seg para reenviar o e-mail.';
            } else {
                // Gera código e envia e-mail
                $codigo = rand(100000, 999999);
                $_SESSION['recupera_email'] = $email;
                $_SESSION['recupera_codigo'] = $codigo;
                $_SESSION['recupera_codigo_time'] = $agora;
                $_SESSION['recupera_logid'] = $user['logid'];
                $_SESSION[$lastSendKey] = $agora;
                // Envia e-mail
                require_once __DIR__ . '/../../vendor/autoload.php';
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'hydra.0.motors@gmail.com';
                    $mail->Password = 'rhbn asrn vwqp rrev';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperação de Senha - HydraMotors';
                    $mail->Body    = "<h2>Seu código de recuperação é:</h2><p style='font-size:24px;'><strong>$codigo</strong></p>";
                    $mail->AltBody = "Seu código de recuperação é: $codigo";
                    $mail->send();
                    // Nenhum output antes deste header!
                    header('Location: redefinir_senha.php');
                    exit;
                } catch (Exception $e) {
                    $erro = 'Erro ao enviar e-mail: ' . $mail->ErrorInfo . '<br>Detalhe: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - HydraMotors</title>
    <link href="/CRUDTCC/css/login.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <!-- NÃO incluir header.php aqui! -->
</head>
<body>
    <?php include_once("../../header.php"); ?>
    <div class="login-container">
        <h2>Recuperar Senha</h2>
        <?php if ($erro): ?>
            <div class="msg error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="email">Digite seu e-mail cadastrado</label>
            <input type="email" name="email" id="email" required autocomplete="username">
            <button type="submit">Enviar código</button>
        </form>
        <div style="margin-top:18px;text-align:center;">
            <a href="login.php" style="color:#1976d2;text-decoration:underline;">Voltar ao login</a>
        </div>
    </div>
    <script>
      // Herdar darkmode do header.php
      (function() {
        var theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        if (localStorage.getItem('dark-mode') === '1') {
          document.body.classList.add('dark-mode');
          document.body.classList.remove('light-mode');
        } else {
          document.body.classList.remove('dark-mode');
          document.body.classList.add('light-mode');
        }
      })();
    </script>
</body>
</html>
