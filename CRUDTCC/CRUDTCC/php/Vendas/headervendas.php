<?php
// Header de vendas adaptado
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Sempre usa PDO/SQLite
if (!isset($con) || !$con) {
    if (function_exists('conectar')) {
        $con = conectar();
    } else {
        die('Erro: Função de conexão não encontrada.');
    }
}

include dirname(__DIR__, 2) . '/conexao.php';

// Detecta se está em uma página de vendas
$currentPage = basename($_SERVER['PHP_SELF']);
$searchValue = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Links do navbar principal (apenas 3 botões)
$navbarLinks = [
    ['url' => '/CRUDTCC/index.php', 'text' => 'Home'],
    ['url' => '/CRUDTCC/php/Vendas/categorias vendas/GERAL.php', 'text' => 'Carros À Venda', 'style' => 'background: #ff9800; color: #fff;'],
    ['url' => '/CRUDTCC/help.php', 'text' => 'Ajuda'],
];

// Links extras para o dropdown
$dropdownLinks = [
    ['url' => '/CRUDTCC/php/Usuários/login.php', 'text' => 'Login'],
    ['url' => '/CRUDTCC/php/categorias/GERAL.php', 'text' => 'Carros'],
    ['url' => '/CRUDTCC/php/Crud.php', 'text' => 'Cadastrar Veículo'],
    ['url' => '/CRUDTCC/php/Vendas/CrudVendas.php', 'text' => 'Cadastrar Veículo para Venda', 'style' => 'background: #4caf50; color: #fff;'],
    ['url' => '/CRUDTCC/php/Vendas/Vendas.php', 'text' => 'Vendas', 'style' => 'background: #ff9800; color: #fff;'],
];

if (isset($_SESSION['logid'])) {
    // Remove "Login" do dropdownLinks
    $dropdownLinks = array_filter($dropdownLinks, function($link) {
        return $link['text'] !== 'Login';
    });
    // Garante que o botão de perfil sempre apareça logo após o login
    array_unshift($dropdownLinks, [
        'url' => '/CRUDTCC/php/Usuários/perfil.php',
        'text' => 'Perfil',
        'style' => 'background: #1976d2; color: #fff; font-weight: bold;'
    ]);
    $dropdownLinks[] = ['url' => '/CRUDTCC/php/Usuários/logoff.php', 'text' => 'Logoff', 'style' => 'background: #e53935; color: #fff;'];
    $logid = $_SESSION['logid'];
    $resAdm = $con->query("SELECT Adm, Rev FROM clilogin WHERE logid = " . $con->quote($logid) . " LIMIT 1");
    if ($resAdm && $rowAdm = $resAdm->fetch(PDO::FETCH_ASSOC)) {
        $isAdm = isset($rowAdm['Adm']) && $rowAdm['Adm'] == 1;
        $isRev = isset($rowAdm['Rev']) && $rowAdm['Rev'] == 1;
        if ($isAdm) {
            $dropdownLinks[] = [
                'url' => '/CRUDTCC/php/Usuários/lista_contas.php',
                'text' => 'Contas do Site',
                'style' => 'background: #222; color: #ffe066; font-weight: bold;'
            ];
        }
    }
}


