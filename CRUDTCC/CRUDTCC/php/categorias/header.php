<?php
include __DIR__ . '/../../header.php';
include dirname(__DIR__, 2) . '/conexao.php';

// Get the current script name to determine the active page
$currentPage = basename($_SERVER['PHP_SELF']);

// Valor da pesquisa (mantém o valor ao navegar)
$searchValue = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Define um array de navegação exclusivo para Vendas (exceto Home)
$navLinks = [
    ['url' => '/CRUDTCC/index.php', 'text' => 'Home'],
    ['url' => '/CRUDTCC/php/Vendas/Vendas.php', 'text' => 'Vendas', 'style' => 'background: #ff9800; color: #fff;'],
    ['url' => '/CRUDTCC/php/Vendas/CrudVendas.php', 'text' => 'Cadastrar Veículo para Venda', 'style' => 'background: #4caf50; color: #fff;'],
];
?>
<link href="/CRUDTCC/css/style.css" rel="stylesheet">
<link href="/CRUDTCC/css/header.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
<style>
  html, body {
    height: 100%;
    width: 100%;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', Arial, sans-serif;
  }
  body {
    min-height: 100vh;
    margin: 0;
    padding: 0;
    background: none !important;
    color: #23272a;
  }
  body.light-mode {
    background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed !important;
  }
  body.dark-mode {
    background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
    color: #f5f4ec;
  }
  body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
  }
  header {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 24px auto;
    background: rgba(167, 211, 214, 0.95);
    border-radius: 0 0 18px 18px;
    padding: 0 0 14px 0;
    box-sizing: border-box;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    position: relative;
  }
  .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 18px 0 10px 0;
  }
  .logo img {
    max-height: 300px;
    width: auto;
    filter: drop-shadow(0 2px 6px #0002);
  }
  .dropdown-logo {
    position: relative;
    display: inline-block;
  }
  .dropdown-toggle-logo {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
  }
  .dropdown-logo img {
    max-height: 70px;
    width: auto;
    filter: drop-shadow(0 2px 6px #0002);
    transition: filter 0.2s;
  }
  .dropdown-logo img:hover,
  .dropdown-logo:focus-within img {
    filter: drop-shadow(0 4px 12px #7b5be6);
  }
  .dropdown-menu-logo {
    display: none;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 80px;
    min-width: 180px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
    z-index: 100;
    padding: 10px 0;
  }
  .dropdown-logo.show .dropdown-menu-logo {
    display: block;
  }
  .dropdown-menu-logo a {
    display: block;
    padding: 10px 24px;
    color: #2b2b2b;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
    text-align: left;
    white-space: nowrap;
  }
  .dropdown-menu-logo a.active,
  .dropdown-menu-logo a:hover {
    background: #7b5be6;
    color: #fff;
  }
  .dropdown-menu-logo .toggle-dark-btn {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
    width: 100%;
    background: none;
    border: none;
    color: #444;
    font-weight: 600;
    font-size: 1.05rem;
    padding: 10px 24px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
  }
  .dropdown-menu-logo .toggle-dark-btn:hover {
    background: #eaeaea;
    color: #222;
  }
  .dropdown-menu-logo .toggle-dark-btn.active {
    background: #222;
    color: #fff;
  }
  .search-bar {
    display: flex;
    justify-content: center;
    margin-top: 0;
    margin-bottom: 0;
  }
  .search-bar form {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 8px;
    padding: 2px 8px 2px 12px;
    box-shadow: 0 2px 8px #0001;
  }
  .search-bar input[type="text"] {
    border: none;
    outline: none;
    font-size: 1rem;
    padding: 7px 8px;
    border-radius: 6px;
    background: transparent;
    width: 170px;
    transition: width 0.2s;
  }
  .search-bar input[type="text"]:focus {
    width: 220px;
  }
  .search-bar button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0 5px;
    border-radius: 6px;
    transition: background 0.2s;
  }
  .search-bar button:hover {
    background: #eaeaea;
  }
  .search-bar img {
    width: 22px;
    height: 22px;
    vertical-align: middle;
  }
  /* Categorias navbar */
  .navbar-categorias {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto 0 auto; /* Remove o espaço superior */
    border-radius: 10px;
    background: rgba(167, 211, 214, 0.95); /* mesma cor da header */
    box-shadow: 0 2px 12px #0001;
    padding: 8px 0 2px 0;
  }
  .navbar-categorias ul {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 14px;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  .navbar-categorias .nav-link {
    color: #444;
    font-weight: 600;
    font-size: 1.05rem;
    padding: 6px 14px;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
    text-decoration: none;
  }
  .navbar-categorias .nav-link.active, .navbar-categorias .nav-link:hover {
    background: #7b5be6;
    color: #fff;
  }
  @media (max-width: 1200px) {
    header, .main-box, .help-main-box, .container {
      max-width: 98vw;
    }
  }
  @media (max-width: 700px) {
    .dropdown-menu-logo {
      left: 0;
      right: 0;
      transform: none;
      min-width: 140px;
    }
    .dropdown-menu-logo a {
      font-size: 1rem;
      padding: 10px 14px;
    }
    nav {
      gap: 10px;
    }
    .search-bar input[type="text"] {
      width: 110px;
    }
    .search-bar input[type="text"]:focus {
      width: 150px;
    }
  }

  /* --- DARK MODE --- */
  body.dark-mode {
    background: url('/CRUDTCC/images/BackgroundDM.jpg') no-repeat center center fixed;
    color: #f5f4ec;
  }
  body.dark-mode header {
    background: #002E33 !important;
    color: #fff;
  }
  body.dark-mode .navbar-categorias {
    background: #002E33 !important;
    color: #fff;
  }
  body.dark-mode .dropdown-menu-logo {
    background: #002E33;
    color: #fff;
  }
  body.dark-mode .dropdown-menu-logo a {
    color: #fff;
    background: transparent;
  }
  body.dark-mode .dropdown-menu-logo a.active,
  body.dark-mode .dropdown-menu-logo a:hover {
    background: #7b5be6;
    color: #fff;
  }
  body.dark-mode .dropdown-menu-logo .toggle-dark-btn {
    color: #fff;
    background: none;
  }
  body.dark-mode .dropdown-menu-logo .toggle-dark-btn:hover {
    background: #222;
    color: #fff;
  }
  body.dark-mode .search-bar form {
    background: #002E33;
    box-shadow: 0 2px 8px #0004;
  }
  body.dark-mode .search-bar input[type="text"] {
    background: transparent;
    color: #fff;
  }
  body.dark-mode .search-bar button:hover {
    background: #222;
  }
  /* Troca branco para preto e bege para cinza quente escuro */
  body.dark-mode,
  body.dark-mode .main-box,
  body.dark-mode .categories-panel,
  body.dark-mode .card,
  body.dark-mode .card-body {
    background-color: #2C2B28 !important;
    color: #fff !important;
  }
  body.dark-mode .card-title {
    color: #fff !important;
  }
  body.dark-mode .card-img-top {
    background: #222 !important;
  }
  /* Troca preto para branco em textos principais */
  body.dark-mode h1,
  body.dark-mode h2,
  body.dark-mode h3,
  body.dark-mode h4,
  body.dark-mode h5,
  body.dark-mode h6,
  body.dark-mode label,
  body.dark-mode .form-label,
  body.dark-mode .category-item span {
    color: #fff !important;
  }
</style>
<!-- REMOVIDO include do header.php para evitar erro de sessão -->
<nav class="navbar-categorias" style="margin-top:0;">
  <ul>
    <li><a href="/CRUDTCC/php/categorias/GERAL.php" class="nav-link<?= $currentPage == 'GERAL.php' ? ' active' : '' ?>">Todos</a></li>
    <li><a href="/CRUDTCC/php/categorias/Antiguidade.php" class="nav-link<?= $currentPage == 'Antiguidade.php' ? ' active' : '' ?>">Antiguidade</a></li>
    <li><a href="/CRUDTCC/php/categorias/Coupe.php" class="nav-link<?= $currentPage == 'Coupe.php' ? ' active' : '' ?>">Coupé</a></li>
    <li><a href="/CRUDTCC/php/categorias/Hatch.php" class="nav-link<?= $currentPage == 'Hatch.php' ? ' active' : '' ?>">Hatch</a></li>
    <li><a href="/CRUDTCC/php/categorias/Sedan.php" class="nav-link<?= $currentPage == 'Sedan.php' ? ' active' : '' ?>">Sedan</a></li>
    <li><a href="/CRUDTCC/php/categorias/SUV.php" class="nav-link<?= $currentPage == 'SUV.php' ? ' active' : '' ?>">SUV</a></li>
    <li><a href="/CRUDTCC/php/categorias/UTE_Picape.php" class="nav-link<?= $currentPage == 'UTE_Picape.php' ? ' active' : '' ?>">UTE/Picape</a></li>
    <li><a href="/CRUDTCC/php/categorias/Eletrico.php" class="nav-link<?= $currentPage == 'Eletrico.php' ? ' active' : '' ?>">Elétrico</a></li>
    <li><a href="/CRUDTCC/php/categorias/Hibrido.php" class="nav-link<?= $currentPage == 'Hibrido.php' ? ' active' : '' ?>">Híbrido</a></li>
  </ul>
</nav>
<script>
  // Dropdown menu toggle
  const dropdownLogo = document.getElementById('dropdownLogo');
  const dropdownBtn = document.getElementById('dropdownLogoBtn');
  document.addEventListener('click', function(e) {
    if (dropdownLogo.contains(e.target)) {
      dropdownLogo.classList.toggle('show');
    } else {
      dropdownLogo.classList.remove('show');
    }
  });

  // Dark mode toggle (dropdown)
  // Ao trocar o tema, salva em cookie para outras páginas PHP
  function setDarkModeState(isDark) {
    if (isDark) {
      document.body.classList.add('dark-mode');
      document.body.classList.remove('light-mode');
      if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = '☀️';
      if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = 'Modo Claro';
      localStorage.setItem('darkMode', '1');
      document.cookie = "tema=dark;path=/";
    } else {
      document.body.classList.remove('dark-mode');
      document.body.classList.add('light-mode');
      if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = '🌙';
      if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = 'Modo Escuro';
      localStorage.setItem('darkMode', '0');
      document.cookie = "tema=light;path=/";
    }
  }
  function getDarkModeState() {
    return document.body.classList.contains('dark-mode');
  }
  // Inicializa estado ao carregar
  window.addEventListener('DOMContentLoaded', function() {
    // Prioriza tema do GET, depois cookie, depois localStorage
    var tema = '';
    var params = new URLSearchParams(window.location.search);
    if (params.has('tema')) {
      tema = params.get('tema');
    } else if (document.cookie.match(/(^|;) ?tema=([^;]*)(;|$)/)) {
      tema = document.cookie.replace(/(?:(?:^|.*;\s*)tema\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    }
    if (tema === 'dark') {
      setDarkModeState(true);
    } else if (tema === 'light') {
      setDarkModeState(false);
    } else {
      var dark = localStorage.getItem('darkMode') === '1';
      setDarkModeState(dark);
    }
  });
  // Botão no dropdown
  const toggleDarkDropdown = document.getElementById('toggle-dark-dropdown');
  if (toggleDarkDropdown) {
    toggleDarkDropdown.onclick = function(e) {
      e.preventDefault();
      const isDark = !getDarkModeState();
      setDarkModeState(isDark);
    };
  }
</script>