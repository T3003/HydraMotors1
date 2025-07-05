<?php
include '../config.php';
include '../mysqlexecuta.php';

$con = conectar();
mysqli_select_db($con, 'hydramotors');
$imageDir = '../../images/Carimg/';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT carimg, carnome FROM car";
if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($con, $search);
    $sql .= " WHERE carnome LIKE '%$searchEscaped%'";
}
$res = mysqlexecuta($con, $sql);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesquisa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/pesquisa.css" rel="stylesheet">
  </head>
  <body>
    <?php include_once("../../header.php"); ?>
    <div class="main-box">
      <h1 class="text-center">Resultado da Pesquisa</h1>
      <div class="container">
        <div class="row">
          <?php if(mysqli_num_rows($res) == 0): ?>
            <p class="text-center">Nenhum carro encontrado.</p>
          <?php endif; ?>
          <?php while ($row = mysqli_fetch_assoc($res)): ?>
            <?php 
              $carName = $row['carnome'];
              $carImg = $row['carimg'];
            ?>
            <div class="col-md-3 mb-4">
              <div class="card h-100">
                <a href="Carro.php?nome=<?= urlencode($carName) ?>" style="text-decoration:none; color:inherit;">
                  <?php if (!empty($carImg) && file_exists($imageDir . $carImg)): ?>
                    <img src="<?= $imageDir . $carImg ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>">
                  <?php else: ?>
                    <img src="/CRUDTCC/images/default.jpg" class="card-img-top" alt="Imagem não disponível">
                  <?php endif; ?>
                  <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($carName) ?></h5>
                  </div>
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Dark mode igual ao index/header
      function setDarkModeState(isDark) {
        if (isDark) {
          document.body.classList.add('dark-mode');
        } else {
          document.body.classList.remove('dark-mode');
        }
        if (document.getElementById('dark-mode-icon') && document.getElementById('dark-mode-label')) {
          document.getElementById('dark-mode-icon').textContent = isDark ? '☀️' : '🌙';
          document.getElementById('dark-mode-label').textContent = isDark ? 'Modo Claro' : 'Modo Escuro';
        }
      }
      if (localStorage.getItem('dark-mode')) {
        setDarkModeState(true);
      }
      const btn = document.getElementById('toggle-dark');
      if (btn) {
        btn.onclick = function() {
          setDarkModeState(!document.body.classList.contains('dark-mode'));
          if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('dark-mode', '1');
          } else {
            localStorage.removeItem('dark-mode');
          }
        };
      }
    </script>
  </body>
</html>
