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
    $mail->CharSet = 'UTF-8';

    // Remetente e destinatário
    $mail->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
    $mail->addAddress($email);

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Redefinir Senha - HydraMotors';
    $mail->Body    = "<h2>Seu código de verificação é:</h2><p style='font-size:24px;'><strong>$codigo</strong></p>";
    $mail->AltBody = "Seu código de verificação é: $codigo";

    $mail->send();
    // Redireciona para página de verificação
    header('Location: verificar_codigo.php');
    exit;
} catch (Exception $e) {
    echo "<div style='color:red;text-align:center;'>Erro ao enviar e-mail: {$mail->ErrorInfo}</div>";
}

// Recebe o e-mail, gera e envia o código, redireciona para esqueci_senha.php?recupera=1
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    include '../config.php';
    $con = conectar();
    $email = trim($_POST['email']);
    $sql = "SELECT logid FROM clilogin WHERE logemail = :email LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $codigo = rand(100000, 999999);
        $_SESSION['recupera_email'] = $email;
        $_SESSION['recupera_codigo'] = $codigo;
        $_SESSION['recupera_logid'] = $user['logid'];
        // Envio do código por email
        mail($email, 'Código de recuperação de senha', "Seu código: $codigo");
        header('Location: esqueci_senha.php?recupera=1');
        exit;
    } else {
        $_SESSION['erro_recupera'] = 'Email não encontrado.';
        header('Location: esqueci_senha.php');
        exit;
    }
} else {
    header('Location: esqueci_senha.php');
    exit;
}
