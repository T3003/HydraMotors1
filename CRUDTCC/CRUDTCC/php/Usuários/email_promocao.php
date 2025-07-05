<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';

function enviarEmailPromocaoCargo($email, $nome, $novoCargo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hydra.0.motors@gmail.com';
        $mail->Password = 'rhbn asrn vwqp rrev';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
        $mail->addAddress($email, $nome);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        if ($novoCargo === 'Adm') {
            $mail->Subject = 'Parabéns! Você agora é Administrador do HydraMotors';
            $mail->Body = '<b>Olá, ' . htmlspecialchars($nome) . '!</b><br><br>Você foi promovido a <b>Administrador</b> no sistema HydraMotors.<br><br>Como Administrador, você pode:<ul>'
                .'<li>Editar veículos comuns e de outros administradores</li>'
                .'<li>Deletar veículos</li>'
                .'<li>Deletar anúncios</li>'
                .'<li>Banir e desbanir contas</li>'
                .'<li>Banir e desbanir anúncios</li>'
                .'<li>Promover e remover permissões de outros usuários para administrador ou revisor</li>'
                .'</ul><br>Use seus poderes com responsabilidade!';
            $mail->AltBody = 'Olá, ' . $nome . '! Você foi promovido a Administrador no sistema HydraMotors. Como Administrador, você pode editar veículos comuns e de outros administradores, deletar veículos, deletar anúncios, banir/desbanir contas e anúncios, promover e remover permissões de outros usuários para administrador ou revisor. Use seus poderes com responsabilidade!';
        } else if ($novoCargo === 'Rev') {
            $mail->Subject = 'Parabéns! Você agora é Revisor do HydraMotors';
            $mail->Body = '<b>Olá, ' . htmlspecialchars($nome) . '!</b><br><br>Você foi promovido a <b>Revisor</b> no sistema HydraMotors.<br><br>Como Revisor, você pode:<ul><li>Editar veículos comuns</li></ul><br>Obrigado por contribuir para a qualidade da plataforma!';
            $mail->AltBody = 'Olá, ' . $nome . '! Você foi promovido a Revisor no sistema HydraMotors. Como Revisor, você pode editar veículos comuns. Obrigado por contribuir para a qualidade da plataforma!';
        } else {
            return;
        }
        $mail->send();
    } catch (Exception $e) {
        // Falha ao enviar e-mail, ignora
    }
}
