<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';
// Define o tema padrão como claro
$theme = 'light';
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();
// Se o usuário estiver logado, tenta buscar o tema salvo no banco
if (isset($_SESSION['logid'])) {
    $con = isset($con) ? $con : (isset($conn) ? $conn : null);
    if ($con) {
        mysqli_select_db($con, 'hydramotors');
        $logid = mysqli_real_escape_string($con, $_SESSION['logid']);
        $resTheme = mysqli_query($con, "SELECT Theme FROM clilogin WHERE logid = '$logid' LIMIT 1");
        if ($resTheme && $rowTheme = mysqli_fetch_assoc($resTheme)) {
            $theme = ($rowTheme['Theme'] == 1) ? 'dark' : 'light';
        }
    }
}
?>
<script>
// Script para aplicar o tema salvo (claro/escuro) ao carregar a página
(function() {
  var theme = '<?= $theme ?>';
  // Função para aplicar as classes de tema
  function applyTheme(t) {
    document.body.classList.remove('light-mode', 'dark-mode');
    document.documentElement.classList.remove('light-mode', 'dark-mode');
    document.body.classList.add(t + '-mode');
    document.documentElement.classList.add(t + '-mode');
  }
  applyTheme(theme);

  // Função para trocar a logo conforme o tema
  function updateLogoForTheme() {
    var logoImg = document.getElementById('logo-img');
    if (!logoImg) return;
    if (document.body.classList.contains('dark-mode')) {
      logoImg.src = '/images/LOGODM.png';
    } else {
      logoImg.src = '/images/LOGO.png';
    }
  }
  // Atualiza a logo ao carregar e ao mudar o tema dinamicamente
  document.addEventListener('DOMContentLoaded', function() {
    updateLogoForTheme();
    var observer = new MutationObserver(updateLogoForTheme);
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
  });
})();
</script>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HYDRAMOTORS</title>
    <!-- Importa Bootstrap e estilos do projeto -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/header.css" rel="stylesheet">
    <link href="/css/index.css" rel="stylesheet">
  </head>
  <body>
    <!-- Inclui o header com navegação e tema -->
    <?php include_once("header.php"); ?>
    <div class="main-box">
      <h1 class="main-title">HYDRAMOTORS</h1>
      <!-- Primeira linha de categorias -->
      <div class="categories-row">
        <div class="category-item">
          <a href="php/categorias/Antiguidade.php">
            <img src="/images/antiguidades.png" alt="Antiguidades">
          </a>
          <span>Antiguidades</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/Sedan.php">
            <img src="/images/seda.png" alt="Sedãs">
          </a>
          <span>Sedãs</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/SUV.php">
            <img src="/images/suv.png" alt="SUVs">
          </a>
          <span>SUVs</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/Hatch.php">
            <img src="/images/hatch.png" alt="Hatchs">
          </a>
          <span>Hatchs</span>
        </div>
      </div>
      <!-- Segunda linha de categorias -->
      <div class="categories-row">
        <div class="category-item">
          <a href="php/categorias/UTE.php">
            <img src="/images/ute.png" alt="Ute/Pickups">
          </a>
          <span>Ute/Pickups</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/Eletrico.php">
            <img src="/images/eletrico.png" alt="Elétricos">
          </a>
          <span>Elétricos</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/Coupe.php">
            <img src="/images/coupe.png" alt="Coupes">
          </a>
          <span>Coupes</span>
        </div>
        <div class="category-item">
          <a href="php/categorias/Hibrido.php">
            <img src="/images/hibrido.png" alt="Híbridos">
          </a>
          <span>Híbridos</span>
        </div>
      </div>
    </div>
    <!-- Importa o JS do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>