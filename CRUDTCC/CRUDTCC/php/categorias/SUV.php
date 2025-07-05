<?php
include '../config.php';
include '../mysqlexecuta.php';

$con = conectar();
$imageDir = '/images/Carimg/';

// Consulta correta para SUVs (catid, catidop1 ou catidop2 = 3)
$sql = "SELECT carimg, carnome FROM car WHERE catid = 3 OR catidop1 = 3 OR catidop2 = 3";
$res = mysqlexecuta($con, $sql);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUVs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/header.css" rel="stylesheet">
    <link href="/css/suv.css" rel="stylesheet">
  </head>
  <body>
    <?php include_once(__DIR__ . '/../../header.php'); ?>
    <div class="main-box" style="background:rgba(255,255,220,0.92);border-radius:18px;margin:24px 0 0 0;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:32px 0;min-height:70vh;">
      <h1 class="text-center mb-4" style="font-weight:700;letter-spacing:1px;">SUVs</h1>
      <div class="container">
        <div class="row justify-content-center">
          <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
            <?php 
              $carName = $row['carnome'];
              $carImg = $row['carimg'];
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center mb-4">
              <div class="card" style="width:100%;max-width:260px;min-width:220px;box-shadow:0 2px 10px rgba(0,0,0,0.10);border-radius:10px;overflow:hidden;transition:transform 0.15s;">
                <a href="Carro.php?nome=<?= urlencode($carName) ?>" style="display:block;">
                  <?php if (!empty($carImg) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageDir . $carImg)): ?>
                    <img src="<?= $imageDir . $carImg ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
                  <?php else: ?>
                    <img src="/images/default.jpg" class="card-img-top" alt="Imagem não disponível" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
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
      // Dark mode toggle
      const btn = document.getElementById('toggle-dark');
      if(btn) {
        btn.onclick = function() {
          document.body.classList.toggle('dark-mode');
          localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode') ? '1' : '');
        };
      }
      // Persist dark mode
      if (localStorage.getItem('dark-mode')) {
        document.body.classList.add('dark-mode');
      }
    </script>
  </body>
</html>
