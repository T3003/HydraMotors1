<?php
// Arquivo para deletar veículo e redirecionar para a página anterior
include '../config.php';
// PHPMailer para envio de e-mail
require_once __DIR__ . '/../../../vendor/autoload.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['veiculo_id'])) {
    $con = conectar();
    $veiculo_id = (int)$_POST['veiculo_id'];
    // Busca dados do veículo e do anunciante
    $sqlInfo = "SELECT v.imagem_principal, v.imagem_adicional_1, v.imagem_adicional_2, v.nome, v.logid, c.logemail, c.logname FROM veiculos_venda v LEFT JOIN clilogin c ON v.logid = c.logid WHERE v.id = :id LIMIT 1";
    $stmtInfo = $con->prepare($sqlInfo);
    $stmtInfo->bindValue(':id', $veiculo_id, PDO::PARAM_INT);
    $stmtInfo->execute();
    $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    $imageDir = '../../Vendas/Imagens/';
    if ($info) {
        foreach (['imagem_principal', 'imagem_adicional_1', 'imagem_adicional_2'] as $imgField) {
            if (!empty($info[$imgField])) {
                $imgPath = $imageDir . $info[$imgField];
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }
        }
        // Envio de e-mail ao anunciante se ADM
        $adminUsers = ['user_6838d6be1afc45.64015583', 'user_6838dec504f9e3.45675952'];
        $isAdmin = (isset($_SESSION['logid']) && in_array($_SESSION['logid'], $adminUsers));
        $anuncianteEmail = $info['logemail'] ?? '';
        $anuncianteNome = $info['logname'] ?? '';
        $nomeVeiculo = $info['nome'] ?? '';
        if ($isAdmin && $anuncianteEmail) {
            // Limite de envio: 1 e-mail a cada 4 minutos por endereço
            $limite_minutos = 4;
            $limite_segundos = $limite_minutos * 60;
            $lastSendKey = 'delete_veiculo_last_send_' . md5(strtolower($anuncianteEmail));
            $agora = time();
            if (!isset($_SESSION[$lastSendKey]) || ($agora - $_SESSION[$lastSendKey]) >= $limite_segundos) {
                $_SESSION[$lastSendKey] = $agora;
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
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
                    $mail->addAddress($anuncianteEmail, $anuncianteNome);
                    $mail->isHTML(true);
                    $mail->Subject = 'Seu anúncio foi removido por um administrador - HydraMotors';
                    $mail->Body = "<h2>Olá, $anuncianteNome!</h2><p>Informamos que seu anúncio <b>\"$nomeVeiculo\"</b> foi removido por um administrador da plataforma por descumprimento das diretrizes ou denúncias de outros usuários.<br>Se desejar mais informações, entre em contato com o suporte.</p>";
                    $mail->AltBody = "Olá, $anuncianteNome!\nSeu anúncio '$nomeVeiculo' foi removido por um administrador da plataforma por descumprimento das diretrizes ou denúncias de outros usuários. Se desejar mais informações, entre em contato com o suporte.";
                    $mail->send();
                } catch (Exception $e) {
                    // Não interrompe o fluxo em caso de erro de e-mail
                }
            }
        }
    }
    // Deleta o veículo do banco (sem LIMIT para SQLite)
    $sql = "DELETE FROM veiculos_venda WHERE id = :id";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id', $veiculo_id, PDO::PARAM_INT);
    $stmt->execute();
    // Redireciona para página de confirmação
    header('Location: veiculo_deletado.php');
    exit;
} else {
    // Se acesso indevido, redireciona para listagem
    header('Location: /CRUDTCC/php/Vendas/Vendas.php');
    exit;
}
