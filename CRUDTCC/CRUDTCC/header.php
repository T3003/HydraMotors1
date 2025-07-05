<?php
// Inicia o buffer de saída e a sessão, garantindo que não haja problemas de headers
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';

// Garante que a conexão PDO/SQLite esteja ativa
if (!isset($con) || !$con) {
    if (function_exists('conectar')) {
        $con = conectar();
    } else {
        die('Erro: Função de conexão não encontrada.');
    }
}

// Obtém o nome do script atual para destacar o link ativo
$currentPage = basename($_SERVER['PHP_SELF']);

// Mantém o valor da pesquisa ao navegar entre páginas
$searchValue = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Define os links de navegação principais
$navLinks = [
    ['url' => '/CRUDTCC/index.php', 'text' => 'Home'],
    ['url' => '/CRUDTCC/php/Usuários/crudpf.php', 'text' => 'Criar Conta'],
    ['url' => '/CRUDTCC/help.php', 'text' => 'Ajuda'],
];

// Define os links do menu dropdown da logo
$dropdownLinks = [
    ['url' => '/CRUDTCC/php/Usuários/login.php', 'text' => 'Login'],
    ['url' => '/CRUDTCC/php/categorias/GERAL.php', 'text' => 'Carros'],
    ['url' => '/CRUDTCC/php/Crud.php', 'text' => 'Cadastrar Veículo'],
    ['url' => '/CRUDTCC/php/Vendas/CrudVendas.php', 'text' => 'Cadastrar Veículo para Venda', 'style' => 'background: #4caf50; color: #fff;'],
    ['url' => '/CRUDTCC/php/Vendas/Vendas.php', 'text' => 'Vendas', 'style' => 'background: #ff9800; color: #fff;'],
];

