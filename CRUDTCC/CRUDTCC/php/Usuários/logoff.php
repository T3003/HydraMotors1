<?php
session_start();
session_unset();
session_destroy();
header('Location: /CRUDTCC/index.php');
exit;
?>
<link href="/CRUDTCC/css/style.css" rel="stylesheet">
<link href="/CRUDTCC/css/header.css" rel="stylesheet">
<link href="/CRUDTCC/css/logoff.css" rel="stylesheet">
<!-- Remover qualquer bloco <style>...</style> daqui -->
