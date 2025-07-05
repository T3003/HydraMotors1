<?php
session_start();
include '../config.php';

// Apenas administradores podem acessar (pelo campo Adm)
if (!isset($_SESSION['logid'])) {
    header('Location: /CRUDTCC/index.php');
    exit;
}
$con = conectar();
$logid = $_SESSION['logid'];
$stmtAdm = $con->prepare("SELECT Adm FROM clilogin WHERE logid = :logid LIMIT 1");
$stmtAdm->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmtAdm->execute();
$rowAdm = $stmtAdm->fetch(PDO::FETCH_ASSOC);
if (!$rowAdm || $rowAdm['Adm'] != 1) {
    header('Location: /CRUDTCC/index.php');
    exit;
}

// --- PHP: processar alteração de anunban ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza anunban
    if (isset($_POST['anunban'])) {
        foreach ($_POST['anunban'] as $uid => $anunbanValue) {
            $anunbanVal = is_numeric($anunbanValue) ? (int)$anunbanValue : 0;
            $stmt = $con->prepare("UPDATE clilogin SET anunban = :anunban WHERE logid = :logid");
            $stmt->bindValue(':anunban', $anunbanVal, PDO::PARAM_INT);
            $stmt->bindValue(':logid', $uid, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    // Atualiza Adm
    if (isset($_POST['adm_status'])) {
        foreach ($_POST['adm_status'] as $uid => $admValue) {
            $admVal = $admValue == '1' ? 1 : 0;
            // Verifica status anterior
            $stmtOld = $con->prepare("SELECT Adm, logemail, logname FROM clilogin WHERE logid = :logid LIMIT 1");
            $stmtOld->bindValue(':logid', $uid, PDO::PARAM_STR);
            $stmtOld->execute();
            $rowOld = $stmtOld->fetch(PDO::FETCH_ASSOC);
            $eraAdm = $rowOld && $rowOld['Adm'] == 1;
            $email = $rowOld ? $rowOld['logemail'] : '';
            $nome = $rowOld ? $rowOld['logname'] : '';
            $stmt = $con->prepare("UPDATE clilogin SET Adm = :adm WHERE logid = :logid");
            $stmt->bindValue(':adm', $admVal, PDO::PARAM_INT);
            $stmt->bindValue(':logid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            // Se virou Adm agora, envia e-mail
            if (!$eraAdm && $admVal == 1 && $email) {
                require_once __DIR__ . '/email_promocao.php';
                enviarEmailPromocaoCargo($email, $nome, 'Adm');
            }
        }
    }
    // Atualiza Rev
    if (isset($_POST['rev_status'])) {
        foreach ($_POST['rev_status'] as $uid => $revValue) {
            $revVal = $revValue == '1' ? 1 : 0;
            // Verifica status anterior
            $stmtOld = $con->prepare("SELECT Rev, logemail, logname FROM clilogin WHERE logid = :logid LIMIT 1");
            $stmtOld->bindValue(':logid', $uid, PDO::PARAM_STR);
            $stmtOld->execute();
            $rowOld = $stmtOld->fetch(PDO::FETCH_ASSOC);
            $eraRev = $rowOld && $rowOld['Rev'] == 1;
            $email = $rowOld ? $rowOld['logemail'] : '';
            $nome = $rowOld ? $rowOld['logname'] : '';
            $stmt = $con->prepare("UPDATE clilogin SET Rev = :rev WHERE logid = :logid");
            $stmt->bindValue(':rev', $revVal, PDO::PARAM_INT);
            $stmt->bindValue(':logid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            // Se virou Revisor agora, envia e-mail
            if (!$eraRev && $revVal == 1 && $email) {
                require_once __DIR__ . '/email_promocao.php';
                enviarEmailPromocaoCargo($email, $nome, 'Rev');
            }
        }
    }
    // Excluir conta
    if (isset($_POST['delete_user'])) {
        $deleteId = $_POST['delete_user'];
        $stmtCheck = $con->prepare("SELECT Adm, logpfp FROM clilogin WHERE logid = :logid LIMIT 1");
        $stmtCheck->bindValue(':logid', $deleteId, PDO::PARAM_STR);
        $stmtCheck->execute();
        $rowCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($rowCheck && $rowCheck['Adm'] != 1) {
            if (!empty($rowCheck['logpfp']) && $rowCheck['logpfp'] !== 'default.jpg') {
                $fotoPath = __DIR__ . '/Fotos de perfil/' . $rowCheck['logpfp'];
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }
            $stmtDel = $con->prepare("DELETE FROM clilogin WHERE logid = :logid");
            $stmtDel->bindValue(':logid', $deleteId, PDO::PARAM_STR);
            $stmtDel->execute();
        }
    }
    header("Location: lista_contas.php");
    exit;
}

// IDs que sempre serão administradores
$admsFixos = ['user_6838d6be1afc45.64015583', 'user_6838dce405a9e4.45675952'];
foreach ($admsFixos as $admId) {
    $stmtAdmFix = $con->prepare("UPDATE clilogin SET Adm = 1 WHERE logid = :logid");
    $stmtAdmFix->bindValue(':logid', $admId, PDO::PARAM_STR);
    $stmtAdmFix->execute();
}

// Campo de pesquisa e filtro de anúncios banidos
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$filtrarAnunban = isset($_GET['anunban']) && $_GET['anunban'] == '1';
$where = [];
if ($pesquisa !== '') {
    $pesqEsc = '%' . str_replace(['%', '_'], ['\%', '\_'], $pesquisa) . '%';
    $where[] = "(logid LIKE :pesquisa OR logemail LIKE :pesquisa OR logname LIKE :pesquisa)";
}
if ($filtrarAnunban) {
    $where[] = "anunban >= 1";
}
$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}
// Busca todas as contas (inclui campo Adm, Rev e anunban), ordenando ADM > REV > outros, depois por nome
$order = $filtrarAnunban ? 'ORDER BY anunban DESC' : 'ORDER BY Adm DESC, Rev DESC, logname ASC';
$sql = "SELECT logid, logname, logemail, logpfp, Adm, Rev, anunban, logbio FROM clilogin $whereSql $order";
$stmt = $con->prepare($sql);
if ($pesquisa !== '') {
    $stmt->bindValue(':pesquisa', $pesqEsc, PDO::PARAM_STR);
}
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Contas do Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/lista_contas.css" rel="stylesheet">
    <style>
      /* Corrige tamanho da fonte e quebra de texto na tabela */
      table th, table td {
        font-size: 1rem !important;
        white-space: normal !important;
        word-break: break-word !important;
        vertical-align: middle !important;
      }
      table th {
        font-weight: 700;
        text-align: center;
        background: #f8f6f0;
        color: #2a4a7b;
        letter-spacing: 0.5px;
      }
      .table {
        table-layout: auto !important;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px #0002;
      }
      .main-box {
        background: #232323e6;
        border-radius: 18px;
        box-shadow: 0 4px 24px #0003;
      }
      .perfil-foto.shadow {
        box-shadow: 0 2px 8px #0002;
      }
      tr:hover {
        box-shadow: 0 2px 8px #bfa13a33;
        background: #fffbe7 !important;
      }
      thead th {
        border-bottom: 2px solid #bfa13a !important;
      }
      tbody td {
        border-bottom: 1px solid #eee !important;
      }
      .btn-primary, .btn-warning, .btn-danger {
        border-radius: 20px;
        font-weight: 600;
        letter-spacing: 0.5px;
      }
      .btn-primary {
        background: #2a4a7b;
        border: none;
      }
      .btn-warning {
        background: #bfa13a;
        color: #232323;
        border: none;
      }
      .btn-warning.active, .btn-warning:active {
        background: #a88d2b !important;
      }
      .btn-outline-secondary {
        border-radius: 20px;
      }
    </style>
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="main-box mt-4" style="max-width:1100px; width:98%; margin:40px auto; padding:32px 18px;">
    <h2 class="mb-4 text-center">Todas as Contas do Site</h2>
    <form method="get" class="mb-3 d-flex justify-content-center align-items-center" style="gap:10px;flex-wrap:wrap;">
        <input type="text" name="pesquisa" class="form-control" style="max-width:320px;" placeholder="Pesquisar por nome, email ou ID" value="<?= htmlspecialchars($pesquisa) ?>">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <?php if ($pesquisa !== ''): ?>
            <a href="lista_contas.php" class="btn btn-secondary ms-2">Limpar</a>
        <?php endif; ?>
        <a href="lista_contas.php?anunban=1<?= $pesquisa !== '' ? '&pesquisa=' . urlencode($pesquisa) : '' ?>" class="btn btn-warning ms-2<?= $filtrarAnunban ? ' active' : '' ?>">Mostrar apenas contas com anúncios banidos</a>
        <?php if ($filtrarAnunban): ?>
            <a href="lista_contas.php<?= $pesquisa !== '' ? '?pesquisa=' . urlencode($pesquisa) : '' ?>" class="btn btn-outline-secondary ms-2">Mostrar todas</a>
        <?php endif; ?>
    </form>
    <form method="post">
    <div class="table-responsive">
        <table class="table table-bordered align-middle" style="min-width:900px;">
            <thead class="table-light">
                <tr>
                    <th style="width:80px; text-align:center;">Foto</th>
                    <th style="width:160px;">Nome</th>
                    <th style="width:220px;">Email</th>
                    <th style="width:260px;">ID</th>
                    <th style="width:110px;">Administrador</th>
                    <th style="width:80px;">É Adm?</th>
                    <th style="width:110px;">Revisor</th>
                    <th style="width:90px;">É Revisor?</th>
                    <th style="width:90px;">Excluir</th>
                    <th style="width:90px;">Banidos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($res as $row): ?>
                <?php
                    $isAdm = ($row['Adm'] == 1);
                    $isRev = ($row['Rev'] == 1) || $isAdm;
                ?>
                <tr style="background:#fff;box-shadow:0 1px 4px #0001;transition:box-shadow .2s;">
                    <td style="text-align:center;">
                        <img src="/CRUDTCC/php/Usuários/Fotos de perfil/<?= htmlspecialchars($row['logpfp'] ?: 'default.jpg') ?>" class="perfil-foto shadow" alt="Foto" style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:2px solid #bfa13a;background:#eee;">
                    </td>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['logname']) ?></td>
                    <td style="color:#2a4a7b;"><?= htmlspecialchars($row['logemail']) ?></td>
                    <td style="font-size:0.92em;word-break:break-all;"><?= htmlspecialchars($row['logid']) ?></td>
                    <td style="text-align:center;">
                        <?= $isAdm ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?>
                    </td>
                    <td style="text-align:center;">
                        <input type="hidden" name="adm_status[<?= htmlspecialchars($row['logid']) ?>]" value="0">
                        <input type="checkbox" name="adm_status[<?= htmlspecialchars($row['logid']) ?>]" value="1" <?= $isAdm ? 'checked' : '' ?> >
                    </td>
                    <td style="text-align:center;">
                        <?= $isRev ? '<span class="badge bg-info text-dark">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?>
                    </td>
                    <td style="text-align:center;">
                        <input type="hidden" name="rev_status[<?= htmlspecialchars($row['logid']) ?>]" value="0">
                        <input type="checkbox" name="rev_status[<?= htmlspecialchars($row['logid']) ?>]" value="1" <?= $isRev ? 'checked' : '' ?> <?= $isAdm ? 'disabled' : '' ?> >
                    </td>
                    <td style="text-align:center;">
                        <?php if (!$isAdm): ?>
                        <button type="submit" name="delete_user" value="<?= htmlspecialchars($row['logid']) ?>" class="btn btn-danger btn-sm" style="border-radius:20px;" onclick="return confirm('Tem certeza que deseja excluir esta conta?');">Excluir</button>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center; font-weight:bold; color:#bfa13a;">
                        <input type="number" name="anunban[<?= htmlspecialchars($row['logid']) ?>]" value="<?= isset($row['anunban']) ? (int)$row['anunban'] : 0 ?>" min="0" max="999" style="width:60px;text-align:center;font-weight:bold;color:#bfa13a;background:#fffbe7;border:1px solid #bfa13a;border-radius:8px;">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Salvar Permissões</button>
    </form>
</div>
</body>
</html>
