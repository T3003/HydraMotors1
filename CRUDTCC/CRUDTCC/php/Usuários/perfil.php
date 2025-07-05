<?php
session_start();
include '../config.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/CRUDTCC/php/theme.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

$con = conectar();
$logid = $_SESSION['logid'];

// Busca dados do usuário
$sqlUser = "SELECT * FROM clilogin WHERE logid = :logid LIMIT 1";
$stmtUser = $con->prepare($sqlUser);
$stmtUser->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Mapeamento dos campos do banco para o perfil
$user_nome = $user['logname'] ?? '';
$user_email = $user['logemail'] ?? '';
$user_foto = $user['logpfp'] ?? 'default.jpg';

// Busca carros cadastrados pelo usuário (não à venda)
$sqlCarrosCadastro = "SELECT * FROM car WHERE logid = :logid";
$stmtCadastro = $con->prepare($sqlCarrosCadastro);
$stmtCadastro->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmtCadastro->execute();
$carrosCadastro = $stmtCadastro->fetchAll(PDO::FETCH_ASSOC);

// Busca carros à venda pelo usuário
$sqlCarrosVenda = "SELECT * FROM veiculos_venda WHERE logid = :logid";
$stmtVenda = $con->prepare($sqlCarrosVenda);
$stmtVenda->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmtVenda->execute();
$carrosVenda = $stmtVenda->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/perfil.css" rel="stylesheet">
    <link href="/CRUDTCC/css/perfil_custom.css" rel="stylesheet">
    <style>
        body.light-mode {
            background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed !important;
            color: #232323;
        }
        body.dark-mode {
            background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
            color: #f5f4ec;
        }
        .perfil-card {
            background: #232323e6;
            color: #fff;
            border-radius: 22px;
            box-shadow: 0 2px 16px #0008;
        }
        body.light-mode .perfil-card {
            background: #fffbe6;
            color: #232323;
            box-shadow: 0 2px 16px #bfa13a44;
        }
        .perfil-card h2, .perfil-card h4 {
            color: #ffe066;
        }
        body.light-mode .perfil-card h2, body.light-mode .perfil-card h4 {
            color: #bfa13a;
        }
        .perfil-foto {
            border: 4px solid #ffe066;
            background: #fff;
        }
        body.light-mode .perfil-foto {
            border: 4px solid #bfa13a;
            background: #fffbe6;
        }
        .perfil-info {
            background: #2c2f34;
            color: #fff;
            border-radius: 12px;
            padding: 18px 16px 12px 16px;
        }
        body.light-mode .perfil-info {
            background: #f7f3d7;
            color: #232323;
        }
        .perfil-email span {
            color: #fff;
        }
        body.light-mode .perfil-email span {
            color: #232323;
        }
        .btn-primary, .btn-danger {
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .carro-card {
            background: #232323cc;
            color: #fff;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 12px;
        }
        body.light-mode .carro-card {
            background: #fffbe6cc;
            color: #232323;
        }
        hr {
            background: #ffe066;
            opacity: 0.3;
        }
        body.light-mode hr {
            background: #bfa13a;
        }
    </style>
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="container">
    <div class="perfil-card shadow p-4">
        <h2 class="mb-4 text-center">Meu Perfil</h2>
        <div class="row align-items-center">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <img src="../Usuários/Fotos de perfil/<?= htmlspecialchars($user_foto) ?>" class="perfil-foto mb-3" alt="Foto de Perfil">
                <div class="perfil-info">
                    <div class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($user_nome) ?></div>
                    <div class="mb-2 perfil-email">
                        <strong>Email:</strong>
                        <span><?= htmlspecialchars($user_email) ?></span>
                    </div>
                    <?php
                    // Exibe o label de privilégio se for Adm ou Revisor
                    if (isset($user['Adm']) && $user['Adm'] == 1) {
                        echo '<span class="badge bg-warning text-dark mb-2" style="font-size:1rem;">Administrador</span>';
                    } elseif (isset($user['Rev']) && $user['Rev'] == 1) {
                        echo '<span class="badge bg-info text-dark mb-2" style="font-size:1rem;">Revisor</span>';
                    }
                    ?>
                    <a href="valida_senha.php" class="btn btn-primary mt-2 w-100">Editar Perfil</a>
                    <?php if (isset($user['Adm']) && $user['Adm'] == 0): ?>
                    <form action="ExcluirConta.php" method="post" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível.');" class="mt-2">
                        <button type="submit" class="btn btn-danger w-100">Excluir Conta</button>
                    </form>
                    <?php elseif (isset($user['Adm']) && $user['Adm'] == 1): ?>
                    <div class="alert alert-warning mt-2" style="font-size:0.95rem;">Administradores não podem excluir a própria conta.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8">
                <div class="mb-4">
                    <h4 class="mb-3">Carros Cadastrados</h4>
                    <?php if (count($carrosCadastro) > 0): ?>
                        <div class="row">
                        <?php foreach ($carrosCadastro as $car): ?>
                            <div class="col-sm-12 col-lg-6">
                                <div class="carro-card">
                                    <strong><?= htmlspecialchars($car['carnome'] ?? '-') ?></strong><br>
                                    Marca: <?= htmlspecialchars($car['carmarca'] ?? '-') ?><br>
                                    <?php
                                        // Exibe corretamente o ano de fabricação, considerando possíveis campos
                                        $ano = '-';
                                        if (!empty($car['carano'])) {
                                            $ano = htmlspecialchars($car['carano']);
                                        } elseif (!empty($car['CarFabIn']) && !empty($car['CarFabFim'])) {
                                            $ano = htmlspecialchars($car['CarFabIn']) . ' - ' . htmlspecialchars($car['CarFabFim']);
                                        } elseif (!empty($car['CarFabIn'])) {
                                            $ano = htmlspecialchars($car['CarFabIn']);
                                        } elseif (!empty($car['CarFabFim'])) {
                                            $ano = htmlspecialchars($car['CarFabFim']);
                                        }
                                    ?>
                                    Ano: <?= $ano ?><br>
                                    <!-- Adicione outros campos se necessário -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Você ainda não cadastrou nenhum carro.</p>
                    <?php endif; ?>
                </div>
                <hr style="background: #444;">
                <div>
                    <h4 class="mb-3">Carros à Venda</h4>
                    <?php if (count($carrosVenda) > 0): ?>
                        <div class="row">
                        <?php foreach ($carrosVenda as $car): ?>
                            <div class="col-sm-12 col-lg-6">
                                <div class="carro-card">
                                    <strong><?= htmlspecialchars($car['nome']) ?></strong><br>
                                    Marca: <?= htmlspecialchars($car['marca']) ?><br>
                                    Ano: <?= htmlspecialchars($car['ano']) ?><br>
                                    Preço: <?= isset($car['preco']) ? 'R$ ' . number_format($car['preco'], 2, ',', '.') : '-' ?><br>
                                    <a href="../Vendas/categorias vendas/Carro.php?nome=<?= urlencode($car['nome']) ?>" class="btn btn-sm btn-info mt-2">Ver anúncio</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Você ainda não cadastrou nenhum carro para venda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function setThemeByTime() {
    const hour = new Date().getHours();
    if (hour >= 7 && hour < 19) {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
    } else {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    }
}
setThemeByTime();
</script>
</body>
</html>