// Se o usuário está logado, ajusta os links de navegação e dropdown
if (isset($_SESSION['logid'])) {
    // Remove "Criar Conta" do navLinks
    $navLinks = array_filter($navLinks, function($link) {
        return $link['text'] !== 'Criar Conta';
    });
    // Remove "Login" do dropdownLinks
    $dropdownLinks = array_filter($dropdownLinks, function($link) {
        return $link['text'] !== 'Login';
    });
    // Adiciona o botão de perfil no início do dropdown
    array_unshift($dropdownLinks, [
        'url' => '/CRUDTCC/php/Usuários/perfil.php',
        'text' => 'Perfil',
        'style' => 'background: #1976d2; color: #fff; font-weight: bold;'
    ]);
    // Adiciona o botão de logoff ao final
    $dropdownLinks[] = ['url' => '/CRUDTCC/php/Usuários/logoff.php', 'text' => 'Logoff', 'style' => 'background: #e53935; color: #fff;'];
    // Busca informações de Adm/Rev do usuário logado
    $logid = isset($_SESSION['logid']) ? $_SESSION['logid'] : null;
    $resAdm = $con->query("SELECT Adm, Rev FROM clilogin WHERE logid = " . $con->quote($logid) . " LIMIT 1");
    if ($resAdm && $rowAdm = $resAdm->fetch(PDO::FETCH_ASSOC)) {
        $isAdm = isset($rowAdm['Adm']) && $rowAdm['Adm'] == 1;
        $isRev = isset($rowAdm['Rev']) && $rowAdm['Rev'] == 1;
        // Se for admin, adiciona o link para lista de contas
        if ($isAdm) {
            $dropdownLinks[] = [
                'url' => '/CRUDTCC/php/Usuários/lista_contas.php',
                'text' => 'Contas do Site',
                'style' => 'background: #222; color: #ffe066; font-weight: bold;'
            ];
        }
    }
}
// Define o tema padrão (não usa mais banco, só localStorage/JS)
$theme = 'light';
?>
<script>
// Script para aplicar o tema salvo no localStorage ao carregar a página
(function() {
  var theme = localStorage.getItem('dark-mode') === '1' ? 'dark' : 'light';
  document.body.classList.remove('light-mode', 'dark-mode');
  document.documentElement.classList.remove('light-mode', 'dark-mode');
  document.body.classList.add(theme + '-mode');
  document.documentElement.classList.add(theme + '-mode');

  // Função para alternar o modo escuro/claro
  function setDarkModeState(isDark) {
    document.body.classList.toggle('dark-mode', isDark);
    document.body.classList.toggle('light-mode', !isDark);
    document.documentElement.classList.toggle('dark-mode', isDark);
    document.documentElement.classList.toggle('light-mode', !isDark);
    if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = isDark ? '☀️' : '🌙';
    if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = isDark ? 'Modo Claro' : 'Modo Escuro';
    // Troca a logo conforme o tema
    var logoImg = document.querySelector('.logo img');
    if (logoImg) {
      logoImg.src = isDark ? '/CRUDTCC/images/LOGODM.png' : '/CRUDTCC/images/LOGO.png';
    }
    // Salva no localStorage
    localStorage.setItem('dark-mode', isDark ? '1' : '0');
  }
  // Inicializa o botão de alternância ao carregar
  document.addEventListener('DOMContentLoaded', function() {
    var toggleDarkDropdown = document.getElementById('toggle-dark-dropdown');
    var localPref = localStorage.getItem('dark-mode');
    if (localPref !== null) {
      setDarkModeState(localPref === '1');
    } else {
      setDarkModeState(theme === 'dark');
    }
    if (toggleDarkDropdown) {
      toggleDarkDropdown.onclick = function(e) {
        e.preventDefault();
        var isDark = !document.body.classList.contains('dark-mode');
        setDarkModeState(isDark);
      };
    }
  });
})();
</script>
<!-- Importa os estilos principais e fontes -->
<link href="/CRUDTCC/css/style.css" rel="stylesheet">
<link href="/CRUDTCC/css/header.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
<style>
/* Estilos para a logo com aura neon conforme o tema */
body.light-mode .logo img {
  border-radius: 18px;
  filter: brightness(0.92) drop-shadow(0 0 2px #00e0ff);
  object-fit: contain;
  height: 300px;
  width: auto;
}
body.dark-mode .logo img {
  border-radius: 18px;
  filter: brightness(0.92) drop-shadow(0 0 2px #a259ff);
  object-fit: contain;
  height: 300px;
  width: auto;
}
</style>
<header>
  <div class="logo">
    <div class="dropdown-logo" id="dropdownLogo">
      <!-- Botão da logo com menu dropdown -->
      <button class="dropdown-toggle-logo" id="dropdownLogoBtn" aria-haspopup="true" aria-expanded="false">
        <img src="/CRUDTCC/images/LOGO.png" alt="HYDRA">
      </button>
      <div class="dropdown-menu-logo" id="dropdownLogoMenu">
        <!-- Links do dropdown -->
        <?php foreach ($dropdownLinks as $link) : ?>
          <a href="<?= $link['url'] ?>" style="<?= $link['style'] ?? '' ?>"><?= $link['text'] ?></a>
        <?php endforeach; ?>
        <?php
        // Link para anúncios ocultos só para admin
        if (isset($isAdm) && $isAdm) : ?>
          <a href="/CRUDTCC/php/Vendas/categorias vendas/Ocultos.php" style="background: #b71c1c; color: #fff; font-weight: bold;">Anúncios Ocultos</a>
        <?php endif; ?>
        <!-- Botão para alternar modo escuro/claro -->
        <button type="button" id="toggle-dark-dropdown" class="toggle-dark-btn" tabindex="0">
          <span id="dark-mode-icon" style="font-size:1.2em;">🌙</span>
          <span id="dark-mode-label">Modo Escuro</span>
        </button>
      </div>
    </div>
  </div>
  <?php if (empty($hideNavbar)): ?>
  <!-- Barra de navegação principal -->
  <nav class="navbar-categorias">
    <ul>
      <?php foreach ($navLinks as $link) : ?>
        <li><a href="<?= $link['url'] ?>" class="nav-link<?= $currentPage == basename($link['url']) ? ' active' : '' ?>" style="<?= $link['style'] ?? '' ?>"><?= $link['text'] ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <?php endif; ?>
  <!-- Barra de pesquisa e botão de filtro -->
  <div class="search-bar" style="display: flex; align-items: center; gap: 10px;">
    <a href="/CRUDTCC/php/categorias/Pesquisa.php" class="btn btn-primary" style="background:#1976d2;color:#fff;font-weight:700;letter-spacing:1px;padding:7px 22px 7px 22px;font-size:1.08rem;border:none;border-radius:7px;text-transform:uppercase;box-shadow:0 2px 8px #1976d244;transition:filter 0.18s,box-shadow 0.18s;outline:none;text-decoration:none !important;display:inline-block;">
      FILTRAR
    </a>
    <form method="get" action="/CRUDTCC/php/categorias/Pesquisa.php" style="margin:0;">
      <input type="text" name="search" placeholder="Pesquisa" value="<?= $searchValue ?>" style="border: 1.5px solid #bbb; border-radius: 6px;" autocomplete="off">
      <button type="submit">
        <img src="/CRUDTCC/images/Lupa.png" alt="Search">
      </button>
    </form>
    <div style="height:28px; border-left:1.5px solid #bbb; margin-left:8px; margin-right:0;"></div>
  </div>
</header>
<script>
  // Script para abrir/fechar o menu dropdown da logo
  const dropdownLogo = document.getElementById('dropdownLogo');
  const dropdownBtn = document.getElementById('dropdownLogoBtn');
  document.addEventListener('click', function(e) {
    if (dropdownLogo.contains(e.target)) {
      dropdownLogo.classList.toggle('show');
    } else {
      dropdownLogo.classList.remove('show');
    }
  });

  // Função para alternar modo escuro/claro (dropdown)
  function setDarkModeState(isDark) {
    if (isDark) {
      document.body.classList.add('dark-mode');
      document.body.classList.remove('light-mode');
      if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = '☀️';
      if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = 'Modo Claro';
    } else {
      document.body.classList.remove('dark-mode');
      document.body.classList.add('light-mode');
      if (document.getElementById('dark-mode-icon')) document.getElementById('dark-mode-icon').textContent = '🌙';
      if (document.getElementById('dark-mode-label')) document.getElementById('dark-mode-label').textContent = 'Modo Escuro';
    }
  }
  function getDarkModeState() {
    return document.body.classList.contains('dark-mode');
  }
  // Inicializa o estado do modo escuro ao carregar
  if (localStorage.getItem('dark-mode')) {
    setDarkModeState(true);
  } else {
    setDarkModeState(false);
  }
  // Botão de alternância no dropdown
  const toggleDarkDropdown = document.getElementById('toggle-dark-dropdown');
  if (toggleDarkDropdown) {
    toggleDarkDropdown.onclick = function(e) {
      e.preventDefault();
      const isDark = !getDarkModeState();
      setDarkModeState(isDark);
      if (isDark) {
        localStorage.setItem('dark-mode', '1');
      } else {
        localStorage.removeItem('dark-mode');
      }
    };
  }
  // Fecha o dropdown de filtros ao clicar fora
  window.onclick = function(event) {
    if (!event.target.matches('button')) {
      var dropdown = document.getElementById('dropdown-filtros');
      if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        dropdown.style.display = 'none';
      }
    }
  }
  // Botão para abrir/fechar dropdown de filtros
  document.querySelector('button[onclick]').addEventListener('click', function() {
    var dropdown = document.getElementById('dropdown-filtros');
    if (dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
    } else {
      dropdown.style.display = 'block';
    }
  });

  // Funções utilitárias para exibir texto de nível de conforto/esportividade
  function nivelTexto(valor) {
    valor = parseInt(valor);
    if (valor <= 3) return "Básico";
    if (valor <= 7) return "Intermediário";
    return "Avançado";
}
function atualizaNivel(inputId, spanId) {
    var valor = document.getElementById(inputId).value;
    document.getElementById(spanId).textContent = nivelTexto(valor);
}
document.addEventListener('DOMContentLoaded', function() {
    atualizaNivel('CarConfort', 'conforto_nivel');
    atualizaNivel('CarSport', 'esportividade_nivel');
});

function nivelConfortoTexto(valor) {
    valor = parseInt(valor);
    if (valor === 1) return "Básico";
    if (valor === 2) return "Intermediário";
    if (valor === 3) return "Confortável";
    if (valor === 4) return "Luxuoso";
    if (valor === 5) return "Premium/Executivo";
    return valor;
}
function nivelEsportividadeTexto(valor) {
    valor = parseInt(valor);
    if (valor === 1) return "Básico";
    if (valor === 2) return "Intermediário";
    if (valor === 3) return "Esportivo";
    if (valor === 4) return "Muito Esportivo";
    if (valor === 5) return "Extremo";
    return valor;
}
function atualizaNivelConforto() {
    var valor = document.getElementById('CarConfort').value;
    document.getElementById('conforto_nivel').textContent = nivelConfortoTexto(valor);
}
function atualizaNivelEsportividade() {
    var valor = document.getElementById('CarSport').value;
    document.getElementById('esportividade_nivel').textContent = nivelEsportividadeTexto(valor);
}
document.addEventListener('DOMContentLoaded', function() {
    atualizaNivelConforto();
    atualizaNivelEsportividade();
});
document.getElementById('btn-filtros').addEventListener('click', function(e) {
    e.stopPropagation();
    var dropdown = document.getElementById('dropdown-filtros');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});
window.onclick = function(event) {
    var dropdown = document.getElementById('dropdown-filtros');
    if (dropdown && dropdown.style.display === 'block' && !event.target.closest('#dropdown-filtros') && !event.target.closest('#btn-filtros')) {
        dropdown.style.display = 'none';
    }
};
  // Atualiza os valores dos sliders ao carregar a página
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('conforto_valor').textContent = document.getElementById('CarConfort').value;
    document.getElementById('esportividade_valor').textContent = document.getElementById('CarSport').value;
  });

  // Unifica a cor do header e da navbar conforme o tema
  function unifyHeaderNavbarColors() {
    const header = document.querySelector('header');
    const navbar = document.querySelector('.navbar-categorias');
    if (header && navbar) {
      if (document.body.classList.contains('dark-mode')) {
        const headerColor = window.getComputedStyle(header).backgroundColor;
        navbar.style.backgroundColor = headerColor;
      } else {
        navbar.style.backgroundColor = '';
      }
    }
  }
  // Chama ao carregar e ao alternar tema
  window.addEventListener('load', unifyHeaderNavbarColors);
  const originalSetDarkModeState = setDarkModeState;
  setDarkModeState = function(isDark) {
    originalSetDarkModeState(isDark);
    unifyHeaderNavbarColors();
  }
</script>
<script>
// Garante troca da logo ao carregar e ao alternar tema
function updateLogoForTheme() {
  var logoImg = document.querySelector('.logo img');
  if (!logoImg) return;
  var isDark = document.body.classList.contains('dark-mode');
  logoImg.src = isDark ? '/CRUDTCC/images/LOGODM.png' : '/CRUDTCC/images/LOGO.png';
}
document.addEventListener('DOMContentLoaded', updateLogoForTheme);
// Observa mudanças de classe para atualizar a logo se o tema mudar dinamicamente
new MutationObserver(updateLogoForTheme).observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>