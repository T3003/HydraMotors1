<?php
// theme.php - Inclua este arquivo no início de todas as páginas para herdar o tema claro/escuro
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['theme'])) {
    // Detecta preferência do sistema na primeira visita (fallback: claro)
    echo '<script>if(window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches){document.cookie="theme=dark;path=/";}else{document.cookie="theme=light;path=/";}</script>';
    $_SESSION['theme'] = 'light';
}
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = ($_GET['theme'] === 'dark') ? 'dark' : 'light';
    setcookie('theme', $_SESSION['theme'], time()+60*60*24*30, '/');
}
$theme = $_SESSION['theme'] ?? ($_COOKIE['theme'] ?? 'light');
?>
<script>
(function(){
    var theme = '<?php echo $theme; ?>';
    if(theme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.documentElement.classList.remove('light-mode');
    } else {
        document.documentElement.classList.add('light-mode');
        document.documentElement.classList.remove('dark-mode');
    }
})();
</script>
