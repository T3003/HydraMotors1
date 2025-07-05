<?php
// tutorial_logo.php
session_start();
if (!isset($_SESSION['logid'])) {
    header('Location: /CRUDTCC/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo! Tutorial Rápido</title>
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <style>
        body.light-mode {
            background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed !important;
            color: #232323;
        }
        body.dark-mode {
            background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
            color: #f5f4ec;
        }
        .tutorial-box {
            max-width: 520px;
            margin: 60px auto 0 auto;
            background: #232323e6;
            color: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 16px #0008;
            padding: 36px 32px 28px 32px;
            text-align: center;
        }
        body.light-mode .tutorial-box {
            background: #fffbe6;
            color: #232323;
            box-shadow: 0 2px 16px #bfa13a44;
        }
        .tutorial-box h2 {
            color: #ffe066;
            font-size: 2.1rem;
            margin-bottom: 18px;
        }
        body.light-mode .tutorial-box h2 {
            color: #bfa13a;
        }
        .tutorial-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 18px;
            border: 3px solid #ffe066;
        }
        body.light-mode .tutorial-box img {
            border: 3px solid #bfa13a;
        }
        .tutorial-box .btn {
            margin-top: 28px;
            font-size: 1.1rem;
            padding: 10px 32px;
            border-radius: 8px;
        }
        .btn-tutorial {
            background: linear-gradient(90deg, #ffe066 0%, #bfa13a 50%, #e0e0e0 100%);
            color: #232323 !important;
            font-weight: bold;
            border: none;
            box-shadow: 0 2px 8px #bfa13a44;
            transition: filter 0.2s, box-shadow 0.2s;
            outline: none;
            letter-spacing: 0.5px;
            text-decoration: none !important;
        }
        .btn-tutorial:hover, .btn-tutorial:focus {
            filter: brightness(1.08) drop-shadow(0 0 6px #ffe066cc);
            box-shadow: 0 4px 18px #ffe06699;
            color: #232323 !important;
            text-decoration: none !important;
        }
        .tutorial-highlight {
            color: #ffe066;
            font-weight: bold;
        }
        body.light-mode .tutorial-highlight {
            color: #bfa13a;
        }
        .tutorial-arrow {
            font-size: 2.2rem;
            color: #ffe066;
            margin: 18px 0 8px 0;
            display: block;
        }
        body.light-mode .tutorial-arrow {
            color: #bfa13a;
        }
    </style>
</head>
<body>
<?php include_once("../../header.php"); ?>
<div class="tutorial-box">
    <img id="logoImg" src="/CRUDTCC/images/LOGO.png" alt="Logo Hydra Motors" style="width:90px;height:90px;border-radius:50%;margin-bottom:18px;border:3px solid #ffe066;background:#fff;">
    <h2 style="color:inherit;font-size:2.2rem;font-weight:900;margin-bottom:18px;">Bem-vindo ao Hydra Motors!</h2>
    <p style="font-size:1.15rem;">Antes de começar, veja como acessar as principais funções do site:</p>
    <span class="tutorial-arrow">&#8595;</span>
    <div style="font-size:1.13rem;margin-bottom:18px;">
        No topo da página, você verá o <span class="tutorial-highlight">LOGO DA HYDRA</span>.<br>
        <strong>Clique no logo</strong> para abrir o <span class="tutorial-highlight">MENU DROPDOWN</span>.<br>
        Nele você pode acessar rapidamente seu perfil, anúncios, configurações e sair da conta.
    </div>
    <div style="margin:0 auto 18px auto;">
        <img id="tutorialImg" src="/CRUDTCC/images/TUTORIAL.png?v=<?= time() ?>" alt="Exemplo do dropdown" style="width:260px;max-width:90vw;height:auto;border-radius:12px;border:2px solid #ffe066;box-shadow:0 2px 12px #0006;cursor:zoom-in;background:#fff;">
    </div>
    <p style="margin-top:10px;font-size:1.08rem;">Explore o menu sempre que precisar navegar pelo site!</p>
    <a href="/CRUDTCC/index.php" class="btn btn-tutorial" style="margin-top:18px;">Começar a usar</a>
</div>
<div id="zoomModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;backdrop-filter:blur(2px);">
    <img id="zoomImg" src="" style="max-width:90vw;max-height:90vh;border-radius:12px;box-shadow:0 4px 32px #000a;">
</div>
<script>
// Zoom da imagem do tutorial
const tutorialImg = document.getElementById('tutorialImg');
const zoomModal = document.getElementById('zoomModal');
const zoomImg = document.getElementById('zoomImg');
tutorialImg.addEventListener('click', function() {
    zoomImg.src = this.src;
    zoomModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
});
zoomModal.addEventListener('click', function() {
    zoomModal.style.display = 'none';
    document.body.style.overflow = 'auto';
});

// Alterna logo conforme modo escuro/claro
function updateLogoImg() {
    const logoImg = document.getElementById('logoImg');
    if (!logoImg) return;
    if (document.body.classList.contains('dark-mode')) {
        logoImg.src = '/CRUDTCC/images/LOGODM.png';
    } else {
        logoImg.src = '/CRUDTCC/images/LOGO.png';
    }
}

// Detecta mudança de modo
function setThemeByTime() {
    const hour = new Date().getHours();
    if (hour >= 7 && hour < 19) {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
    } else {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    }
    updateLogoImg();
}
setThemeByTime();

// Se o usuário alternar manualmente o modo (ex: via localStorage em outro lugar)
window.addEventListener('storage', function(e) {
    if (e.key === 'dark-mode') updateLogoImg();
});

// Se o modo for alternado por outro script
const observer = new MutationObserver(updateLogoImg);
observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>
</body>
</html>
