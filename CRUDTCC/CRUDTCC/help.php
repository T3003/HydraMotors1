<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda-Hydramotors</title>
    <!-- Importa os estilos globais e específicos da página de ajuda -->
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/help.css" rel="stylesheet">
    <style>
      /* Estilos para dark mode na página de ajuda */
      body.dark-mode .help-col {
        background: #23272a !important;
        color: #f5f4ec !important;
        box-shadow: 0 2px 12px #0008;
        border: 1.5px solid #ffe066;
      }
      body.dark-mode .help-title,
      body.dark-mode .help-text,
      body.dark-mode .help-info-box {
        color: #ffe066 !important;
      }
      body.dark-mode .help-info-box {
        background: rgba(44,43,40,0.97) !important;
        border-radius: 10px;
      }
    </style>
</head>
<body>
    <?php
    // Inclui o header com navegação e tema
    include 'header.php';
    ?>
    <!-- Container principal centralizado -->
    <div class="main-box d-flex flex-column align-items-center" style="min-height: 70vh; justify-content: center; max-width: 900px; width: 98%; margin: 40px auto; padding: 32px 18px;">
      <div class="help-main-box" style="width:100%; display:flex; flex-wrap:wrap; gap:32px; justify-content:center; align-items:stretch;">
        <!-- Box com informações sobre o site -->
        <div class="help-col" style="flex:1 1 320px; min-width:260px; max-width:420px; background:#fffbe6; border-radius:16px; box-shadow:0 2px 12px #bfa13a22; padding:24px 18px; margin-bottom:0;">
          <div class="help-title" style="font-size:2.1rem; text-align:center; margin-bottom:18px;">HYDRAMOTORS</div>
          <div class="help-text" style="font-size:1.18rem; text-align:center; font-weight:500; margin-bottom:22px;">
            A HydraMotors tem como finalidade servir como um Servidor público onde as pessoas brasileiras podem postar carros para a venda com informações de tudo, inclusive manutenção e/ou só informações de carros específicos, para a satisfação alheia
          </div>
        </div>
        <!-- Box com informações sobre compra e contato -->
        <div class="help-col" style="flex:1 1 320px; min-width:260px; max-width:420px; background:#fffbe6; border-radius:16px; box-shadow:0 2px 12px #bfa13a22; padding:24px 18px; margin-bottom:0;">
          <div class="help-title" style="font-size:1.7rem; text-align:center; margin-bottom:14px;">INFO. COMPRA</div>
          <div class="help-info-box" style="font-size:1.13rem; text-align:center; font-weight:500;">
            A HydraMotors não haverá compras diretamente dentro do site, por favor se refira ao contato do usuário que fez a postagem do carro para completar a transação. A HydraMotors não se responsabilizará caso você leve um golpe. Qualquer dúvida, contate <b>Hydra.0.Motors@gmail.com</b><br>
            16 99429-1882
          </div>
        </div>
      </div>
    </div>
    <script>
      // Função para aplicar ou remover dark mode na página
      function setDarkMode(state) {
        if(state) {
          document.body.classList.add('dark-mode');
        } else {
          document.body.classList.remove('dark-mode');
        }
      }
      // Aplica o dark mode conforme preferência salva no localStorage
      if(localStorage.getItem('dark-mode') === '1') setDarkMode(true);
      else setDarkMode(false);
      // Atualiza o tema se o localStorage mudar em outra aba
      window.addEventListener('storage', function(e) {
        if(e.key === 'dark-mode') {
          setDarkMode(localStorage.getItem('dark-mode') === '1');
        }
      });
    </script>
</body>
</html>