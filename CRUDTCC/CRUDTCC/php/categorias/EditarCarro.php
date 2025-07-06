<?php
include __DIR__ . '/../../config.php';
include __DIR__ . '/../../mysqlexecuta.php';

$con = conectar();
$imageDir = '/images/Carimg/';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recebe o nome do carro via GET ou POST
$carName = isset($_GET['nome']) ? trim($_GET['nome']) : (isset($_POST['nome']) ? trim($_POST['nome']) : '');

if (!$carName) {
    echo '<div class="container mt-5"><h2>Carro não encontrado.</h2></div>';
    exit;
}

// Busca os dados do carro
$stmtCar = mysqlexecuta($con, "SELECT * FROM car WHERE carnome = '" . str_replace("'", "''", $carName) . "' LIMIT 1");
$car = $stmtCar->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    echo '<div class="container mt-5"><h2>Carro não encontrado.</h2></div>';
    exit;
}

// Verifica permissão: só o dono ou quem tem Adm=1 pode editar
$temPermissao = false;
if (isset($_SESSION['logid'])) {
    // Verifica se é o dono
    if ($_SESSION['logid'] == $car['logid']) {
        $temPermissao = true;
    } else {
        // Verifica se Adm=1 ou Rev=1 na tabela clilogin
        $logid = str_replace("'", "''", $_SESSION['logid']);
        $resAdm = mysqlexecuta($con, "SELECT Adm, Rev FROM clilogin WHERE logid = '" . $logid . "' LIMIT 1");
        if ($resAdm && $rowAdm = $resAdm->fetch(PDO::FETCH_ASSOC)) {
            if ((isset($rowAdm['Adm']) && $rowAdm['Adm'] == 1) || (isset($rowAdm['Rev']) && $rowAdm['Rev'] == 1)) {
                $temPermissao = true;
            }
        }
    }
}
if (!$temPermissao) {
    echo '<div class="container mt-5"><h2>Você não tem permissão para editar este carro.</h2></div>';
    exit;
}

