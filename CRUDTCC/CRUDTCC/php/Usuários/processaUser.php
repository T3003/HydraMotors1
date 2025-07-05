<?php include_once("../theme.php"); ?>
<?php
include_once(__DIR__ . '/../config.php');

// Recebe os dados do formulário
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirmar_senha = isset($_POST['confirmar_senha']) ? trim($_POST['confirmar_senha']) : '';
$bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
$pfp = isset($_POST['pfp']) ? trim($_POST['pfp']) : '';

// Validação simples
if ($nome === '' || $senha === '' || $email === '' || $confirmar_senha === '') {
    echo "Nome, email e senha são obrigatórios.";
    exit;
}
// Conexão ANTES da verificação de email duplicado
$con = conectar();
// Verifica se já existe email cadastrado
$emailCheckSql = "SELECT 1 FROM clilogin WHERE logemail = :email LIMIT 1";
$stmtEmail = $con->prepare($emailCheckSql);
$stmtEmail->bindValue(':email', $email, PDO::PARAM_STR);
$stmtEmail->execute();
if ($stmtEmail->fetch()) {
    header('Location: crudpf.php?erro=email');
    exit;
}
if ($senha !== $confirmar_senha) {
    echo "As senhas não coincidem.";
    exit;
}
// Gera um ID aleatório para logid e garante unicidade
$logid = '';
do {
    $logid = uniqid('user_', true);
    $checkSql = "SELECT 1 FROM clilogin WHERE logid = :logid LIMIT 1";
    $stmtCheck = $con->prepare($checkSql);
    $stmtCheck->bindValue(':logid', $logid, PDO::PARAM_STR);
    $stmtCheck->execute();
} while ($stmtCheck->fetch());

// Lida com upload de imagem de perfil
$pfpEsc = '';
if (isset($_FILES['imagens']) && $_FILES['imagens']['error'][0] === UPLOAD_ERR_OK) {
    $imgTmp = $_FILES['imagens']['tmp_name'][0];
    $imgType = pathinfo($_FILES['imagens']['name'][0], PATHINFO_EXTENSION);
    $imgName = $logid . '.' . $imgType;
    $destDir = __DIR__ . '/Fotos de perfil/';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0777, true);
    }
    $destPath = $destDir . $imgName;
    if (move_uploaded_file($imgTmp, $destPath)) {
        $pfpEsc = $imgName;
    }
}

// Insere na tabela clilogin
$sql = "INSERT INTO clilogin (logid, Adm, Rev, logname, logemail, logsenha, logbio, logpfp) VALUES (:logid, :adm, :rev, :nome, :email, :senha, :bio, :pfp)";
$stmt = $con->prepare($sql);
$stmt->bindValue(':logid', $logid, PDO::PARAM_STR);
$stmt->bindValue(':adm', 0, PDO::PARAM_INT); // valor padrão
$stmt->bindValue(':rev', 0, PDO::PARAM_INT); // valor padrão
$stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':senha', $senha, PDO::PARAM_STR);
$stmt->bindValue(':bio', $bio, PDO::PARAM_STR);
$stmt->bindValue(':pfp', $pfpEsc, PDO::PARAM_STR);
$res = $stmt->execute();

if ($res) {
    // Cadastro realizado com sucesso
    session_start();
    $_SESSION['logid'] = $logid;
    header("Location: tutorial_logo.php");
    exit;
} else {
    echo "Erro ao cadastrar usuário.";
}
