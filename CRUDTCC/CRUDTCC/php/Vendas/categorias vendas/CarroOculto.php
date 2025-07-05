<?php
include '../../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// IDs dos administradores
$adminUsers = ['user_6838d6be1afc45.64015583', 'user_6838dec504f9e3.45675952'];
if (!isset($_SESSION['logid']) || !in_array($_SESSION['logid'], $adminUsers)) {
    http_response_code(403);
    echo '<h2 style="color:#b71c1c;text-align:center;margin-top:60px;">Acesso restrito: apenas administradores podem visualizar esta página.</h2>';
    exit;
}
$con = conectar();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo '<h3 style="color:#b71c1c;text-align:center;margin-top:60px;">ID do anúncio não informado.</h3>';
    exit;
}
// Busca o anúncio
$car = $con->query("SELECT * FROM veiculos_venda WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
if (!$car) {
    echo '<h3 style="color:#b71c1c;text-align:center;margin-top:60px;">Anúncio não encontrado.</h3>';
    exit;
}
// Busca motivos das denúncias a partir das colunas da própria tabela
$motivos = [];
if ($car['denun'] > 0 && !empty($car['denunid']) && !empty($car['motdenun'])) {
    $usuarios = explode('|', $car['denunid']);
    $motivosArr = explode('|', $car['motdenun']);
    $count = min(count($usuarios), count($motivosArr));
    for ($i = 0; $i < $count; $i++) {
        $usuarioId = trim($usuarios[$i]);
        $usuarioNome = $usuarioId;
        if ($usuarioId) {
            $sqlNome = $con->query("SELECT logname FROM clilogin WHERE logid = " . $con->quote($usuarioId));
            $usuarioNomeBanco = $sqlNome ? $sqlNome->fetchColumn() : null;
            if ($usuarioNomeBanco) {
                $usuarioNome = $usuarioNomeBanco . " (" . $usuarioId . ")";
            }
        }
        $motivos[] = [
            'usuario' => $usuarioNome,
            'motivo' => trim($motivosArr[$i])
        ];
    }
}
// Busca status de banimento do dono (anunban) e nome do dono
$anunban = '-';
$nomeDono = '-';
if (!empty($car['logid'])) {
    $logidAutor = $con->quote($car['logid']);
    $sqlDono = $con->query("SELECT anunban, logname FROM clilogin WHERE logid = $logidAutor LIMIT 1");
    if ($sqlDono && $rowDono = $sqlDono->fetch(PDO::FETCH_ASSOC)) {
        $anunban = (int)$rowDono['anunban'];
        $nomeDono = $rowDono['logname'];
    }
}
// Ações administrativas
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['zerar_denuncias'])) {
        $con->exec("UPDATE veiculos_venda SET denun = 0, denunid = '', motdenun = '' WHERE id = $id");
        $msg = 'Denúncias zeradas!';
        $car['denun'] = 0;
        $car['denunid'] = '';
        $car['motdenun'] = '';
        $motivos = [];
    }
    if (isset($_POST['apagar_denuncia']) && isset($_POST['denuncia_index'])) {
        $index = (int)$_POST['denuncia_index'];
        $usuarios = explode('|', $car['denunid']);
        $motivosArr = explode('|', $car['motdenun']);
        if (isset($usuarios[$index]) && isset($motivosArr[$index])) {
            array_splice($usuarios, $index, 1);
            array_splice($motivosArr, $index, 1);
            $novoDenunid = implode('|', $usuarios);
            $novoMotdenun = implode('|', $motivosArr);
            $novoDenun = max(0, count($usuarios));
            $con->exec("UPDATE veiculos_venda SET denun = $novoDenun, denunid = " . $con->quote($novoDenunid) . ", motdenun = " . $con->quote($novoMotdenun) . " WHERE id = $id");
            $msg = 'Denúncia removida!';
            $car['denun'] = $novoDenun;
            $car['denunid'] = $novoDenunid;
            $car['motdenun'] = $novoMotdenun;
            // Atualiza lista de motivos após remoção
            $motivos = [];
            if ($novoDenun > 0 && !empty($novoDenunid) && !empty($novoMotdenun)) {
                $usuarios = explode('|', $novoDenunid);
                $motivosArr = explode('|', $novoMotdenun);
                $count = min(count($usuarios), count($motivosArr));
                for ($i = 0; $i < $count; $i++) {
                    $motivos[] = [
                        'usuario' => trim($usuarios[$i]),
                        'motivo' => trim($motivosArr[$i])
                    ];
                }
            }
        }
    }
    if (isset($_POST['tornar_visivel'])) {
        $con->exec("UPDATE veiculos_venda SET visivel = 1 WHERE id = $id");
        $msg = 'Anúncio tornado visível!';
        $car['visivel'] = 1;
    }
    if (isset($_POST['anunban'])) {
        $novoBan = (int)$_POST['anunban'];
        $logid = $con->quote($car['logid']);
        $con->exec("UPDATE clilogin SET anunban = $novoBan WHERE logid = $logid");
        $msg = 'Status de banimento do dono atualizado!';
    }
    if (isset($_POST['deletar'])) {
        $con->exec("DELETE FROM veiculos_venda WHERE id = $id");
        echo '<h3 style="color:#b71c1c;text-align:center;margin-top:60px;">Anúncio deletado com sucesso.</h3>';
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administração do Anúncio Oculto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
</head>
<body>
<?php include_once("header.php"); ?>
<div class="container mt-4 mb-5">
    <h2 class="mb-3 text-center" style="color:#b71c1c;">Administração do Anúncio Oculto</h2>
    <?php if ($msg): ?><div class="alert alert-success text-center"><?= $msg ?></div><?php endif; ?>
    <div class="card mb-4" style="max-width:600px;margin:0 auto;">
        <div class="card-header bg-danger text-white">Dados do Anúncio</div>
        <div class="card-body">
            <strong>ID:</strong> <?= $car['id'] ?><br>
            <strong>Nome:</strong> <?= htmlspecialchars($car['nome']) ?><br>
            <strong>Dono (logid):</strong> <?= htmlspecialchars($car['logid']) ?><?php if($nomeDono && $nomeDono != '-'): ?> (<?= htmlspecialchars($nomeDono) ?>)<?php endif; ?><br>
            <strong>Denúncias:</strong> <?= $car['denun'] ?><br>
            <strong>Visível:</strong> <?= $car['visivel'] ? 'Sim' : 'Não' ?><br>
            <strong>Banimento do dono (anunban):</strong> <?= $anunban === 1 ? '1 - Banido' : ($anunban === 0 ? '0 - Não banido' : '-') ?><br>
        </div>
    </div>
    <div class="card mb-4" style="max-width:600px;margin:0 auto;">
        <div class="card-header bg-warning">Motivos das Denúncias</div>
        <div class="card-body">
            <?php if ($motivos): ?>
                <ul class="list-group">
                    <?php foreach ($motivos as $i => $m): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <b><?= htmlspecialchars($m['usuario']) ?></b>:<br>
                                <?= nl2br(htmlspecialchars($m['motivo'])) ?>
                            </div>
                            <form method="post" style="margin-left:10px;">
                                <input type="hidden" name="denuncia_index" value="<?= $i ?>">
                                <button name="apagar_denuncia" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover esta denúncia?')">Apagar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <span class="text-muted">Nenhuma denúncia registrada.</span>
            <?php endif; ?>
        </div>
    </div>
    <form method="post" class="mb-3 text-center">
        <button name="zerar_denuncias" class="btn btn-warning me-2" onclick="return confirm('Zerar todas as denúncias?')">Zerar Denúncias</button>
        <button name="tornar_visivel" class="btn btn-success me-2" onclick="return confirm('Tornar o anúncio visível novamente?')">Tornar Visível</button>
        <button name="deletar" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja deletar este anúncio? Esta ação não pode ser desfeita.')">Deletar Anúncio</button>
    </form>
    <form method="post" class="mb-3 text-center" style="max-width:400px;margin:0 auto;">
        <label for="anunban" class="form-label">Alterar status de banimento do dono (anunban):</label>
        <select name="anunban" id="anunban" class="form-select mb-2">
            <option value="0"<?= $anunban === 0 ? ' selected' : '' ?>>0 - Não banido</option>
            <option value="1"<?= $anunban === 1 ? ' selected' : '' ?>>1 - Banido</option>
        </select>
        <button type="submit" class="btn btn-secondary">Atualizar Banimento</button>
    </form>
    <div class="text-center mt-4">
        <a href="Ocultos.php" class="btn btn-outline-dark">Voltar para lista de ocultos</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