// Se o formulário foi enviado, processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carnome'])) {
    $carnome = str_replace("'", "''", $_POST['carnome']);
    $descricao = str_replace("'", "''", $_POST['descricao']);
    $carmarca = str_replace("'", "''", $_POST['carmarca']);
    $catid = intval($_POST['catid']);
    $catidop1 = intval($_POST['catidop1'] ?? 0);
    $catidop2 = intval($_POST['catidop2'] ?? 0);
    // Ajusta para NULL se for 0
    $catidop1_sql = $catidop1 === 0 ? 'NULL' : $catidop1;
    $catidop2_sql = $catidop2 === 0 ? 'NULL' : $catidop2;
    $carfipe = str_replace("'", "''", $_POST['carfipe']);
    $pdm = str_replace("'", "''", $_POST['pdm']);
    $carhistory = str_replace("'", "''", $_POST['carhistory']);
    $cargen = str_replace("'", "''", $_POST['cargen']);
    $genname = str_replace("'", "''", $_POST['genname']);
    $consumo = str_replace("'", "''", $_POST['Consumo']);
    // Novos campos
    $car_dire = str_replace("'", "''", $_POST['CarDire'] ?? '');
    $car_confort = intval($_POST['CarConfort'] ?? 1);
    $car_sport = intval($_POST['CarSport'] ?? 1);
    $car_combus = str_replace("'", "''", $_POST['CarCombus'] ?? '');
    $consumo2 = str_replace("'", "''", $_POST['Consumo2'] ?? '');
    $consumo3 = str_replace("'", "''", $_POST['Consumo3'] ?? '');
    $consumo4 = str_replace("'", "''", $_POST['Consumo4'] ?? ''); // GNV
    $consumo5 = str_replace("'", "''", $_POST['Consumo5'] ?? ''); // Diesel
    $carfabini = isset($_POST['CarFabIn']) ? intval($_POST['CarFabIn']) : null;
    $carfabfim = isset($_POST['CarFabFim']) ? intval($_POST['CarFabFim']) : null;

    // Corrige o nome do carro para o WHERE
    $carNameEscaped = str_replace("'", "''", $carName);

    $sqlUpdate = "UPDATE car SET carnome='$carnome', descricao='$descricao', carmarca='$carmarca', catid=$catid, catidop1=$catidop1_sql, catidop2=$catidop2_sql, carfipe='$carfipe', pdm='$pdm', carhistory='$carhistory', cargen='$cargen', genname='$genname', Consumo='$consumo', CarDire='$car_dire', CarConfort=$car_confort, CarSport=$car_sport, CarCombus='$car_combus', Consumo2='$consumo2', Consumo3='$consumo3', Consumo4='$consumo4', Consumo5='$consumo5', CarFabIni=$carfabini, CarFabFim=$carfabfim WHERE carnome='$carNameEscaped'";
    $resUpdate = mysqlexecuta($con, $sqlUpdate);
    if ($resUpdate) {
        ?>
        <style>
          body {
            background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed;
            color: #23272a;
            position: relative;
          }
          .success-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            padding: 40px 0 0 0;
            animation: fadeIn 0.7s;
          }
          .success-box {
            background: rgba(167, 211, 214, 0.97);
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.13);
            padding: 36px 32px 28px 32px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            margin: 0 auto;
            border: 2px solid #7b5be6;
          }
          .success-box h2 {
            color: #002E33;
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: 1px;
          }
          .success-box p {
            font-size: 1.15rem;
            color: #222;
            margin-bottom: 18px;
          }
          .success-box a {
            display: inline-block;
            background: #7b5be6;
            color: #fff !important;
            font-weight: 600;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 1.1rem;
            text-decoration: none;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #0002;
          }
          .success-box a:hover {
            background: #002E33;
            color: #ffe066 !important;
            box-shadow: 0 4px 16px #7b5be6;
          }
          @media (max-width: 600px) {
            .success-box { padding: 18px 6vw; }
          }
          @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
          }
          body.dark-mode .success-box {
            background: #222c2b;
            color: #fff;
            border: 2px solid #7b5be6;
          }
          body.dark-mode .success-box h2 {
            color: #ffe066;
          }
          body.dark-mode .success-box p {
            color: #fff;
          }
          body.dark-mode .success-box a {
            background: #7b5be6;
            color: #fff !important;
          }
          body.dark-mode .success-box a:hover {
            background: #ffe066;
            color: #222 !important;
          }
        </style>
        <div class="success-container">
          <div class="success-box">
            <h2>Sucesso!</h2>
            <p>Carro atualizado com sucesso!</p>
            <a href="Carro.php?nome=<?= urlencode($carnome) ?>">Ver Veículo</a>
          </div>
        </div>
        <?php
        exit;
    } else {
        echo '<div class="container mt-5"><div class="alert alert-danger">Erro ao atualizar carro.</div></div>';
    }
}

