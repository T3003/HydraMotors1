<?php
// Inclui o arquivo de configuração do banco de dados
include '../../config.php';

// Realiza a conexão com o banco de dados
$con = conectar();
// Diretório onde estão as imagens dos veículos à venda
$imageDir = '../../Vendas/Imagens/';

// Recebe o nome do veículo via GET
$carName = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// Inicializa a variável que irá armazenar os dados do veículo
$car = null;
if ($carName !== '') {
    $carNameEscaped = $con->quote($carName);
    // Busca na tabela correta: veiculos_venda
    $sql = "SELECT * FROM veiculos_venda WHERE nome = $carNameEscaped LIMIT 1";
    $res = $con->query($sql);
    $car = $res->fetch(PDO::FETCH_ASSOC);
}

// Array de nomes das categorias conforme os IDs
$nomesCategorias = [
  1 => 'Antiguidade',
  2 => 'Coupé',
  3 => 'Sedan',
  4 => 'Hatch',
  5 => 'SUV',
  6 => 'UTE/Picape',
  7 => 'Híbrido',
  8 => 'Elétrico'
];
// Inicia a sessão ANTES de qualquer saída HTML
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale">
    <title><?= $car ? htmlspecialchars($car['nome']) : 'Veículo não encontrado' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/carro.css" rel="stylesheet">
    <?php include_once("header.php"); ?>
    <style>
      body {
        min-height: 100vh;
        margin: 0;
        padding: 0;
        background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed !important;
        color: #23272a;
        font-family: 'Times New Roman', Times, serif !important;
      }
      body.light-mode {
        background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed !important;
        color: #232323;
      }
      body.dark-mode {
        background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
        color: #f5f4ec;
      }
      .main-box {
        border-radius: 22px;
        margin: 48px auto 48px auto;
        padding: 36px 32px 28px 32px;
        max-width: 1200px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'Times New Roman', Times, serif !important;
      }
      .car-info-box {
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 10px 0 8px 0;
        box-shadow: 0 2px 12px #0002;
      }
      .carousel-dark-bg {
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 18px;
        transition: background 0.3s;
        font-family: 'Times New Roman', Times, serif !important;
        background: none !important;
      }
      .descricao-box {
        /* background removido! */
      }
      h1, h2, h3, h4, h5, h6, .text-center {
        color: #232323;
        font-family: 'Times New Roman', Times, serif !important;
      }
      .car-thumbs img {
        border-radius: 8px;
        border: 2px solid #ecebe3;
        transition: border 0.2s;
      }
      .car-thumbs img:hover {
        border: 2px solid #7b5be6;
      }
      /* DARK MODE */
      body.dark-mode .main-box {
        background: rgba(44, 43, 40, 0.97) !important;
        color: #f5f4ec !important;
        box-shadow: 0 2px 16px rgba(0,0,0,0.30);
      }
      body.dark-mode .card {
        background-color: #23272a !important;
        border-color: #444 !important;
        color: #f5f4ec !important;
      }
      body.dark-mode .car-info-box {
        background: #23272a !important;
        color: #f5f4ec !important;
        border-color: #444 !important;
      }
      body.dark-mode .card-title {
        color: #ffe066 !important;
        text-shadow: 0 2px 8px #0008;
      }
      body.dark-mode h1,
      body.dark-mode h2,
      body.dark-mode h3,
      body.dark-mode h4,
      body.dark-mode h5,
      body.dark-mode h6 {
        color: #ffe066 !important;
        text-shadow: 0 2px 8px #0008;
      }
      body.dark-mode .card-body {
        background: #2c2f34 !important;
        color: #f5f4ec !important;
        border-top: 1px solid #444;
      }
      body.dark-mode img {
        filter: brightness(0.88);
      }
      /* Ajustes para exibição dos dados do veículo */
      .car-info-box strong {
        font-size: 1.15rem;
        color: #bfa13a !important;
        display: block;
        margin-bottom: 2px;
        font-family: 'Arial Black', Arial, sans-serif !important;
      }
      .car-info-box div {
        font-size: 1.05rem;
        color: #232323;
        font-weight: 500;
      }
      .car-info-box-center {
        text-align: center;
      }
      body.dark-mode .car-info-box strong {
        color: #ffe066 !important;
      }
      body.dark-mode .car-info-box div {
        color: #f5f4ec !important;
      }
      /* Responsividade */
      @media (max-width: 600px) {
        .car-info-box strong {
          font-size: 1rem;
        }
        .car-info-box div {
          font-size: 0.98rem;
        }
        .carousel-inner img, .car-thumbs img {
          max-width: 100px !important;
          height: 60px !important;
        }
      }
      /* Ajuste para botões */
      .btn-danger, .btn-warning {
        font-size: 1rem;
        padding: 6px 18px;
        border-radius: 8px;
      }
      /* Ajuste para o modal de zoom */
      #zoomModal img {
        border: 3px solid #fff3;
      }
      @media (max-width: 1300px) {
        .main-box {
          padding: 18px 4vw;
          max-width: 99vw;
        }
      }
      .row.g-2 {
        display: flex;
        flex-wrap: wrap;
        gap: 18px 0;
        justify-content: center;
      }
      .row.g-2 > [class^='col-'] {
        padding-left: 12px;
        padding-right: 12px;
        margin-bottom: 0;
      }
      /* THEME-AWARE STYLES */
      body.light-mode .car-info-box {
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 10px 0 8px 0;
        box-shadow: 0 2px 12px #0001;
      }
      body.light-mode .car-info-box strong {
        font-size: 1.15rem;
        color: #bfa13a !important;
        display: block;
        margin-bottom: 2px;
        font-family: 'Arial Black', Arial, sans-serif !important;
      }
      body.light-mode .car-info-box div {
        font-size: 1.05rem;
        color: #232323 !important;
        font-weight: 500;
      }
      body.light-mode .car-info-box-center {
        text-align: center;
      }
      body.light-mode .main-box {
        background: #fffbe6 !important;
        border-radius: 18px;
        box-shadow: 0 2px 16px #0001;
      }
      body.light-mode .carousel-dark-bg {
        background: #e9e9e9;
        border-radius: 14px;
        padding: 18px 0 18px 0;
        margin-bottom: 18px;
      }
      body.light-mode h1,
      body.light-mode h2,
      body.light-mode h3,
      body.light-mode h4,
      body.light-mode h5,
      body.light-mode h6 {
        color: #232323 !important;
        background: unset !important;
        text-shadow: none !important;
      }
      body.light-mode .card-title {
        color: #232323 !important;
      }
      body.light-mode .card-body {
        background: #fff;
        color: #232323;
      }
      body.light-mode .car-thumbs img {
        border: 2px solid #e0e0e0;
      }
      body.light-mode .car-thumbs img:hover {
        border: 2px solid #7b5be6;
      }
      body.light-mode .alert-danger {
        background: #fffbe6 !important;
        color: #b71c1c !important;
        border: 1.5px solid #bfa13a !important;
      }
      /* Remove qualquer background escuro inline das caixas principais no modo claro */
      body.light-mode .car-info-box,
      body.light-mode .carousel-dark-bg,
      body.light-mode .descricao-box {
        background: unset !important;
      }
      /* Unifica o background de todas as car-info-box (inclusive conforto) */
      .car-info-box,
      .car-info-box.conforto-box {
        background: linear-gradient(135deg, #fffbe6 60%, #f7e7b4 100%) !important;
        border: 2.5px solid #bfa13a !important;
        box-shadow: 0 4px 32px #bfa13a22, 0 1.5px 8px #0001;
        border-radius: 18px !important;
        padding: 1.5rem 1.5rem 1.5rem 1.5rem !important;
        margin-top: 12px;
        margin-bottom: 12px;
        position: relative;
        overflow: hidden;
      }
      .car-info-box.conforto-box {
        padding: 2.2rem 2.2rem 2.2rem 2.2rem !important;
      }
      @media (max-width: 700px) {
        .car-info-box,
        .car-info-box.conforto-box {
          padding: 1.1rem 0.7rem !important;
        }
      }
      /* BACKGROUND PADRÃO PARA TÍTULOS E SUBTÍTULOS */
      h1, h2, h3, h4, h5, h6,
      .car-info-box strong,
      .car-info-box .conforto-titulo,
      .car-info-box .conforto-titulo *,
      .car-info-box .conforto-desc strong,
      .car-info-box > div:first-child,
      .card-title,
      .main-box > h1,
      .main-box > h2,
      .main-box > h3,
      .main-box > h4,
      .main-box > h5,
      .main-box > h6 {
        background: linear-gradient(90deg, #fffbe6 60%, #f7e7b4 100%);
        color: #bfa13a !important;
        text-shadow: 0 2px 8px #fff7, 0 1px 0 #bfa13a44;
        font-family: 'Arial Black', Arial, sans-serif !important;
        letter-spacing: 0.5px;
        border-radius: 8px;
        padding: 0.2em 0.7em;
        display: inline-block;
      }
      body.dark-mode h1,
      body.dark-mode h2,
      body.dark-mode h3,
      body.dark-mode h4,
      body.dark-mode h5,
      body.dark-mode h6,
      body.dark-mode .car-info-box strong,
      body.dark_mode .car-info-box .conforto-titulo,
      body.dark-mode .car-info-box .conforto-titulo *,
      body.dark_mode .car_info_box .conforto-desc strong,
      body.dark-mode .car-info-box > div:first-child,
      body.dark-mode .card-title {
        color: #ffe066 !important;
        text-shadow: 0 2px 8px #0008;
        background: unset !important;
      }
      /* Subtítulos e labels dentro das caixas */
      .car-info-box strong,
      .car-info-box > strong,
      .car-info-box > div > strong,
      .car-info-box .conforto-titulo {
        color: #bfa13a !important;
        font-weight: bold !important;
      }
      body.dark-mode .car-info-box strong,
      body.dark-mode .car-info_box > strong,
      body.dark-mode .car-info-box > div > strong,
      body.dark-mode .car-info-box .conforto-titulo {
        color: #ffe066 !important;
      }
      .car-info-box div {
        color: #232323;
      }
      body.dark-mode .car-info-box div {
        color: #f5f4ec !important;
      }
      /* Subtítulos dentro de .car-info-box (ex: "Cadastro:", "Ano:", etc.) */
      .car-info-box > strong,
      .car-info-box > div > strong {
        color: #bfa13a !important;
      }
      body.dark-mode .car-info-box > strong,
      body.dark-mode .car-info-box > div > strong {
        color: #ffe066 !important;
      }
      /* Subtítulos em .conforto-desc */
      .car-info-box .conforto-desc strong {
        color: #bfa13a !important;
      }
      body.dark-mode .car-info-box .conforto-desc strong {
        color: #ffe066 !important;
      }
      /* Consumo e Kit GNV coloridos + contorno preto */
      .consumo-color {
        color: #1e90ff !important;
        font-weight: bold;
        text-shadow:
          -1px -1px 0 #000,
           1px -1px 0 #000,
          -1px  1px 0 #000,
           1px  1px 0 #000;
      }
      .kit-gnv-color {
        color: #28d17c !important;
        font-weight: bold;
        text-shadow:
          -1px -1px 0 #000,
           1px -1px 0 #000,
          -1px  1px 0 #000,
           1px  1px 0 #000;
      }
      .kit-gnv-nao {
        color: #e74c3c !important;
        font-weight: bold;
        text-shadow:
          -1px -1px 0 #000,
           1px -1px 0 #000,
          -1px  1px 0 #000,
           1px  1px 0 #000;
      }
      body.dark-mode .consumo-color,
      body.dark-mode .kit-gnv-color,
      body.dark-mode .kit-gnv-nao {
        text-shadow:
          -1px -1px 0 #000,
           1px -1px 0 #000,
          -1px  1px 0 #000,
           1px  1px 0 #000;
      }
      .descricao-titulo {
        color: #232323 !important;
      }
      body.dark-mode .descricao-titulo {
        color: #fff !important;
      }
    </style>
</head>
<body>
<?php include_once("../headervendas.php"); ?>
<?php
// ALERTAS DE PERFIL E DE DENÚNCIA DENTRO DA MAIN BOX
$denun = isset($car['denun']) ? (int)$car['denun'] : 0;
$anunban = 0;
// Se o anúncio atingiu 10 denúncias ou mais, só o dono pode acessar
if ($denun >= 10 && isset($car['id'])) {
    $isOwner = (isset($_SESSION['logid']) && isset($car['logid']) && $_SESSION['logid'] === $car['logid']);
    $idAnuncio = (int)$car['id'];
    // Torna invisível se ainda estiver visível
    if (!isset($car['visivel']) || $car['visivel'] != 0) {
        $sqlVisivel = "UPDATE veiculos_venda SET visivel = 0 WHERE id = $idAnuncio";
        $con->exec($sqlVisivel);
        $car['visivel'] = 0;
    }
    if (!$isOwner) {
        header('Location: /CRUDTCC/php/Vendas/Vendas.php');
        exit;
    }
}
if (!empty($car['logid'])) {
    $logidAutor = $con->quote($car['logid']);
    $sqlBan = "SELECT anunban FROM clilogin WHERE logid = $logidAutor LIMIT 1";
    $resBan = $con->query($sqlBan);
    if ($resBan && $rowBan = $resBan->fetch(PDO::FETCH_ASSOC)) {
        $anunban = (int)$rowBan['anunban'];
    }
}
if ($anunban === 1) {
    echo '<div class="alert alert-warning text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #bfa13a; box-shadow:0 2px 12px #bfa13a44; background:#fffbe6; color:#bfa13a;">'
        . '🔹 <b>Alerta de Perfil – Histórico de Infração Única:</b><br>Este perfil teve 1 anúncio removido anteriormente por violar as diretrizes da plataforma. Fique atento ao avaliar os produtos oferecidos.'
        . '</div>';
}
// Alerta para 2 a 5 anúncios banidos
if ($anunban >= 2 && $anunban <= 5) {
    echo '<div class="alert alert-warning text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #bfa13a; box-shadow:0 2px 12px #bfa13a44; background:#fffbe6; color:#bfa13a;">'
        . '🔸 <b>Alerta de Perfil – Múltiplas Ocorrências:</b><br>Este perfil já teve diversos anúncios removidos por descumprimento de regras. Recomendamos atenção redobrada antes de qualquer negociação.'
        . '</div>';
}
// Alerta para 6 ou mais anúncios banidos
if ($anunban >= 6) {
    echo '<div class="alert alert-danger text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #b71c1c; box-shadow:0 2px 12px #bfa13a44; background:#fff0f0; color:#b71c1c;">'
        . '🔴 <b>Alerta de Perfil – Reincidência Grave:</b><br>Este perfil foi classificado como não confiável, com vários anúncios removidos por infrações recorrentes. Aconselhamos cautela extrema ao interagir ou realizar compras.'
        . '</div>';
}
if ($denun >= 9) {
    // Nível 3 – Alerta Crítico
    echo '<div class="alert alert-danger text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #b71c1c; box-shadow:0 2px 12px #bfa13a44; background:#fff0f0; color:#b71c1c;">'
        . '🔴 <b> Alerta Crítico:</b><br>Este anúncio possuí mútiplas denúncias por violar nossas diretrizes. Se você já iniciou contato ou negociação, interrompa imediatamente e, se necessário, reporte ao suporte.'
        . '</div>';
} elseif ($denun >= 6) {
    // Nível 2 – Aviso Importante
    echo '<div class="alert alert-warning text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #ff9800; box-shadow:0 2px 12px #bfa13a44; background:#fffbe6; color:#b85c00;">'
        . '🟠 <b>Aviso Importante:</b><br>Este anúncio apresenta possíveis irregularidades e está sob investigação. Por segurança, evite concluir qualquer transação até que a verificação seja finalizada.'
        . '</div>';
} elseif ($denun >= 3) {
    // Nível 1 – Aviso de Atenção
    echo '<div class="alert alert-warning text-center fw-bold" style="font-size:1.15rem; max-width:600px; margin: 24px auto 18px auto; border:2.5px solid #ffe066; box-shadow:0 2px 12px #bfa13a44; background:#fffbe6; color:#bfa13a;">'
        . '🟡 <b>Aviso de Atenção:</b><br>Este anúncio foi sinalizado por outros usuários. Nossa equipe está analisando a situação. Recomendamos cautela ao prosseguir com qualquer negociação.'
        . '</div>';
}
?>
<div class="main-box d-flex flex-column align-items-center">
<?php if (!$car): ?>
    <div class="alert alert-danger text-center">
        <h2>Veículo não encontrado</h2>
        <p>O veículo com o nome informado não está disponível no sistema.</p>
        <a href="/CRUDTCC/Vendas/" class="btn btn-primary mt-3">Voltar para listagem</a>
    </div>
</div>
</body>
</html>
<?php exit; ?>
<?php endif; ?>

<?php if ($car): ?>
    <div class="d-flex flex-column align-items-center w-100" style="margin-bottom: 18px;">
        <h1 class="car-title-main text-center" style="font-family: 'Arial Black', Arial, sans-serif; font-size: 2.7rem; margin-bottom: 0.3em; color:#232323; letter-spacing:1px; background: none; border-radius: 0; padding: 0; text-shadow: none;">
            <?= htmlspecialchars($car['nome']) ?>
        </h1>
        <div class="text-center" style="width:100%; margin-bottom: 0.5em;">
            <span class="car-price-main fs-2 fw-bold valor-carro" style="color:#232323; font-size:2rem; background: none; border-radius: 0; padding: 0; box-shadow: none; display:inline-block;">
            <?php
            if (isset($car['preco']) && $car['preco'] !== '') {
                $preco = trim($car['preco']);
                if (is_numeric($preco)) {
                    echo 'R$ ' . number_format((float)$preco, 2, ',', '.');
                } else {
                    $precoNumerico = preg_replace('/[^\d,\.]/', '', $preco);
                    $posVirg = strrpos($precoNumerico, ',');
                    if ($posVirg !== false) {
                        $precoNumerico = str_replace('.', '', substr($precoNumerico, 0, $posVirg)) . '.' . substr($precoNumerico, $posVirg + 1);
                    } else {
                        $precoNumerico = str_replace('.', '', $precoNumerico);
                    }
                    if (is_numeric($precoNumerico)) {
                        echo 'R$ ' . number_format((float)$precoNumerico, 2, ',', '.');
                    } else {
                        echo htmlspecialchars($preco);
                    }
                }
            } else {
                echo '-';
            }
            ?>
            </span>
        </div>
        <div class="d-flex justify-content-center align-items-center mb-3" style="gap: 10px; background: none; border-radius: 0; padding: 0; box-shadow: none;">
            <?php
            $autorNome = '-';
            $autorFoto = '/CRUDTCC/php/Usuários/Fotos de perfil/default.jpg';
            if (!empty($car['logid'])) {
                $logidAutor = $con->quote($car['logid']);
                $sqlAutor = "SELECT logname, logpfp FROM clilogin WHERE logid = $logidAutor LIMIT 1";
                $resAutor = $con->query($sqlAutor);
                if ($resAutor && $rowAutor = $resAutor->fetch(PDO::FETCH_ASSOC)) {
                    $autorNome = htmlspecialchars($rowAutor['logname']);
                    if (!empty($rowAutor['logpfp'])) {
                        $autorFoto = '/CRUDTCC/php/Usuários/Fotos de perfil/' . htmlspecialchars($rowAutor['logpfp']);
                    }
                }
            }
            ?>
            <img src="<?= $autorFoto ?>" alt="Foto do autor" style="width:32px;height:32px;object-fit:cover;border-radius:50%;border:1.5px solid #bfa13a; background:#fff;">
            <span class="car-owner-main" style="font-size:1rem;font-weight:500;color:#232323; font-family:'Montserrat',Arial,sans-serif;">Por: <?= $autorNome ?></span>
        </div>
    </div>
    <style>
      body.dark-mode .car-title-main,
      body.dark-mode .car-price-main,
      body.dark-mode .car-owner-main {
        color: #fff !important;
        text-shadow: 0 2px 8px #000a;
      }
    </style>
    <div class="info-centralizada">
        <!-- Removido Contato e Data de cadastro duplicados -->
    </div>
    <div class="d-flex flex-column align-items-center" style="width: 100%;">
        <div class="carousel-dark-bg">
            <?php
            // Monta array de imagens válidas (com fallback para default)
            $imgs = [];
            $imgFields = [
                'imagem_principal',
                'imagem_adicional_1',
                'imagem_adicional_2'
            ];
            foreach ($imgFields as $field) {
                if (!empty($car[$field])) {
                    $imgFile = $car[$field];
                    // Caminho físico correto para verificar existência
                    $imgPath = $imageDir . $imgFile;
                    if (file_exists($imgPath)) {
                        // Caminho web correto para exibir a imagem
                        $imgs[] = '/CRUDTCC/php/Vendas/Imagens/' . $imgFile;
                    } else {
                        // Tenta sem o prefixo 'img_'
                        $altName = preg_replace('/^img_/', '', $imgFile);
                        if ($altName !== $imgFile && file_exists($imageDir . $altName)) {
                            $imgs[] = '/CRUDTCC/php/Vendas/Imagens/' . $altName;
                        }
                    }
                }
            }
            if (empty($imgs)) $imgs[] = '/CRUDTCC/images/default.jpg';
            ?>
            <div id="carroCarousel" class="carousel slide" data-bs-ride="carousel" style="max-width: 400px; margin: 0 auto 10px auto;">
                <div class="carousel-inner">
                    <?php foreach ($imgs as $i => $img): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <img src="<?= $img ?>" class="d-block w-100" style="max-width:400px; height:auto; object-fit:contain; background:#f8f9fa; border-radius:8px;" alt="Imagem <?= $i+1 ?> de <?= htmlspecialchars($car['nome']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($imgs) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próxima</span>
                    </button>
                <?php endif; ?>
                <div class="d-flex justify-content-center gap-2 mt-3 car-thumbs">
                    <?php foreach ($imgs as $i => $img): ?>
                        <img src="<?= $img ?>" style="max-width: 130px; width: 100%; height: 80px; object-fit: cover; cursor:pointer;" data-bs-target="#carroCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?> alt="Miniatura <?= $i+1 ?> de <?= htmlspecialchars($car['nome']) ?>">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="container mt-4">
            <div class="row justify-content-center w-100" style="margin:0;">
                <div class="col-12 mb-3">
                    <div class="car-info-box car-info-box-center w-100" style="width:100%;">
                        <strong class="descricao-titulo texto-contorno-branco" style="font-size:2rem;display:block;">Descrição:</strong>
                        <div class="descricao-user texto-contorno-branco" style="font-size: 1.1rem; text-align:left;">
                            <?= $car['descricao'] !== '' ? nl2br(htmlspecialchars($car['descricao'])) : '' ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-2">
                <!-- Ano (esquerda) -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Ano:</strong>
                        <div id="ano-ipva-info" class="texto-preto-campo texto-contorno-branco">
                            <?php
                            $ano = isset($car['ano']) ? (int)$car['ano'] : 0;
                            echo htmlspecialchars($ano ? $ano : '-');
                            ?>
                            <span id="ipva-status" style="font-size:0.95em; font-weight:600;"></span>
                        </div>
                    </div>
                </div>
                <!-- Contato (direita, ao lado de Ano) -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Contato:</strong>
                        <div class="texto-preto-campo texto-contorno-branco"><?= nl2br(htmlspecialchars($car['contato'] ?? '')) ?></div>
                    </div>
                </div>
                <!-- Motor e Swamp de Motor -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Motor:</strong>
                        <div class="texto-preto-campo texto-contorno-branco">
                            <?php
                            $motor = $car['Motor'] ?? '-';
                            $swampMotor = $car['SwampMotor'] ?? 0;
                            $motorOriginal = $car['MotorAntigo'] ?? '';
                            if ($swampMotor) {
                                echo "Swamp de Motor: Sim<br>";
                                echo "Motor Original: " . htmlspecialchars($motorOriginal ?: '-') . "<br>";
                                echo "Motor Atual: " . htmlspecialchars($motor ?: '-');
                            } else {
                                echo htmlspecialchars($motor ?: '-');
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Transmissão e Swamp de Transmissão -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Transmissão:</strong>
                        <div class="texto-preto-campo texto-contorno-branco">
                            <?php
                            $transmissao = $car['Transmissao'] ?? '-';
                            $swampTransmissao = $car['SwampTransmissao'] ?? 0;
                            $transmissaoOriginal = $car['TransmissaoAntiga'] ?? '';
                            if ($swampTransmissao == 1) {
                                echo "Swamp de Transmissão: Sim<br>";
                                echo "Transmissão Original: " . htmlspecialchars($transmissaoOriginal ?: '-') . "<br>";
                                echo "Transmissão Atual: " . htmlspecialchars($transmissao ?: '-');
                            } else {
                                echo htmlspecialchars($transmissao ?: '-');
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Tipo de Transmissão -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Tipo de Transmissão:</strong>
                        <div class="texto-preto-campo texto-contorno-branco">
                            <?php
                            $swampTransmissao = $car['SwampTransmissao'] ?? 0;
                            $tipoTransmissaoOriginal = $car['TipoTransmissaoOriginal'] ?? '-';
                            $tipoTransmissaoAtual = $car['TipoTransmissao'] ?? '-';
                            $modeloTransmissaoOriginal = $car['TransmissaoAntiga'] ?? '-';
                            $modeloTransmissaoAtual = $car['Transmissao'] ?? '-';
                            echo 'Swamp de Transmissão: ' . ($swampTransmissao == 1 ? 'Sim' : 'Não') . '<br>';
                            echo 'Tipo Original: ' . htmlspecialchars($tipoTransmissaoOriginal ?: '-') . '<br>';
                            echo 'Tipo Atual: ' . htmlspecialchars($tipoTransmissaoAtual ?: '-') . '<br>';
                            echo 'Modelo Original: ' . htmlspecialchars($modeloTransmissaoOriginal ?: '-') . '<br>';
                            echo 'Modelo Atual: ' . htmlspecialchars($modeloTransmissaoAtual ?: '-');
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Marca -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Marca:</strong>
                        <div class="texto-preto-campo texto-contorno-branco"><?= htmlspecialchars($car['marca'] ?? '-') ?></div>
                    </div>
                </div>
                <!-- Quilometragem -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Quilometragem:</strong>
                        <div class="texto-preto-campo texto-contorno-branco"><?= htmlspecialchars($car['km'] ?? '-') ?> km</div>
                    </div>
                </div>
                <!-- Categorias -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Categorias:</strong>
                        <div class="texto-preto-campo texto-contorno-branco" style="font-size: 1.1rem;">
                            <?php
                            $cats = array();
                            foreach (["categoria_id", "categoria_idop1", "categoria_idop2"] as $catField) {
                                if (
                                    isset($car[$catField]) &&
                                    $car[$catField] !== null &&
                                    $car[$catField] !== '' &&
                                    $car[$catField] != 0 &&
                                    isset($nomesCategorias[(int)$car[$catField]])
                                ) {
                                    $cats[] = $nomesCategorias[(int)$car[$catField]];
                                }
                            }
                            echo $cats ? htmlspecialchars(implode(', ', $cats)) : '-';
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Direção -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Direção:</strong>
                        <div class="texto-preto-campo texto-contorno-branco"><?= htmlspecialchars($car['CarDire'] ?? '-') ?></div>
                    </div>
                </div>
                <!-- Combustível + Consumo + Kit GNV lado a lado -->
                <div class="col-12 col-md-6">
                    <div class="car-info-box conforto-box mb-2 text-center py-2">
                        <strong class="texto-contorno-branco">Combustível:</strong>
                        <div>
                            <?php
                            $combustivel = '-';
                            $combustivelClass = '';
                            if (isset($car['CarCombus'])) {
                                if ($car['CarCombus'] === 'Outro' && !empty($car['CarCombusOutro'])) {
                                    $combustivel = htmlspecialchars($car['CarCombusOutro']);
                                } else {
                                    $combustivel = htmlspecialchars($car['CarCombus']);
                                }
                                $combustivelLower = mb_strtolower($combustivel);
                                if (strpos($combustivelLower, 'gasolina') !== false) {
                                    $combustivelClass = 'combustivel-gasolina';
                                } elseif (strpos($combustivelLower, 'etanol') !== false) {
                                    $combustivelClass = 'combustivel-etanol';
                                } elseif (strpos($combustivelLower, 'diesel') !== false) {
                                    $combustivelClass = 'combustivel-diesel';
                                } elseif (strpos($combustivelLower, 'flex') !== false) {
                                    $combustivelClass = 'combustivel-flex';
                                } elseif (strpos($combustivelLower, 'elétrico') !== false || strpos($combustivelLower, 'eletrico') !== false) {
                                    $combustivelClass = 'combustivel-eletrico';
                                } elseif (strpos($combustivelLower, 'híbrido') !== false || strpos($combustivelLower, 'hibrido') !== false) {
                                    $combustivelClass = 'combustivel-hibrido';
                                } elseif (strpos($combustivelLower, 'gnv') !== false) {
                                    $combustivelClass = 'combustivel-gnv';
                                }
                            }
                            echo '<span class="' . $combustivelClass . ' texto-contorno-branco" style="font-size:1.1em;">' . $combustivel . '</span>';
                            // Consumo principal
                            $consumo = (isset($car['Consumo']) && $car['Consumo'] !== '') ? htmlspecialchars($car['Consumo']) : '';
                            $consumo2 = (isset($car['Consumo2']) && $car['Consumo2'] !== '') ? htmlspecialchars($car['Consumo2']) : '';
                            $consumo3 = (isset($car['Consumo3']) && $car['Consumo3'] !== '') ? htmlspecialchars($car['Consumo3']) : '';
                            $labelConsumo = 'Consumo';
                            $labelConsumo2 = 'Consumo Etanol (km/l)';
                            $labelConsumo3 = 'Autonomia (KM/Bateria)';
                            // Flex ou Híbrido: Consumo principal é Gasolina
                            if (isset($car['CarCombus']) && ($car['CarCombus'] === 'Flex' || $car['CarCombus'] === 'Híbrido')) {
                                $labelConsumo = 'Consumo Gasolina (km/l)';
                            }
                            // Exibe consumo principal
                            if ($consumo) {
                                echo ' <span style="color:#aaa; font-size:0.98em;">|</span> <span style="font-weight:600;">' . $labelConsumo . ': ' . $consumo . ' km/l</span>';
                            }
                            // Exibe Consumo2 se Flex ou Híbrido
                            if ($consumo2 && isset($car['CarCombus']) && ($car['CarCombus'] === 'Flex' || $car['CarCombus'] === 'Híbrido')) {
                                echo ' <span style="color:#aaa; font-size:0.98em;">|</span> <span style="font-weight:600;">' . $labelConsumo2 . ': ' . $consumo2 . ' km/l</span>';
                            }
                            // Exibe Consumo3 se Híbrido ou Elétrico
                            if ($consumo3 && isset($car['CarCombus']) && ($car['CarCombus'] === 'Híbrido' || $car['CarCombus'] === 'Elétrico')) {
                                echo ' <span style="color:#aaa; font-size:0.98em;">|</span> <span style="font-weight:600;">' . $labelConsumo3 . ': ' . $consumo3 . ' km</span>';
                            }
                            // Kit GNV ao lado
                            if (isset($car['gnv'])) {
                                echo ' <span style="color:#aaa; font-size:0.98em;">|</span> <span style="font-weight:600;">Kit GNV: ';
                                echo ($car['gnv'] == 1) ? '<span style="color:#28d17c;font-weight:bold;">Sim' : '<span style="color:#e74c3c;font-weight:bold;">Não';
                                echo '</span>';
                                // Consumo GNV
                                if ($car['gnv'] == 1) {
                                    echo ' <span style="color:#aaa; font-size:0.98em;">|</span> <span style="color:#ffe066; font-weight:bold;">';
                                    echo 'Consumo GNV: ' . (isset($car['ConsumoGNV']) && $car['ConsumoGNV'] !== '' ? htmlspecialchars($car['ConsumoGNV']) . ' km/m³' : '-');
                                    echo '</span>';
                                }
                                // Fim do bloco Kit GNV
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Conforto (linha inteira, centralizado) -->
                <div class="col-12 mb-3">
                    <div class="car-info-box mb-2 text-center py-2 conforto-box" style="padding-bottom: 1.5rem; max-width:700px; margin:0 auto;">
                        <div class="conforto-titulo texto-contorno-branco">Conforto:</div>
                        <?php
                        $conforto = $car['CarConfort'] ?? '';
                        $confortoNiveis = array(
                            1 => array(
                                'titulo' => 'Básico',
                                'desc' => 'Carros com bancos simples, pouca ou nenhuma tecnologia de suspensão avançada, ruído interno perceptível e poucos recursos de conforto, como ar-condicionado básico e acabamento simples.'
                            ),
                            2 => array(
                                'titulo' => 'Intermediário',
                                'desc' => 'Veículos com bancos um pouco mais ergonômicos, ar-condicionado eficiente, suspensão melhor ajustada e menos ruído interno. Podem incluir alguns extras como direção elétrica ou regulagem de altura do banco.'
                            ),
                            3 => array(
                                'titulo' => 'Confortável',
                                'desc' => 'Modelos com bancos mais anatômicos e materiais melhores, suspensão bem ajustada para reduzir impactos, isolamento acústico aprimorado e recursos como ar-condicionado digital, piloto automático e mais ajustes elétricos nos bancos.'
                            ),
                            4 => array(
                                'titulo' => 'Luxuoso',
                                'desc' => 'Carros com bancos de couro, climatização individual, excelente isolamento acústico, suspensão adaptativa, sistema multimídia sofisticado e recursos como bancos aquecidos, ventilados e com ajustes automáticos.'
                            ),
                            5 => array(
                                'titulo' => 'Premium/Executivo',
                                'desc' => 'O nível máximo de conforto, encontrado em sedãs de alto padrão e SUVs de luxo. Aqui há bancos com função de massagem, suspensão a ar, silêncio absoluto na cabine, acabamento refinado, materiais nobres e tecnologias avançadas para o bem-estar dos passageiros.'
                            )
                        );
                        if (isset($confortoNiveis[$conforto])): ?>
                            <div class="conforto-titulo texto-contorno-branco" style="font-size:1.25rem; font-weight:bold; margin-top:12px;">
                                <?= $confortoNiveis[$conforto]['titulo'] ?> <span style="font-size:1.1em;">&#9733;</span>
                            </div>
                            <div class="conforto-desc texto-preto-campo texto-contorno-branco" style="margin-top:8px;">
                                <?= $confortoNiveis[$conforto]['desc'] ?>
                            </div>
                        <?php else: ?>
                            <div class="conforto-desc texto-preto-campo texto-contorno-branco" style="margin-top:8px;">-</div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Cadastro (fora do grid, centralizado, logo abaixo de conforto) -->
                <div class="row justify-content-center w-100 mt-2" style="margin:0;">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="car-info-box mb-2 text-center py-2" style="max-width: 400px; margin: 0 auto;">
                            <strong style="font-size:1.22rem;" class="texto-contorno-branco">Cadastro:</strong>
                            <div class="texto-preto-campo texto-contorno-branco" style="font-size: 1.22rem;">
                                <?= !empty($car['data_cadastro']) ? htmlspecialchars(date('d/m/Y H:i:s', strtotime($car['data_cadastro']))) : '-' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // Exibe o formulário de denúncia apenas se o usuário NÃO for o dono e NÃO for admin
    $adminUsers = ['user_6838d6be1afc45.64015583', 'user_6838dec504f9e3.45675952'];
    $isOwner = (isset($_SESSION['logid']) && isset($car['logid']) && $_SESSION['logid'] === $car['logid']);
    $isAdmin = (isset($_SESSION['logid']) && in_array($_SESSION['logid'], $adminUsers));
    if (!$isOwner && !$isAdmin): ?>
        <div class="w-100 d-flex justify-content-center align-items-center mt-4 mb-2">
            <button id="btnMostrarDenuncia" class="btn btn-outline-danger" style="font-weight:bold;min-width:140px;height:40px;" type="button">Denunciar</button>
            <form id="formDenuncia" method="post" action="/CRUDTCC/php/Vendas/categorias vendas/denunciar_veiculo.php" onsubmit="return confirm('Tem certeza que deseja denunciar este veículo?');" style="display:none;max-width:400px;margin-left:16px;">
                <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($car['id']) ?>">
                <div class="mb-2">
                    <textarea name="motivo" class="form-control" rows="2" maxlength="300" required placeholder="Descreva o motivo da denúncia (obrigatório)"></textarea>
                </div>
                <button type="submit" class="btn btn-outline-danger" style="font-weight:bold;min-width:140px;height:40px;">Enviar denúncia</button>
                <button type="button" class="btn btn-secondary ms-2" onclick="document.getElementById('formDenuncia').style.display='none';document.getElementById('btnMostrarDenuncia').style.display='inline-block';">Cancelar</button>
            </form>
        </div>
    <?php endif; ?>
    <?php if ($isOwner): ?>
        <div class="w-100 d-flex justify-content-center align-items-center mt-4 mb-2 gap-3">
            <a href="/CRUDTCC/php/Vendas/categorias vendas/EditarVeiculo.php?id=<?= urlencode($car['id']) ?>" class="btn btn-warning" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Editar Anúncio</a>
            <form method="post" action="/CRUDTCC/php/Vendas/categorias vendas/delete_veiculo.php" onsubmit="return confirm('Tem certeza que deseja deletar este anúncio? Esta ação não pode ser desfeita.');" style="display:inline;">
                <input type="hidden" name="id" value="<?= htmlspecialchars($car['id']) ?>">
                <button type="submit" class="btn btn-danger" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Deletar Anúncio</button>
            </form>
            <?php if (isset($car['pausa']) && $car['pausa'] == 1): ?>
            <form method="post" action="" style="display:inline;">
                <input type="hidden" name="pausar_veiculo_id" value="<?= htmlspecialchars($car['id']) ?>">
                <button type="submit" class="btn btn-secondary" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Pausar Anúncio</button>
            </form>
            <?php elseif (isset($car['pausa']) && $car['pausa'] == 0): ?>
            <form method="post" action="" style="display:inline;">
                <input type="hidden" name="retomar_veiculo_id" value="<?= htmlspecialchars($car['id']) ?>">
                <button type="submit" class="btn btn-success" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Retomar Anúncio</button>
            </form>
            <?php endif; ?>
        </div>
    <?php elseif ($isAdmin): ?>
        <div class="w-100 d-flex justify-content-center align-items-center mt-4 mb-2 gap-3">
            <form method="post" action="/CRUDTCC/php/Vendas/categorias vendas/delete_veiculo.php" onsubmit="return confirm('Tem certeza que deseja deletar este anúncio? Esta ação não pode ser desfeita. (ADM)');" style="display:inline;">
                <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($car['id']) ?>">
                <button type="submit" class="btn btn-danger" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Deletar Anúncio</button>
            </form>
            <?php if (isset($car['visivel']) && $car['visivel'] != 0): ?>
            <form method="post" action="" style="display:inline;">
                <input type="hidden" name="ocultar_veiculo_id" value="<?= htmlspecialchars($car['id']) ?>">
                <button type="submit" class="btn btn-secondary" style="font-weight:bold;min-width:160px;height:44px;color:#232323 !important;display:flex;align-items:center;justify-content:center;">Ocultar Anúncio</button>
            </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>

<div id="zoomModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;backdrop-filter:blur(2px);">
    <img id="zoomImg" src="" style="max-width:90vw;max-height:90vh;border-radius:12px;box-shadow:0 4px 32px #000a;">
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Zoom de imagem
    const zoomModal = document.getElementById('zoomModal');
    const zoomImg = document.getElementById('zoomImg');
    document.querySelectorAll('.carousel-inner img, .car-thumbs img').forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function(e) {
            zoomImg.src = this.src;
            zoomModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });
    zoomModal.addEventListener('click', function() {
        this.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    // Atualiza status de IPVA baseado no ano do carro e ano atual do dispositivo
    (function() {
        var anoCarro = <?php echo isset($car['ano']) ? (int)$car['ano'] : 0; ?>;
        var ipvaStatus = document.getElementById('ipva-status');
        if (!anoCarro || !ipvaStatus) return;
        var anoAtual = new Date().getFullYear();
        var diferenca = anoAtual - anoCarro;
        if (diferenca >= 20) {
            ipvaStatus.innerHTML = ' <span style="color:#28a745;font-weight:bold;">(Isento de IPVA)</span>';
        } else {
            var faltam = 20 - diferenca;
            ipvaStatus.textContent = ' (Paga IPVA - Faltam ' + faltam + ' ano' + (faltam > 1 ? 's' : '') + ' para isenção)';
            ipvaStatus.style.color = '#dc3545';
        }
    })();

    // Mostra/esconde formulário de denúncia
    const btnMostrarDenuncia = document.getElementById('btnMostrarDenuncia');
    const formDenuncia = document.getElementById('formDenuncia');
    if (btnMostrarDenuncia && formDenuncia) {
        btnMostrarDenuncia.onclick = function() {
            formDenuncia.style.display = 'block';
            btnMostrarDenuncia.style.display = 'none';
        };
    }
</script>

<?php
// Processa ocultação do veículo se ADM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocultar_veiculo_id']) && $isAdmin) {
    $ocultarId = (int)$_POST['ocultar_veiculo_id'];
    $sqlOcultar = "UPDATE veiculos_venda SET visivel = 0 WHERE id = :id";
    $stmtOcultar = $con->prepare($sqlOcultar);
    $stmtOcultar->bindValue(':id', $ocultarId, PDO::PARAM_INT);
    $stmtOcultar->execute();
    if (isset($car['id']) && $car['id'] == $ocultarId) {
        $car['visivel'] = 0;
    }
    // Envio de e-mail para o dono do anúncio
    set_time_limit(0);
    if (!empty($car['logid'])) {
        $logidAutor = $con->quote($car['logid']);
        $sqlEmail = "SELECT logemail, logname FROM clilogin WHERE logid = $logidAutor LIMIT 1";
        $resEmail = $con->query($sqlEmail);
        if ($resEmail && $rowEmail = $resEmail->fetch(PDO::FETCH_ASSOC)) {
            $destinatario = $rowEmail['logemail'];
            $nomeDono = $rowEmail['logname'];
            $nomeAnuncio = isset($car['nome']) ? $car['nome'] : '';
            if (filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/PHPMailer.php';
                require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/SMTP.php';
                require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/Exception.php';
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    // Configurações do servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Altere para o SMTP do seu provedor
                    $mail->SMTPAuth = true;
                    $mail->Username = 'seu_email@gmail.com'; // Altere para o e-mail do sistema
                    $mail->Password = 'sua_senha'; // Altere para a senha do e-mail
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    // Remetente e destinatário
                    $mail->setFrom('seu_email@gmail.com', 'Equipe Hydra Motors');
                    $mail->addAddress($destinatario, $nomeDono);
                    // Conteúdo do e-mail
                    $mail->isHTML(true);
                    $mail->Subject = '📢 Seu anúncio foi pausado para revisão: ' . $nomeAnuncio;
                    $corpo =
                        '📢 Seu anúncio <b>' . htmlspecialchars($nomeAnuncio) . '</b> foi pausado para revisão<br><br>' .
                        'Olá! Tudo bem?<br><br>' .
                        'Seu anúncio <b>' . htmlspecialchars($nomeAnuncio) . '</b> foi temporariamente pausado e está passando por uma revisão por parte da nossa equipe administrativa. Essa medida é apenas para garantir que todas as informações estejam de acordo com nossas diretrizes e políticas.<br><br>' .
                        '🔍 Assim que a análise for concluída, entraremos em contato com a atualização — seja para reativação ou com orientações sobre possíveis ajustes.<br><br>' .
                        'Agradecemos pela sua compreensão e parceria! Se tiver alguma dúvida ou precisar de suporte, estamos à disposição.';
                    $mail->Body = $corpo;
                    $mail->AltBody =
                        '📢 Seu anúncio "' . $nomeAnuncio . '" foi pausado para revisão\n\n' .
                        'Olá! Tudo bem?\n\n' .
                        'Seu anúncio "' . $nomeAnuncio . '" foi temporariamente pausado e está passando por uma revisão por parte da nossa equipe administrativa. Essa medida é apenas para garantir que todas as informações estejam de acordo com nossas diretrizes e políticas.\n\n' .
                        '🔍 Assim que a análise for concluída, entraremos em contato com a atualização — seja para reativação ou com orientações sobre possíveis ajustes.\n\n' .
                        'Agradecemos pela sua compreensão e parceria! Se tiver alguma dúvida ou precisar de suporte, estamos à disposição.';
                    $mail->send();
                } catch (Exception $e) {
                    // Você pode logar o erro se desejar: $mail->ErrorInfo
                }
            }
        }
    }
    echo '<div class="alert alert-info text-center fw-bold" style="font-size:1.1rem; max-width:600px; margin: 24px auto 18px auto;">Anúncio ocultado com sucesso.</div>';
}
// Processa pausa do veículo se dono
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pausar_veiculo_id']) && $isOwner) {
    $pausarId = (int)$_POST['pausar_veiculo_id'];
    $sqlPausar = "UPDATE veiculos_venda SET pausa = 0 WHERE id = :id";
    $stmtPausar = $con->prepare($sqlPausar);
    $stmtPausar->bindValue(':id', $pausarId, PDO::PARAM_INT);
    $stmtPausar->execute();
    if (isset($car['id']) && $car['id'] == $pausarId) {
        $car['pausa'] = 0;
    }
    echo '<div class="alert alert-info text-center fw-bold" style="font-size:1.1rem; max-width:600px; margin: 24px auto 18px auto;">Anúncio pausado com sucesso.</div>';
}
// Processa retomada do veículo se dono
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retomar_veiculo_id']) && $isOwner) {
    $retomarId = (int)$_POST['retomar_veiculo_id'];
    $sqlRetomar = "UPDATE veiculos_venda SET pausa = 1 WHERE id = :id";
    $stmtRetomar = $con->prepare($sqlRetomar);
    $stmtRetomar->bindValue(':id', $retomarId, PDO::PARAM_INT);
    $stmtRetomar->execute();
    if (isset($car['id']) && $car['id'] == $retomarId) {
        $car['pausa'] = 1;
    }
    echo '<div class="alert alert-info text-center fw-bold" style="font-size:1.1rem; max-width:600px; margin: 24px auto 18px auto;">Anúncio retomado com sucesso.</div>';
}
?>
