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
$sql = "SELECT * FROM veiculos_venda WHERE visivel = 0 ORDER BY id DESC";
$res = $con->query($sql);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anúncios Ocultos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
</head>
<body>
<?php include_once("header.php"); ?>
<div class="main-box" style="background:rgba(255,235,235,0.97);border-radius:18px;margin:24px 0 0 0;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:32px 0;min-height:70vh;">
  <h1 class="text-center mb-4" style="font-weight:700;letter-spacing:1px;color:#b71c1c;">Anúncios Ocultos</h1>
  <div class="container">
    <div class="row justify-content-center">
      <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
        <?php 
          $carName = $row['nome'];
          $carImg = $row['imagem_principal'];
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center mb-4">
          <div class="card" style="width:100%;max-width:260px;min-width:220px;box-shadow:0 2px 10px rgba(0,0,0,0.10);border-radius:10px;overflow:hidden;transition:transform 0.15s;">
            <a href="CarroOculto.php?id=<?= $row['id'] ?>" style="display:block;">
              <?php if (!empty($carImg) && file_exists(__DIR__ . '/../Imagens/' . $carImg)): ?>
                <img src="/CRUDTCC/php/Vendas/Imagens/<?= htmlspecialchars($carImg) ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
              <?php else: ?>
                <img src="/CRUDTCC/images/default.jpg" class="card-img-top" alt="Imagem não disponível" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
              <?php endif; ?>
            </a>
            <div class="card-body text-center p-2" style="background:#fff;">
              <h5 class="card-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;margin:0 auto;font-size:1.1rem;font-weight:600;">
                <?= htmlspecialchars($carName) ?>
              </h5>
              <div style="font-size:0.95rem;color:#b71c1c;">ID: <?= $row['id'] ?></div>
              <div style="font-size:0.95rem;">Motivo ocultação: <?= isset($row['denun']) ? ($row['denun'] . ' denúncias') : '-' ?></div>
              <div style="font-size:0.95rem;">Dono: <?= htmlspecialchars($row['logid']) ?></div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
      <?php if ($res->rowCount() == 0): ?>
        <div class="col-12 text-center mt-5">
          <h4 style="color:#b71c1c;">Nenhum anúncio oculto encontrado.</h4>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