// Array de nomes das categorias conforme os IDs
$nomesCategorias = [
  1 => 'Antiguidade',
  2 => 'Sedã',
  3 => 'SUV',
  4 => 'Hatch',
  5 => 'UTE/Pickup',
  6 => 'Elétrico',
  7 => 'Coupé',
  8 => 'Híbrido'
];
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Carro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/style.css" rel="stylesheet">
  <link href="/css/header.css" rel="stylesheet">
  <style>
    body {
      background: url('/CRUDTCC/images/Background.png') center center/cover no-repeat fixed;
      color: #23272a;
      position: relative;
    }
    .main-box {
      background: rgba(255, 255, 255, 0.97);
      border-radius: 18px;
      box-shadow: 0 4px 32px rgba(0,0,0,0.13);
      padding: 36px 32px 28px 32px;
      max-width: 600px;
      margin: 0 auto;
      border: 2px solid #7b5be6;
    }
    .main-box h2 {
      color: #002E33;
      font-size: 2.1rem;
      font-weight: 700;
      margin-bottom: 24px;
      text-align: center;
    }
    .form-label {
      font-weight: 500;
      color: #333;
    }
    .btn-primary {
      background-color: #7b5be6;
      border-color: #7b5be6;
      color: #fff;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 8px;
      transition: background 0.3s, transform 0.3s;
    }
    .btn-primary:hover {
      background-color: #6a4db3;
      transform: translateY(-2px);
    }
    .btn-secondary {
      background-color: #f0f0f0;
      border-color: #ddd;
      color: #333;
      font-weight: 500;
      padding: 10px 20px;
      border-radius: 8px;
      transition: background 0.3s, transform 0.3s;
    }
    .btn-secondary:hover {
      background-color: #e0e0e0;
      transform: translateY(-2px);
    }
    @media (max-width: 600px) {
      .main-box { padding: 24px 16px; }
      .main-box h2 { font-size: 1.8rem; }
      .btn-primary, .btn-secondary {
        width: 100%;
        padding: 12px 0;
      }
    }
    /* DARK MODE */
    body.dark-mode {
      background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
      height: auto;
      color: #fff;
      position: relative;
    }
    body.dark-mode .main-box {
      background: #2C2B28ee !important;
      color: #fff;
      box-shadow: 0 2px 16px rgba(0,0,0,0.30);
    }
    body.dark-mode label,
    body.dark-mode .form-label {
      color: #e0e0e0 !important;
      font-weight: 500;
    }
    body.dark-mode h2,
    body.dark-mode .text-center {
      color: #ffe066 !important;
      text-shadow: 0 2px 8px #0008;
      font-weight: 700;
    }
    body.dark-mode input,
    body.dark-mode select,
    body.dark-mode textarea {
      background: #222 !important;
      color: #fff !important;
      border-color: #444 !important;
    }
    body.dark-mode .btn-primary,
    body.dark-mode .btn-secondary,
    body.dark-mode .btn {
      background-color: #002E33 !important;
      color: #fff !important;
      border-color: #002E33 !important;
    }
    body.dark-mode .btn-primary:hover,
    body.dark-mode .btn-secondary:hover,
    body.dark-mode .btn:hover {
      background-color: #00444b !important;
      color: #fff !important;
    }
    .conforto-label, .exportividade-label {
      color: #b2b2b2; /* prata escuro para modo claro */
      font-weight: bold;
    }
    body.dark-mode .conforto-label, body.dark-mode .exportividade-label {
      color: #ffe066 !important; /* dourado para modo escuro */
    }
    .conforto-desc-label, .exportividade-desc-label {
      color: #b2b2b2; /* prata escuro para modo claro */
      font-weight: 500;
    }
    body.dark-mode .conforto-desc-label, body.dark-mode .exportividade-desc-label {
      color: #ffe066 !important; /* dourado para modo escuro */
    }
  </style>
