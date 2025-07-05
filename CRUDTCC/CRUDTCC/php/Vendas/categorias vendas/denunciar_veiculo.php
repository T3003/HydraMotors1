<?php
set_time_limit(0);
// denuncia_veiculo.php - Incrementa a coluna 'denun' do veículo apenas se o usuário ainda não denunciou
include '../../config.php';
session_start();

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['veiculo_id']) && 
    isset($_SESSION['logid']) && 
    isset($_POST['motivo']) && 
    trim($_POST['motivo']) !== ''
) {
    $id = intval($_POST['veiculo_id']);
    $userId = $_SESSION['logid'];
    $motivo = trim($_POST['motivo']);
    $con = conectar();
    // Busca valor atual e lista de usuários que já denunciaram
    $sql = "SELECT denun, denunid, motdenun FROM veiculos_venda WHERE id = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $denun = (int)$row['denun'];
        $denunid = $row['denunid'] ?? '';
        $motdenun = $row['motdenun'] ?? '';
        $ids = array_filter(array_map('trim', explode(',', $denunid)));
        // Buscar o nome do veículo para redirecionamento correto
        $sqlNome = "SELECT nome FROM veiculos_venda WHERE id = ? LIMIT 1";
        $stmtNome = $con->prepare($sqlNome);
        $stmtNome->execute([$id]);
        $nomeVeiculo = $stmtNome->fetchColumn();
        if (in_array($userId, $ids)) {
            // Já denunciou
            if ($nomeVeiculo) {
                header("Location: Carro.php?nome=" . urlencode($nomeVeiculo) . "&denuncia=ja_denunciou");
            } else {
                header("Location: Carro.php?denuncia=ja_denunciou");
            }
            exit;
        } else {
            // Adiciona o usuário ao final da lista, separado por vírgula
            $newDenunid = $denunid === '' ? $userId : $denunid . ',' . $userId;
            // Adiciona o motivo entre aspas, precedido do logid, separado por vírgula
            $motivoFormatado = '"' . $userId . ' ' . addslashes($motivo) . '"';
            $newMotdenun = $motdenun === '' ? $motivoFormatado : $motdenun . ',' . $motivoFormatado;
            $denun++;
            $sqlUpdate = "UPDATE veiculos_venda SET denun = ?, denunid = ?, motdenun = ? WHERE id = ?";
            $stmtUpdate = $con->prepare($sqlUpdate);
            $stmtUpdate->execute([$denun, $newDenunid, $newMotdenun, $id]);

            // Envia e-mail ao dono do anúncio a partir da terceira denúncia
            if ($denun === 3) {
                // Buscar logid do dono
                $sqlLogid = "SELECT logid FROM veiculos_venda WHERE id = ? LIMIT 1";
                $stmtLogid = $con->prepare($sqlLogid);
                $stmtLogid->execute([$id]);
                $logidDono = $stmtLogid->fetchColumn();
                if ($logidDono) {
                    // Buscar e-mail e anunban do dono
                    $sqlEmail = "SELECT logemail, anunban FROM clilogin WHERE logid = ? LIMIT 1";
                    $stmtEmail = $con->prepare($sqlEmail);
                    $stmtEmail->execute([$logidDono]);
                    $rowDono = $stmtEmail->fetch(PDO::FETCH_ASSOC);
                    $emailDono = $rowDono ? $rowDono['logemail'] : null;
                    $anunban = $rowDono ? (int)$rowDono['anunban'] : 0;
                    if ($emailDono) {
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
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
                            $mail->addAddress($emailDono);
                            $mail->isHTML(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->Subject = 'Alerta Inicial (Aviso Suave) - Denúncia de Anúncio';
                            $mail->Body = "<br>Olá! Recebemos uma denúncia relacionada a este anúncio. Ela está sendo analisada pela nossa equipe para garantir que todas as diretrizes sejam respeitadas. Como você for o anunciante, recomendamos revisar o conteúdo e fazer os ajustes necessários caso haja alguma inconsistência.";
                            $mail->AltBody = "Alerta Inicial (Aviso Suave): Olá! Recebemos uma denúncia relacionada a este anúncio. Ela está sendo analisada pela nossa equipe para garantir que todas as diretrizes sejam respeitadas. Se você for o anunciante, recomendamos revisar o conteúdo e fazer os ajustes necessários caso haja alguma inconsistência.";
                            $mail->send();
                            enviarEmailParaAdmins(
                                $con,
                                'Alerta de Denúncia (Aviso Suave) - Anúncio #' . $id . ' sob análise',
                                '<b>Alerta de Denúncia:</b><br>O anúncio <b>#' . $id . '</b> ("' . htmlspecialchars($nomeVeiculo) . '") atingiu 3 denúncias e está sob análise. O dono foi notificado automaticamente. Acompanhe possíveis reincidências para ação administrativa.<br><br><b>O dono possui ' . $anunban . ' anúncio(s) banido(s).</b>',
                                'Alerta de Denúncia: O anúncio #' . $id . ' ("' . $nomeVeiculo . '") atingiu 3 denúncias e está sob análise. O dono foi notificado automaticamente. Acompanhe possíveis reincidências para ação administrativa. O dono possui ' . $anunban . ' anúncio(s) banido(s).',
                                $anunban
                            );
                        } catch (Exception $e) {
                            // Falha ao enviar e-mail, mas não impede o fluxo
                        }
                    }
                }
            }
            // Envia e-mail ao dono do anúncio a partir da sexta denúncia
            if ($denun === 6) {
                $sqlLogid = "SELECT logid FROM veiculos_venda WHERE id = ? LIMIT 1";
                $stmtLogid = $con->prepare($sqlLogid);
                $stmtLogid->execute([$id]);
                $logidDono = $stmtLogid->fetchColumn();
                if ($logidDono) {
                    $sqlEmail = "SELECT logemail, anunban FROM clilogin WHERE logid = ? LIMIT 1";
                    $stmtEmail = $con->prepare($sqlEmail);
                    $stmtEmail->execute([$logidDono]);
                    $rowDono = $stmtEmail->fetch(PDO::FETCH_ASSOC);
                    $emailDono = $rowDono ? $rowDono['logemail'] : null;
                    $anunban = $rowDono ? (int)$rowDono['anunban'] : 0;
                    if ($emailDono) {
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
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
                            $mail->addAddress($emailDono);
                            $mail->isHTML(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->Subject = 'Notificação de Irregularidade (Aviso Moderado) - Denúncia de Anúncio';
                            $mail->Body = "<b>Notificação de Irregularidade (Aviso Moderado):</b><br>Aviso: Este anúncio foi denunciado por possíveis violações às nossas políticas. Ele continuará visível enquanto passa por análise. Recomendamos que o anunciante revise o conteúdo e fique atento a qualquer comunicação da equipe de suporte.";
                            $mail->AltBody = "Notificação de Irregularidade (Aviso Moderado): Aviso: Este anúncio foi denunciado por possíveis violações às nossas políticas. Ele continuará visível enquanto passa por análise. Recomendamos que o anunciante revise o conteúdo e fique atento a qualquer comunicação da equipe de suporte.";
                            $mail->send();
                            enviarEmailParaAdmins(
                                $con,
                                'Notificação Moderada - Anúncio #' . $id . ' com 6 denúncias',
                                '<b>Notificação Moderada:</b><br>O anúncio <b>#' . $id . '</b> ("' . htmlspecialchars($nomeVeiculo) . '") atingiu 6 denúncias. O dono foi notificado. Recomenda-se revisão manual e possível contato.<br><br><b>O dono possui ' . $anunban . ' anúncio(s) banido(s).</b>',
                                'Notificação Moderada: O anúncio #' . $id . ' ("' . $nomeVeiculo . '") atingiu 6 denúncias. O dono foi notificado. Recomenda-se revisão manual e possível contato. O dono possui ' . $anunban . ' anúncio(s) banido(s).',
                                $anunban
                            );
                        } catch (Exception $e) {
                            // Falha ao enviar e-mail, mas não impede o fluxo
                        }
                    }
                }
            }
            // Envia e-mail ao dono do anúncio a partir da nona denúncia
            if ($denun === 9) {
                $sqlLogid = "SELECT logid FROM veiculos_venda WHERE id = ? LIMIT 1";
                $stmtLogid = $con->prepare($sqlLogid);
                $stmtLogid->execute([$id]);
                $logidDono = $stmtLogid->fetchColumn();
                if ($logidDono) {
                    $sqlEmail = "SELECT logemail, anunban FROM clilogin WHERE logid = ? LIMIT 1";
                    $stmtEmail = $con->prepare($sqlEmail);
                    $stmtEmail->execute([$logidDono]);
                    $rowDono = $stmtEmail->fetch(PDO::FETCH_ASSOC);
                    $emailDono = $rowDono ? $rowDono['logemail'] : null;
                    $anunban = $rowDono ? (int)$rowDono['anunban'] : 0;
                    if ($emailDono) {
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
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
                            $mail->addAddress($emailDono);
                            $mail->isHTML(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->Subject = 'Ação Imediata (Aviso Crítico) - Denúncia de Anúncio';
                            $mail->Body = "<b>Ação Imediata (Aviso Crítico):</b><br>Importante: Este anúncio foi sinalizado por violações graves às nossas diretrizes. A exibição continua ativa durante a revisão. O anunciante pode estar sujeito a medidas administrativas.";
                            $mail->AltBody = "Ação Imediata (Aviso Crítico): Importante: Este anúncio foi sinalizado por violações graves às nossas diretrizes. A exibição continua ativa durante a revisão. O anunciante pode estar sujeito a medidas administrativas, podendo ter sa conta marcada como não confiavel ou até excluido.";
                            $mail->send();
                            enviarEmailParaAdmins(
                                $con,
                                'Alerta Crítico - Anúncio #' . $id . ' com 9 denúncias',
                                '<b>Alerta Crítico:</b><br>O anúncio <b>#' . $id . '</b> ("' . htmlspecialchars($nomeVeiculo) . '") atingiu 9 denúncias. O dono foi notificado. Ação administrativa pode ser necessária.<br><br><b>O dono possui ' . $anunban . ' anúncio(s) banido(s).</b>',
                                'Alerta Crítico: O anúncio #' . $id . ' ("' . $nomeVeiculo . '") atingiu 9 denúncias. O dono foi notificado. Ação administrativa pode ser necessária. O dono possui ' . $anunban . ' anúncio(s) banido(s).',
                                $anunban
                            );
                        } catch (Exception $e) {
                            // Falha ao enviar e-mail, mas não impede o fluxo
                        }
                    }
                }
            }
            // Envia e-mail ao dono do anúncio a partir da décima denúncia
            if ($denun === 10) {
                $sqlLogid = "SELECT logid FROM veiculos_venda WHERE id = ? LIMIT 1";
                $stmtLogid = $con->prepare($sqlLogid);
                $stmtLogid->execute([$id]);
                $logidDono = $stmtLogid->fetchColumn();
                if ($logidDono) {
                    $sqlEmail = "SELECT logemail, anunban FROM clilogin WHERE logid = ? LIMIT 1";
                    $stmtEmail = $con->prepare($sqlEmail);
                    $stmtEmail->execute([$logidDono]);
                    $rowDono = $stmtEmail->fetch(PDO::FETCH_ASSOC);
                    $emailDono = $rowDono ? $rowDono['logemail'] : null;
                    $anunban = $rowDono ? (int)$rowDono['anunban'] : 0;
                    if ($emailDono) {
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
                        require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
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
                            $mail->addAddress($emailDono);
                            $mail->isHTML(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->Subject = 'Penalidade Final (Aviso de Confiabilidade) - Anúncio Removido';
                            $mail->Body = "<b>Penalidade Final (Aviso de Confiabilidade):</b><br>Alerta: Este anúncio foi oculto por violações severas às regras da plataforma. A conta responsável permanece ativa, mas foi marcada como não confiável, e todos os anúncios futuros exibirão esse alerta permanentemente. Recomendamos entrar em contato com o suporte.";
                            $mail->AltBody = "Penalidade Final (Aviso de Confiabilidade): Alerta: Este anúncio foi oculto por violações severas às regras da plataforma. A conta responsável permanece ativa, mas foi marcada como não confiável, e todos os anúncios futuros exibirão esse alerta permanentemente. Recomendamos entrar em contato com o suporte  para obter mais informações.";
                            $mail->send();
                            enviarEmailParaAdmins(
                                $con,
                                'Penalidade Final - Anúncio #' . $id . ' removido por denúncias',
                                '<b>Penalidade Final:</b><br>O anúncio <b>#' . $id . '</b> ("' . htmlspecialchars($nomeVeiculo) . '") atingiu 10 denúncias e foi removido. O dono foi notificado e a conta marcada como não confiável.<br><br><b>O dono possui ' . $anunban . ' anúncio(s) banido(s).</b>',
                                'Penalidade Final: O anúncio #' . $id . ' ("' . $nomeVeiculo . '") atingiu 10 denúncias e foi removido. O dono foi notificado e a conta marcada como não confiável. O dono possui ' . $anunban . ' anúncio(s) banido(s).',
                                $anunban
                            );
                        } catch (Exception $e) {
                            // Falha ao enviar e-mail, mas não impede o fluxo
                        }
                    }
                    // Incrementa o campo anunban do dono do anúncio
                    $sqlBan = "UPDATE clilogin SET anunban = anunban + 1 WHERE logid = ?";
                    $stmtBan = $con->prepare($sqlBan);
                    $stmtBan->execute([$logidDono]);
                }
            }
            if ($nomeVeiculo) {
                header("Location: Carro.php?nome=" . urlencode($nomeVeiculo) . "&denuncia=ok");
            } else {
                header("Location: Carro.php?denuncia=ok");
            }
            exit;
        }
    } else {
        header("Location: Carro.php?denuncia=erro");
        exit;
    }
} else {
    header("Location: /CRUDTCC/Vendas/");
    exit;
}