?>
<script>
(function() {
  // Detecta tema salvo no localStorage ou usa o padrão do PHP
  var theme = localStorage.getItem('dark-mode') === '1' ? 'dark' : (typeof <?= isset($theme) ? json_encode($theme) : "'light'" ?> === 'string' ? <?= isset($theme) ? json_encode($theme) : "'light'" ?> : 'light');
  function clearThemeCache() {
    // Limpa apenas o cache relacionado ao tema
    localStorage.removeItem('theme');
    localStorage.removeItem('dark-mode');
    // Se houver outros itens relacionados ao tema, adicione aqui
  }
  function setDarkModeState(isDark) {
    clearThemeCache(); // Limpa o cache ao alternar o tema
    document.body.classList.toggle('dark-mode', isDark);
    document.body.classList.toggle('light-mode', !isDark);
    document.documentElement.classList.toggle('dark-mode', isDark);
    document.documentElement.classList.toggle('light-mode', !isDark);
    if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = isDark ? '☀️' : '🌙';
    if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = isDark ? 'Modo Claro' : 'Modo Escuro';
    // Atualiza campo hidden do formulário
    var themeInput = document.getElementById('theme-hidden-input');
    if (themeInput) themeInput.value = isDark ? 'dark' : 'light';
    // Salva no localStorage
    localStorage.setItem('dark-mode', isDark ? '1' : '0');
    // Salva no servidor se logado
    <?php if (isset($_SESSION['logid'])): ?>
    fetch('/CRUDTCC/php/Usuários/salva_tema.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'theme=' + (isDark ? '1' : '0')
    });
    <?php endif; ?>
  }
  function getDarkModeState() {
    return document.body.classList.contains('dark-mode');
  }
  document.addEventListener('DOMContentLoaded', function() {
    // Aplica o tema ao carregar
    setDarkModeState(theme === 'dark');
    // Dropdown menu toggle igual ao header principal
    const dropdownLogo = document.getElementById('dropdownLogo');
    const dropdownBtn = document.getElementById('dropdownLogoBtn');
    if (dropdownBtn && dropdownLogo) {
      dropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownLogo.classList.toggle('show');
      });
      document.addEventListener('click', function(e) {
        if (!dropdownLogo.contains(e.target)) {
          dropdownLogo.classList.remove('show');
        }
      });
    }
    // Botão no dropdown
    const toggleDarkDropdown = document.getElementById('toggle-dark-dropdown');
    if (toggleDarkDropdown) {
      toggleDarkDropdown.onclick = function(e) {
        e.preventDefault();
        const isDark = !getDarkModeState();
        setDarkModeState(isDark);
      };
    }
    // Estilo de hover para o botão FILTRAR
    const filtrarBtn = document.getElementById('filtrar-btn');
    if (filtrarBtn) {
      filtrarBtn.addEventListener('mouseover', function() {
        filtrarBtn.style.background = '#1769aa';
      });
      filtrarBtn.addEventListener('mouseout', function() {
        filtrarBtn.style.background = '#2196f3';
      });
    }
  });
})();
</script>
<link href="/CRUDTCC/css/style.css" rel="stylesheet">
<link href="/CRUDTCC/css/header.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
<style>
  /* Corrige conflitos e garante visual consistente com o header principal */
  body.light-mode header {
    background: rgba(167, 211, 214, 0.95) !important;
    color: #232323 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07) !important;
    border-radius: 0 0 18px 18px !important;
  }
  body.dark-mode header {
    background:#13313a !important;
    color: #ffe066 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.30) !important;
    border-radius: 0 0 18px 18px !important;
  }
  body.light-mode .navbar-categorias {
    background: rgba(167, 211, 214, 0.95) !important; 
    color: #232323 !important;
    box-shadow: 0 2px 12px #0001 !important;
  }
  body.dark-mode .navbar-categorias {
    background: #13313a !important;
    color: #ffe066 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.30) !important;
  }
  body.light-mode .dropdown-menu-logo {
    display: none;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 80px;
    min-width: 180px;
    background: rgba(167, 211, 214, 0.95) !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12) !important;
    z-index: 100;
    padding: 10px 0;
    color: #2b2b2b !important;
  }
  .dropdown-logo.show .dropdown-menu-logo {
    display: block !important;
  }
  body.light-mode .dropdown-menu-logo a {
    display: block;
    padding: 10px 24px;
    color: #2b2b2b !important;
    /* Remove background: transparent, pois usaremos cores por botão */
    margin-bottom: 7px;
    font-weight: 700;
    font-size: 1.08rem;
  }
  /* Cores dos botões do dropdown */
  body.light-mode .dropdown-menu-logo a[data-btn="perfil"]    { background: #1976d2 !important; color: #fff !important; }
  body.light-mode .dropdown-menu-logo a[data-btn="carros"]    
  body.light-mode .dropdown-menu-logo a[data-btn="cadastro"]  
  body.light-mode .dropdown-menu-logo a[data-btn="cad-venda"] { background: #4caf50 !important; color: #fff !important; }
  body.light-mode .dropdown-menu-logo a[data-btn="vendas"]    { background: #ff9800 !important; color: #fff !important; }
  body.light-mode .dropdown-menu-logo a[data-btn="logoff"]    { background: #e53935 !important; color: #fff !important; }
  body.light-mode .dropdown-menu-logo a[data-btn="login"]     { background: #1976d2 !important; color: #fff !important; }
  body.light-mode .dropdown-menu-logo a[data-btn="contas"]    { background: #222 !important; color: #ffe066 !important; }
  /* Hover */
  body.light-mode .dropdown-menu-logo a:hover {
    filter: brightness(1.08) contrast(1.1);
    opacity: 0.93;
  }
  /* Dark mode */
  body.dark-mode .dropdown-menu-logo a[data-btn="perfil"]    { background: #1976d2 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="carros"]    
  body.dark-mode .dropdown-menu-logo a[data-btn="cadastro"]  { background: #009688 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="cad-venda"] { background: #4caf50 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="vendas"]    { background: #ff9800 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="logoff"]    { background: #e53935 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="login"]     { background: #1976d2 !important; color: #fff !important; }
  body.dark-mode .dropdown-menu-logo a[data-btn="contas"]    { background: #222 !important; color: #ffe066 !important; }
  body.dark-mode .dropdown-menu-logo a:hover {
    filter: brightness(1.12) contrast(1.1);
    opacity: 0.93;
  }
  body.light-mode .dropdown-menu-logo .toggle-dark-btn {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
    width: 100%;
    background: none !important;
    border: none;
    color: #444 !important;
    font-weight: 600;
    font-size: 1.05rem;
    padding: 10px 24px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
  }
  body.light-mode .dropdown-menu-logo .toggle-dark-btn:hover {
    background: #eaeaea !important;
    color: #222 !important;
  }
  body.light-mode .dropdown-menu-logo .toggle-dark-btn.active {
    background: #222 !important;
    color: #fff !important;
  }
  body.dark-mode .dropdown-menu-logo {
    display: none;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 80px;
    min-width: 180px;
    background: #002E33 !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12) !important;
    z-index: 100;
    padding: 10px 0;
    color: #fff !important;
  }
  .dropdown-logo.show .dropdown-menu-logo {
    display: block !important;
  }
  body.dark-mode .dropdown-menu-logo a {
    color: #fff !important;
    background: transparent !important;
  }
  body.dark-mode .dropdown-menu-logo a.active,
  body.dark-mode .dropdown-menu-logo a:hover {
    background: #7b5be6 !important;
    color: #fff !important;
  }
  body.dark-mode .dropdown-menu-logo .toggle-dark-btn {
    color: #fff !important;
    background: none !important;
  }
  body.dark-mode .dropdown-menu-logo .toggle-dark-btn:hover {
    background: #222 !important;
    color: #fff !important;
  }
  body.dark-mode .dropdown-menu-logo .toggle-dark-btn.active {
    background: #fff !important;
    color: #222 !important;
  }
  body.light-mode .toggle-dark-btn {
    background: #2196f3 !important;
    color: #fff !important;
  }
  body.dark-mode .toggle-dark-btn {
    background: #ffe066 !important;
    color: #232323 !important;
  }
  body.light-mode .toggle-dark-btn:hover {
    background: #1769aa !important;
  }
  body.dark-mode .toggle-dark-btn:hover {
    background: #ffb300 !important;
    color: #232323 !important;
  }
  body.light-mode .search-bar input[type="text"] {
    background: #fff !important;
    color: #232323 !important;
    border: 1.5px solid #bbb !important;
  }
  body.dark-mode .search-bar input[type="text"] {
    background: #2c2f34 !important;
    
  }
  body.light-mode .search-bar form {
    background: #fff !important;
    box-shadow: 0 2px 8px #0001 !important;
  }
  body.dark-mode .search-bar form {
    background: #002E33 !important;
    box-shadow: 0 2px 8px #0004 !important;
  }
  body.light-mode .logo img {
    box-shadow: 0 0 24px 0 #00e0ff, ;
    border-radius: 18px;
    filter: brightness(0.92) drop-shadow(0 0 2px #00e0ff);
  }
  /* Aura roxa neon forte ao redor da logo no modo escuro */
  body.dark-mode .logo img {
    box-shadow: 0 0 24px 0 #a259ff, ;
    border-radius: 18px;
    filter: brightness(0.92) drop-shadow(0 0 2px #a259ff);
  }
</style>
<header>
  <div class="logo">
    <div class="dropdown-logo" id="dropdownLogo">
      <button class="dropdown-toggle-logo" id="dropdownLogoBtn" aria-haspopup="true" aria-expanded="false">
        <img id="main-logo" src="/CRUDTCC/images/LOGO.png" alt="HYDRA">
      </button>
      <div class="dropdown-menu-logo" id="dropdownLogoMenu">
        <?php foreach ($dropdownLinks as $link) : 
          // Define o data-btn para cada botão pelo texto
          $btnType = '';
          switch (mb_strtolower($link['text'])) {
            case 'perfil': $btnType = 'perfil'; break;
            case 'carros': $btnType = 'carros'; break;
            case 'cadastrar veículo': $btnType = 'cadastro'; break;
            case 'cadastrar veículo para venda': $btnType = 'cad-venda'; break;
            case 'vendas': $btnType = 'vendas'; break;
            case 'logoff': $btnType = 'logoff'; break;
            case 'login': $btnType = 'login'; break;
            case 'contas do site': $btnType = 'contas'; break;
            default: $btnType = '';
          }
        ?>
          <a href="<?= $link['url'] ?>" style="<?= $link['style'] ?? '' ?>"<?= $btnType ? ' data-btn="'.$btnType.'"' : '' ?>><?= $link['text'] ?></a>
        <?php endforeach; ?>
        <button type="button" id="toggle-dark-dropdown" class="toggle-dark-btn" tabindex="0">
          <span id="dark-mode-icon" style="font-size:1.2em;">🌙</span>
          <span id="dark-mode-label">Modo Escuro</span>
        </button>
      </div>
    </div>
  </div>
  <nav class="navbar-categorias">
    <ul>
      <?php foreach ($navbarLinks as $link): ?>
        <li><a href="<?= $link['url'] ?>" class="nav-link<?= $currentPage == basename($link['url']) ? ' active' : '' ?>" style="<?= $link['style'] ?? '' ?>"><?= $link['text'] ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <div class="search-bar" style="display: flex; align-items: center; gap: 10px; margin-right: 18px;">
    <form method="get" action="/CRUDTCC/php/Vendas/categorias vendas/Pesquisa.php" style="margin:0; display: flex; align-items: center; gap: 10px;">
      <input type="hidden" name="theme" id="theme-hidden-input" value="<?= htmlspecialchars($theme) ?>">
      <button type="submit" id="filtrar-btn" style="background: #2196f3; color: #fff; font-weight: bold; border: none; border-radius: 6px; padding: 7px 22px; font-size: 1.1em; cursor: pointer; margin-right: 5px; transition: background 0.2s;">FILTRAR</button>
      <input type="text" name="search" placeholder="Pesquisa" value="<?= $searchValue ?>" style="border: 1.5px solid #bbb; border-radius: 6px;" autocomplete="off">
      <button type="submit">
        <img src="/CRUDTCC/images/Lupa.png" alt="Search">
      </button>
    </form>
  </div>
</header>
<script>
// Dropdown menu toggle igual ao header principal
const dropdownLogo = document.getElementById('dropdownLogo');
const dropdownBtn = document.getElementById('dropdownLogoBtn');
document.addEventListener('click', function(e) {
  if (dropdownLogo.contains(e.target)) {
    dropdownLogo.classList.toggle('show');
  } else {
    dropdownLogo.classList.remove('show');
  }
});
(function() {
  // Troca a logo conforme o tema
  function updateLogoByTheme() {
    var isDark = document.body.classList.contains('dark-mode');
    var logoImg = document.getElementById('main-logo');
    if (logoImg) {
      logoImg.src = isDark ? '/CRUDTCC/images/LOGODM.png' : '/CRUDTCC/images/LOGO.png';
    }
  }
  // Função para limpar cache do site inteiro
  function clearSiteCache() {
    localStorage.clear();
    sessionStorage.clear();
    // Cookies não são limpos via JS por segurança, mas pode-se expirar manualmente se necessário
  }
  document.addEventListener('DOMContentLoaded', function() {
    updateLogoByTheme();
    // Garante que a logo troca ao alternar tema manualmente
    const toggleDarkDropdown = document.getElementById('toggle-dark-dropdown');
    if (toggleDarkDropdown) {
      toggleDarkDropdown.addEventListener('click', function() {
        setTimeout(updateLogoByTheme, 10); // Pequeno delay para garantir troca após classe
      });
    }
  });
  // Atualiza ao alternar o tema
  var originalSetDarkModeState = window.setDarkModeState;
  window.setDarkModeState = function(isDark) {
    clearSiteCache();
    if (typeof originalSetDarkModeState === 'function') originalSetDarkModeState(isDark);
    updateLogoByTheme();
  };
})();
</script>
