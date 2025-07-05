<?php
session_start();
include '../config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logid'])) {
    header('Location: login.php');
    exit;
}

// Verifica se a senha foi validada antes de permitir edição
if (!isset($_SESSION['valida_perfil']) || $_SESSION['valida_perfil'] !== true) {
    header('Location: valida_senha.php');
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
$user_senha = $user['logsenha'] ?? '';
$user_foto = $user['logpfp'] ?? 'default.jpg';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_perfil'])) {
    $novo_nome = $_POST['nome'];
    $nova_senha = isset($_POST['senha']) && $_POST['senha'] !== '' ? $_POST['senha'] : $user_senha;
    $foto = $user_foto;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_nome = $logid . '.' . $ext;
        $destino = __DIR__ . '/Fotos de perfil/' . $foto_nome;
        move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
        $foto = $foto_nome;
    }
    $sqlUpdate = "UPDATE clilogin SET logname=:nome, logsenha=:senha, logpfp=:foto WHERE logid = :logid";
    $stmtUpdate = $con->prepare($sqlUpdate);
    $stmtUpdate->bindValue(':nome', $novo_nome, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':senha', $nova_senha, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':foto', $foto, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':logid', $logid, PDO::PARAM_STR);
    if ($stmtUpdate->execute()) {
        $msg = 'Perfil atualizado com sucesso!';
        $user_nome = $novo_nome;
        $user_senha = $nova_senha;
        $user_foto = $foto;
    } else {
        $msg = 'Erro ao atualizar perfil.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .perfil-foto { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #b8e0e6; }
        /* --- DARK MODE --- */
        body.dark-mode {
            background: url('/CRUDTCC/images/BackgroundDM.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        body.dark-mode .card.shadow.p-4 {
            background: #2C2B28 !important;
            color: #fff !important;
            max-width: 600px !important;
            min-width: 320px !important;
            width: 100% !important;
            margin: 0 auto !important;
            border-radius: 18px !important;
            box-shadow: 0 0 16px rgba(0,0,0,0.08) !important;
            padding: 2.5rem !important;
        }
        @media (max-width: 700px) {
            body.dark-mode .card.shadow.p-4 {
                padding: 1rem !important;
                max-width: 98vw !important;
                min-width: unset !important;
            }
        }
        body.dark-mode .perfil-foto {
            border-color: #7b5be6;
        }
        body.dark-mode .form-control {
            background: #222 !important;
            color: #fff !important;
            border-color: #444 !important;
        }
        body.dark-mode .form-control:focus {
            background: #333 !important;
            color: #fff !important;
        }
        body.dark-mode .btn-primary {
            background: #fff !important;
            border-color: #fff !important;
            color: #222 !important;
        }
        body.dark-mode .btn-primary:hover {
            background: #eaeaea !important;
            border-color: #eaeaea !important;
            color: #222 !important;
        }
        body.dark-mode .btn-secondary {
            background: #444 !important;
            border-color: #444 !important;
            color: #fff !important;
        }
        body.dark-mode .btn-secondary:hover {
            background: #222 !important;
            border-color: #222 !important;
            color: #fff !important;
        }
        body.dark-mode h2,
        body.dark-mode label,
        body.dark-mode strong {
            color: #fff !important;
        }
        body.dark-mode .alert-info {
            background: #00343a !important;
            color: #fff !important;
            border-color: #1976d2 !important;
        }
    </style>
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="container mt-4">
    <div class="card shadow p-4" style="max-width: 600px; margin: 0 auto; background: #f8f9fa; border-radius: 18px;">
        <h2 class="mb-4">Editar Perfil</h2>
        <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <img src="../Usuários/Fotos de perfil/<?= htmlspecialchars($user_foto) ?>" class="perfil-foto mb-3" alt="Foto de Perfil">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-2">
                        <label for="foto" class="form-label">Foto de Perfil</label>
                        <input type="file" name="foto" id="foto" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($user_nome) ?>" placeholder="Nome">
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <div class="d-flex align-items-center">
                            <input type="text" id="email" class="form-control me-2" value="<?= htmlspecialchars($user_email) ?>" placeholder="Email" disabled>
                            <a href="alterar_email.php" class="btn btn-outline-primary">Alterar Email</a>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control" value="<?= htmlspecialchars($user_senha) ?>" placeholder="Senha">
                    </div>
                    <button type="submit" name="atualizar_perfil" class="btn btn-primary">Salvar Alterações</button>
                    <a href="perfil.php" class="btn btn-secondary ms-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
