<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

session_start();

// Gera código de 6 dígitos
$codigo = rand(100000, 999999);

// Dados do formulário
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Salva dados na sessão para usar após verificação
$_SESSION['cadastro_temp'] = [
    'nome' => $nome,
    'email' => $email,
    'password' => $password,
    'confirmar_senha' => $confirmar_senha,
    'codigo' => $codigo
];
$_SESSION['cadastro_temp_time'] = time();

if (!$email) {
    die('E-mail não fornecido.');
}

// Limite de envio: 1 e-mail a cada 4 minutos por endereço
$limite_minutos = 4;
$limite_segundos = $limite_minutos * 60;
$lastSendKey = 'cadastro_last_send_' . md5(strtolower($email));
$agora = time();
if (isset($_SESSION[$lastSendKey]) && ($agora - $_SESSION[$lastSendKey]) < $limite_segundos) {
    $resta = $limite_segundos - ($agora - $_SESSION[$lastSendKey]);
    header('Location: crudpf.php?erro=limite&resta=' . $resta);
    exit;
} else {
    $_SESSION[$lastSendKey] = $agora;
}

$mail = new PHPMailer(true);

try {
    // Configurações do servidor
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hydra.0.motors@gmail.com';
    $mail->Password = 'rhbn asrn vwqp rrev';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Remetente e destinatário
    $mail->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
    $mail->addAddress($email);

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'BEM VINDO - HydraMotors';
    $mail->Body    = "<h2>Seu código de verificação é:</h2><p style='font-size:24px;'><strong>$codigo</strong></p>";
    $mail->AltBody = "Seu código de verificação é: $codigo";

    $mail->send();
    // Redireciona para página de verificação
    header('Location: verificar_codigo.php');
    exit;
} catch (Exception $e) {
    echo "<div style='color:red;text-align:center;'>Erro ao enviar e-mail: {$mail->ErrorInfo}</div>";
}
