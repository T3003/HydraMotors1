<?php
// Corrige o caminho para headervendas.php
include dirname(__DIR__, 1) . '/headervendas.php';
// Garante que a variável $theme esteja disponível corretamente
if (!isset($theme) || !in_array($theme, ['dark','light'])) {
    if (isset($_GET['theme']) && in_array($_GET['theme'], ['dark','light'])) {
        $theme = $_GET['theme'];
    } elseif (isset($_SESSION['theme']) && in_array($_SESSION['theme'], ['dark','light'])) {
        $theme = $_SESSION['theme'];
    } elseif (isset($_COOKIE['theme']) && in_array($_COOKIE['theme'], ['dark','light'])) {
        $theme = $_COOKIE['theme'];
    } else {
        $theme = 'light';
    }
}
?>
<nav class="navbar-categorias" style="margin-top:0;">
  <ul>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/GERAL.php?theme=<?= urlencode($theme) ?>" class="nav-link">Todos</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Antiguidade.php?theme=<?= urlencode($theme) ?>" class="nav-link">Antiguidade</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Coupe.php?theme=<?= urlencode($theme) ?>" class="nav-link">Coupé</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Hatch.php?theme=<?= urlencode($theme) ?>" class="nav-link">Hatch</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Sedan.php?theme=<?= urlencode($theme) ?>" class="nav-link">Sedan</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/SUV.php?theme=<?= urlencode($theme) ?>" class="nav-link">SUV</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/UTE_Picape.php?theme=<?= urlencode($theme) ?>" class="nav-link">UTE/Picape</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Eletrico.php?theme=<?= urlencode($theme) ?>" class="nav-link">Elétrico</a></li>
    <li><a href="/CRUDTCC/php/Vendas/categorias vendas/Hibrido.php?theme=<?= urlencode($theme) ?>" class="nav-link">Híbrido</a></li>
  </ul>
</nav>