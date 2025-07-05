<?php
session_start();
// Se não houver dados de cadastro, volta para o cadastro
if (!isset($_SESSION['cadastro_temp'])) {
    header('Location: crudpf.php');
    exit;
}
$erro = '';
// Se o código foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_verificacao'])) {
    $codigoInformado = trim($_POST['codigo_verificacao']);
    $cadastro = $_SESSION['cadastro_temp'];
    $codigoGerado = $cadastro['codigo'];
    $tempoEnvio = $_SESSION['cadastro_temp_time'] ?? time();
    // Verifica tempo (5 minutos)
    if (time() - $tempoEnvio > 300) {
        unset($_SESSION['cadastro_temp']);
        unset($_SESSION['cadastro_temp_time']);
        $_SESSION['erro_verificacao'] = 'Tempo expirado. Tente novamente.';
        header('Location: crudpf.php');
        exit;
    }
    if ($codigoInformado == $codigoGerado) {
        // Inclui o script de cadastro real
        $_POST = $cadastro;
        unset($_SESSION['cadastro_temp']);
        unset($_SESSION['cadastro_temp_time']);
        require 'processauser.php';
        exit;
    } else {
        $erro = 'Código incorreto. Tente novamente.';
    }
}
// Salva o tempo do envio do código na sessão
if (!isset($_SESSION['cadastro_temp_time'])) {
    $_SESSION['cadastro_temp_time'] = time();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificação de E-mail</title>
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <style>
        .verificacao-box {
            max-width: 400px;
            margin: 60px auto;
            background: #222;
            border-radius: 16px;
            padding: 32px 24px;
            color: #fff;
            box-shadow: 0 4px 24px #0005;
        }
        .verificacao-box h2 {
            text-align: center;
            margin-bottom: 18px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #444;
            background: #2c2c2c;
            color: #fff;
            margin-bottom: 16px;
            font-size: 1rem;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
        }
        .erro {
            color: #ff5252;
            text-align: center;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="verificacao-box">
    <h2>Verificação de E-mail</h2>
    <p>Enviamos um código de 6 dígitos para o e-mail <b><?= htmlspecialchars($_SESSION['cadastro_temp']['email']) ?></b>.</p>
    <form method="post">
        <input type="text" name="codigo_verificacao" class="form-control" maxlength="6" pattern="[0-9]{6}" required placeholder="Digite o código recebido">
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <button type="submit" class="btn-primary">Verificar</button>
    </form>
    <p style="margin-top:18px;font-size:0.98em;color:#bbb;text-align:center;">O código expira em 5 minutos.</p>
</div>
</body>
</html>
