<?php
include '../../config.php'; // garantir que conectar() retorna sempre PDO/SQLite
// NÃO incluir mysqlexecuta.php ou qualquer arquivo que defina MySQLi

$con = conectar();
$imageDir = '../../Imagens/';

// Buscar veículos da tabela veiculos_venda com categoria principal ou opcionais = 1 E visivel = 1 E pausa = 1
$sql = "SELECT imagem_principal, nome FROM veiculos_venda WHERE (categoria_id = 1 OR categoria_idop1 = 1 OR categoria_idop2 = 1) AND visivel = 1 AND pausa = 1";
$res = $con->query($sql);

// Tema via GET
$tema = isset($_GET['tema']) ? $_GET['tema'] : '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antiguidades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/antiguidade.css" rel="stylesheet">
  </head>
  <body<?php if ($tema === 'dark') { echo ' class="dark-mode"'; } elseif ($tema === 'light') { echo ' class="light-mode"'; } ?>>
    <?php include_once("header.php"); ?>
    <div class="main-box" style="background:rgba(255,255,220,0.92);border-radius:18px;margin:24px 0 0 0;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:32px 0;min-height:70vh;">
      <h1 class="text-center mb-4" style="font-weight:700;letter-spacing:1px;">Antiguidades</h1>
      <div class="container">
        <div class="row justify-content-center">
          <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
            <?php 
              $carName = $row['nome'];
              $carImg = $row['imagem_principal'];
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center mb-4">
              <div class="card" style="width:100%;max-width:260px;min-width:220px;box-shadow:0 2px 10px rgba(0,0,0,0.10);border-radius:10px;overflow:hidden;transition:transform 0.15s;">
                <a href="Carro.php?nome=<?= urlencode($carName) ?>&tema=<?= urlencode($tema) ?>" style="display:block;">
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
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // ...existing code...
    </script>
  </body>
</html>