</head>
<body>
<?php include_once(__DIR__ . '/../../header.php'); ?>
<div class="container mt-5 mb-5">
  <div class="main-box p-4 shadow-lg" style="border-radius:22px;max-width:700px;margin:0 auto;box-shadow:0 4px 32px rgba(123,91,230,0.10);background:rgba(255,255,255,0.98);">
    <h2 class="mb-4 text-center" style="font-weight:800;letter-spacing:1px;color:#7b5be6;text-shadow:0 2px 8px #e0e0ff;">Editar Carro</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="nome" value="<?= htmlspecialchars($car['carnome']) ?>">
      <!-- Seção: Dados Básicos -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nome</label>
          <input type="text" name="carnome" class="form-control" value="<?= htmlspecialchars($car['carnome']) ?>" required placeholder="Ex: Corolla Altis">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Marca</label>
          <input type="text" name="carmarca" class="form-control" value="<?= htmlspecialchars($car['carmarca']) ?>" required placeholder="Ex: Toyota">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3" required placeholder="Breve descrição do carro"><?= htmlspecialchars($car['descricao']) ?></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ano Inicial (Fabricação)</label>
          <input type="number" name="CarFabIn" class="form-control" value="<?= isset($car['CarFabIni']) ? htmlspecialchars($car['CarFabIni']) : '' ?>" placeholder="Ex: 2015">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Ano Final (Fabricação)</label>
          <input type="number" name="CarFabFim" class="form-control" value="<?= isset($car['CarFabFim']) ? htmlspecialchars($car['CarFabFim']) : '' ?>" placeholder="Ex: 2020">
        </div>
      </div>
      <!-- Seção: Imagens -->
      <div class="mb-4 mt-4">
        <h5 class="mb-3" style="color:#7b5be6;font-weight:700;">Imagens do Carro</h5>
        <div class="row g-3">
          <?php for ($i = 1; $i <= 3; $i++): 
            $imgField = "carimg$i";
            $imgPath = !empty($car[$imgField]) ? $imageDir . $car[$imgField] : '';
          ?>
          <div class="col-md-4">
            <label class="form-label">Imagem <?= $i ?></label>
            <div class="mb-2 d-flex justify-content-center align-items-center" style="min-height:90px;">
              <?php if ($imgPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageDir . $car[$imgField])): ?>
                <img src="<?= $imageDir . $car[$imgField] ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
              <?php else: ?>
                <img src="/images/default.jpg" class="card-img-top" alt="Imagem não disponível" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
              <?php endif; ?>
            </div>
            <input type="file" name="carimg<?= $i ?>" class="form-control" accept="image/*">
          </div>
          <?php endfor; ?>
        </div>
      </div>
      <!-- Seção: Especificações -->
      <div class="mb-4">
        <h5 class="mb-3" style="color:#7b5be6;font-weight:700;">Especificações</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Preço de Manutenção</label>
            <textarea name="pdm" class="form-control" rows="2" placeholder="Ex: R$ 1.200,00 por revisão"><?= htmlspecialchars($car['pdm']) ?></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" style="color:#7b5be6;font-weight:600;">Caso o carro tenha mais de uma opção de combustível selecione Híbrido ou Flex</label>
            <label class="form-label">Tipo de Combustível</label>
            <select name="CarCombus" class="form-select" required>
              <option value="">Selecione</option>
              <option value="Gasolina" <?= ($car['CarCombus'] ?? '') === 'Gasolina' ? 'selected' : '' ?>>Gasolina</option>
              <option value="Etanol" <?= ($car['CarCombus'] ?? '') === 'Etanol' ? 'selected' : '' ?>>Etanol</option>
              <option value="Diesel" <?= ($car['CarCombus'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
              <option value="Flex" <?= ($car['CarCombus'] ?? '') === 'Flex' ? 'selected' : '' ?>>Flex</option>
              <option value="Elétrico" <?= ($car['CarCombus'] ?? '') === 'Elétrico' ? 'selected' : '' ?>>Elétrico</option>
              <option value="Híbrido" <?= ($car['CarCombus'] ?? '') === 'Híbrido' ? 'selected' : '' ?>>Híbrido</option>
              <option value="GNV" <?= ($car['CarCombus'] ?? '') === 'GNV' ? 'selected' : '' ?>>GNV</option>
              <option value="Outro" <?= ($car['CarCombus'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Tipo de Direção</label>
            <select name="CarDire" class="form-select" required title="Selecione o tipo de direção">
              <option value="">Selecione</option>
              <option value="Mecânica" <?= ($car['CarDire'] ?? '') === 'Mecânica' ? 'selected' : '' ?>>Mecânica</option>
              <option value="Hidráulica" <?= ($car['CarDire'] ?? '') === 'Hidráulica' ? 'selected' : '' ?>>Hidráulica</option>
              <option value="Elétrica" <?= ($car['CarDire'] ?? '') === 'Elétrica' ? 'selected' : '' ?>>Elétrica</option>
              <option value="Eletro-Hidráulica" <?= ($car['CarDire'] ?? '') === 'Eletro-Hidráulica' ? 'selected' : '' ?>>Eletro-Hidráulica</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Consumo (km/l)</label>
            <input type="number" name="Consumo" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($car['Consumo'] ?? '') ?>" required placeholder="Ex: 12.5">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Consumo Etanol (km/l)</label>
            <input type="number" name="Consumo2" id="consumo2" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($car['Consumo2'] ?? '') ?>" placeholder="Ex: 8.5">
          </div>
          <div class="col-md-6 mb-3" id="consumo3-group" style="display:none;">
            <label class="form-label">Autonomia Elétrica (km por Bateria)</label>
            <input type="number" name="Consumo3" id="consumo3" class="form-control" step="0.1" min="0" value="<?= htmlspecialchars($car['Consumo3'] ?? '') ?>" placeholder="Ex: 350">
          </div>
          <!-- Campos extras para híbrido -->
          <div class="col-md-6 mb-3" id="consumoDiesel-group" style="display:none;">
            <label class="form-label">Consumo Diesel (km/l)</label>
            <input type="number" name="Consumo5" id="consumoDiesel" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($car['Consumo5'] ?? '') ?>" placeholder="Ex: 15.0">
          </div>
          <div class="col-md-6 mb-3" id="consumoGnv-group" style="display:none;">
            <label class="form-label">Consumo GNV (km/m³)</label>
            <input type="number" name="Consumo4" id="consumoGnv" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($car['Consumo4'] ?? '') ?>" placeholder="Ex: 12.0">
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <label class="form-label conforto-label">Nível de Conforto</label>
            <div class="mb-2">
              <span id="conforto-value" class="conforto-label" style="font-size:1.4em;font-weight:bold;"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <input type="range" id="conforto" name="CarConfort" min="1" max="5" value="<?= (int)($car['CarConfort'] ?? 1) ?>" step="1" class="form-range" style="flex:1;">
            </div>
            <div id="conforto-desc" class="conforto-desc-label" style="margin-top:7px;font-size:0.98rem;">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <label class="form-label exportividade-label">Nível de Esportividade</label>
            <div class="mb-2">
              <span id="exportividade-value" class="exportividade-label" style="font-size:1.4em;font-weight:bold;"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <input type="range" id="exportividade" name="CarSport" min="1" max="5" value="<?= (int)($car['CarSport'] ?? 1) ?>" step="1" class="form-range" style="flex:1;">
            </div>
            <div id="exportividade-desc" class="exportividade-desc-label" style="margin-top:7px;font-size:0.98rem;">
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Preço FIPE</label>
          <input type="text" name="carfipe" class="form-control" value="<?= htmlspecialchars($car['carfipe']) ?>" placeholder="Ex: R$ 80.000,00">
        </div>
        <div class="mb-3">
          <label class="form-label">História</label>
          <textarea name="carhistory" class="form-control" rows="2" placeholder="Breve histórico do modelo"><?= htmlspecialchars($car['carhistory']) ?></textarea>
        </div>
      </div>
      <!-- Seção: Geração e Categoria -->
      <div class="mb-4">
        <h5 class="mb-3" style="color:#7b5be6;font-weight:700;">Geração e Categoria</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Geração</label>
            <select name="cargen" class="form-select" required>
              <option value="">Selecione a geração</option>
              <?php for ($i = 1; $i <= 20; $i++): ?>
                <option value="<?= $i ?>ª Geração" <?= ($car['cargen'] == $i . 'ª Geração') ? 'selected' : '' ?>><?= $i ?>ª Geração</option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Nome da Geração</label>
            <input type="text" name="genname" class="form-control" value="<?= htmlspecialchars($car['genname']) ?>" placeholder="Ex: New Generation">
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Categoria Principal</label>
            <select name="catid" class="form-select" required>
              <?php foreach ($nomesCategorias as $id => $nome): ?>
                <option value="<?= $id ?>" <?= $car['catid'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($nome) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Categoria Opcional 1</label>
            <select name="catidop1" class="form-select">
              <option value="0">Nenhuma</option>
              <?php foreach ($nomesCategorias as $id => $nome): ?>
                <option value="<?= $id ?>" <?= $car['catidop1'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($nome) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Categoria Opcional 2</label>
            <select name="catidop2" class="form-select">
              <option value="0">Nenhuma</option>
              <?php foreach ($nomesCategorias as $id => $nome): ?>
                <option value="<?= $id ?>" <?= $car['catidop2'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($nome) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Salvar Alterações</button>
      <a href="Carro.php?nome=<?= urlencode($car['carnome']) ?>" class="btn btn-secondary w-100 mt-2 py-2 fs-5">Cancelar</a>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/CRUDTCC/script/consumo_campos.js"></script>
<script>
// Barra de conforto
const confortoRange = document.getElementById('conforto');
const confortoValue = document.getElementById('conforto-value');
const confortoDesc = document.getElementById('conforto-desc');
const confortoLabels = [
  "Básico",
  "Intermediário",
  "Confortável",
  "Luxuoso",
  "Premium/Executivo"
];
const confortoDescs = [
  "Básico – Carros com bancos simples, pouca ou nenhuma tecnologia de suspensão avançada, ruído interno perceptível e poucos recursos de conforto, como ar-condicionado básico e acabamento simples.",
  "Intermediário – Veículos com bancos um pouco mais ergonômicos, ar-condicionado eficiente, suspensão melhor ajustada e menos ruído interno. Podem incluir alguns extras como direção elétrica ou regulagem de altura do banco.",
  "Confortável – Modelos com bancos mais anatômicos e materiais melhores, suspensão bem ajustada para reduzir impactos, isolamento acústico aprimorado e recursos como ar-condicionado digital, piloto automático e mais ajustes elétricos nos bancos.",
  "Luxuoso – Carros com bancos de couro, climatização individual, excelente isolamento acústico, suspensão adaptativa, sistema multimídia sofisticado e recursos como bancos aquecidos, ventilados e com ajustes automáticos.",
  "Premium/Executivo – O nível máximo de conforto, encontrado em sedãs de alto padrão e SUVs de luxo. Aqui há bancos com função de massagem, suspensão a ar, silêncio absoluto na cabine, acabamento refinado, materiais nobres e tecnologias avançadas para o bem-estar dos passageiros."
];
function updateConfortoBar(val) {
  const idx = parseInt(val, 10) - 1;
  confortoValue.textContent = confortoLabels[idx];
  confortoDesc.textContent = confortoDescs[idx];
}
confortoRange.addEventListener('input', function() {
  updateConfortoBar(this.value);
});
updateConfortoBar(confortoRange.value);

// Barra de esportividade
const exportividadeRange = document.getElementById('exportividade');
const exportividadeValue = document.getElementById('exportividade-value');
const exportividadeDesc = document.getElementById('exportividade-desc');
const exportividadeLabels = [
  "Básico",
  "Intermediário",
  "Esportivo",
  "Muito Esportivo",
  "Extremo"
];
const exportividadeDescs = [
  "Básico – Carros com desempenho modesto, foco em uso urbano, pouca resposta esportiva e visual discreto.",
  "Intermediário – Veículos com motor mais forte, suspensão levemente mais rígida, visual com alguns detalhes esportivos e resposta mais ágil.",
  "Esportivo – Modelos com motor potente, suspensão esportiva, visual agressivo, freios aprimorados e direção mais direta.",
  "Muito Esportivo – Carros com desempenho elevado, tecnologias de pista, visual marcante, bancos esportivos e componentes de alta performance.",
  "Extremo – Nível máximo de esportividade, carros de pista ou superesportivos, desempenho excepcional, materiais leves, aerodinâmica avançada e experiência de condução focada em performance."
];
function updateExportividadeBar(val) {
  const idx = parseInt(val, 10) - 1;
  exportividadeValue.textContent = exportividadeLabels[idx];
  exportividadeDesc.textContent = exportividadeDescs[idx];
}
exportividadeRange.addEventListener('input', function() {
  updateExportividadeBar(this.value);
});
updateExportividadeBar(exportividadeRange.value);

// Adiciona obrigatoriedade dinâmica aos campos de consumo
function toggleRequiredConsumoFields() {
  var combusSelect = document.querySelector('select[name="CarCombus"]');
  var tipo = combusSelect ? combusSelect.value : '';
  var consumo = document.querySelector('input[name="Consumo"]');
  var consumoGroup = consumo ? consumo.closest('.col-md-6.mb-3') : null;
  var consumoLabel = consumoGroup ? consumoGroup.querySelector('label.form-label') : null;
  var consumo2 = document.getElementById('consumo2');
  var consumo3 = document.getElementById('consumo3');
  var consumo4 = document.getElementById('consumo4');
  var consumo5 = document.getElementById('consumo5');
  var consumo3Group = document.getElementById('consumo3-group');
  var consumoDieselGroup = document.getElementById('consumoDiesel-group');
  var consumoGnvGroup = document.getElementById('consumoGnv-group');

  // Se Híbrido, nenhum campo de consumo é obrigatório
  if (tipo === 'Híbrido') {
    if (consumo) consumo.required = false;
    if (consumo2) consumo2.required = false;
    if (consumo3) consumo3.required = false;
    if (consumo4) consumo4.required = false;
    if (consumo5) consumo5.required = false;
    if (consumo3Group) consumo3Group.style.display = '';
    if (consumoDieselGroup) consumoDieselGroup.style.display = '';
    if (consumoGnvGroup) consumoGnvGroup.style.display = '';
  } else {
    // Consumo: se Elétrico, oculta e não exige
    if (tipo === 'Elétrico') {
      if (consumoGroup) consumoGroup.style.display = 'none';
      if (consumo) consumo.required = false;
    } else {
      if (consumoGroup) consumoGroup.style.display = '';
      if (consumo) consumo.required = true;
    }
    // Label Consumo: GNV = km/m³, outros = km/l
    if (consumoLabel) {
      if (tipo === 'GNV') {
        consumoLabel.textContent = 'Consumo (km/m³)';
      } else {
        consumoLabel.textContent = 'Consumo (km/l)';
      }
    }
    // Consumo2: Flex
    if (tipo === 'Flex') {
      if (consumo2) consumo2.required = true;
    } else {
      if (consumo2) consumo2.required = false;
    }
    // Consumo3: Híbrido ou Elétrico
    if (tipo === 'Elétrico') {
      if (consumo3Group) consumo3Group.style.display = '';
      if (consumo3) consumo3.required = true;
    } else {
      if (consumo3Group) consumo3Group.style.display = 'none';
      if (consumo3) consumo3.required = false;
    }
    // Diesel e GNV: só para híbrido
    if (consumoDieselGroup) consumoDieselGroup.style.display = 'none';
    if (consumoDiesel) consumoDiesel.required = false;
    if (consumoGnvGroup) consumoGnvGroup.style.display = 'none';
    if (consumoGnv) consumoGnv.required = false;
  }
}
window.addEventListener('DOMContentLoaded', function() {
  var combusSelect = document.querySelector('select[name="CarCombus"]');
  if (combusSelect) {
    combusSelect.addEventListener('change', toggleRequiredConsumoFields);
    toggleRequiredConsumoFields();
  }
});
</script>
</body>
</html>