// Função para enviar e-mail para todos os administradores
function enviarEmailParaAdmins($con, $assunto, $mensagemHtml, $mensagemTxt, $anunban = 0) {
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
    $sqlADM = "SELECT logemail FROM clilogin WHERE Adm = 1 AND logemail IS NOT NULL AND logemail != ''";
    $resADM = $con->query($sqlADM);
    if ($resADM) {
        while ($rowADM = $resADM->fetch(PDO::FETCH_ASSOC)) {
            $mailADM = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mailADM->isSMTP();
                $mailADM->Host = 'smtp.gmail.com';
                $mailADM->SMTPAuth = true;
                $mailADM->Username = 'hydra.0.motors@gmail.com';
                $mailADM->Password = 'rhbn asrn vwqp rrev';
                $mailADM->SMTPSecure = 'tls';
                $mailADM->Port = 587;
                $mailADM->setFrom('hydra.0.motors@gmail.com', 'HydraMotors');
                $mailADM->addAddress($rowADM['logemail']);
                $mailADM->isHTML(true);
                $mailADM->CharSet = 'UTF-8';
                $mailADM->Subject = $assunto;
                $mailADM->Body = $mensagemHtml;
                $mailADM->AltBody = $mensagemTxt;
                $mailADM->send();
            } catch (Exception $e) {
                // Falha ao enviar e-mail para ADM, ignora
            }
        }
    }
}
