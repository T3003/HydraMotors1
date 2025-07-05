<?php
include '../config.php';
include '../mysqlexecuta.php'; // Para executar o script no MySQL

// Conexão com o banco de dados (PDO)
$con = conectar();

// Função para salvar imagens e retornar o nome do arquivo (id aleatório)
function saveImage($file, $imageDir) {
    if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
        $randomId = uniqid('img_', true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $randomName = $randomId . ($ext ? ('.' . $ext) : '');
        $imagePath = $imageDir . $randomName;
        move_uploaded_file($file['tmp_name'], $imagePath);
        return $randomName;
    }
    return null;
}

// Diretório para salvar as imagens
$imageDir = __DIR__ . '/Imagens/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Recebe os dados do formulário (usando PDO)
$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$categoria = intval($_POST['categoria'] ?? 0);
$categoria2 = intval($_POST['categoria2'] ?? 0);
$categoria3 = intval($_POST['categoria3'] ?? 0);
$marca = $_POST['marca'] ?? '';
$ano = intval($_POST['ano'] ?? 0);
$consumo = $_POST['consumo'] ?? '';
$consumo2 = '';
$consumo3 = '';
$consumo4 = '';
// Consumo2: Flex ou Híbrido
if (isset($_POST['combustivel']) && ($_POST['combustivel'] === 'Flex' || $_POST['combustivel'] === 'Híbrido')) {
    $consumo2 = $_POST['consumo2'] ?? '';
}
// Consumo3: Híbrido ou Elétrico
if (isset($_POST['combustivel']) && ($_POST['combustivel'] === 'Híbrido' || $_POST['combustivel'] === 'Elétrico')) {
    $consumo3 = $_POST['consumo3'] ?? '';
}
// Consumo4: Apenas Híbrido
if (isset($_POST['combustivel']) && $_POST['combustivel'] === 'Híbrido') {
    $consumo4 = $_POST['consumo4'] ?? '';
}
// Recebe o conforto corretamente do formulário
$conforto = isset($_POST['CarConfort']) ? intval($_POST['CarConfort']) : 1;
$direcao = $_POST['direcao'] ?? '';
$km = intval($_POST['km'] ?? 0);
$preco = $_POST['preco'] ?? '';
$contato = $_POST['contato'] ?? '';

// Lógica de combustível (igual ao Crud.php)
$combustivel = $_POST['combustivel'] ?? '';

$imagens = $_FILES['imagens'] ?? null;
$imagem2 = $_FILES['imagem2'] ?? null;
$imagem3 = $_FILES['imagem3'] ?? null;

// Salva as imagens e obtém os nomes dos arquivos (ids aleatórios)
$imagem_principal = isset($imagens['tmp_name'][0]) ? saveImage([
    'tmp_name' => $imagens['tmp_name'][0],
    'name' => $imagens['name'][0]
], $imageDir) : null;
$imagem_adicional_1 = isset($imagem2['tmp_name']) ? saveImage($imagem2, $imageDir) : null;
$imagem_adicional_2 = isset($imagem3['tmp_name']) ? saveImage($imagem3, $imageDir) : null;

// Puxa o logid da sessão
session_start();
$logid = isset($_SESSION['logid']) ? intval($_SESSION['logid']) : 0;

// Recebe os dados do formulário
$tem_gnv = isset($_POST['tem_gnv']) && $_POST['tem_gnv'] === 'sim' ? 1 : 0;
$consumo_gnv = isset($_POST['consumo_gnv']) && $_POST['consumo_gnv'] !== '' ? floatval($_POST['consumo_gnv']) : null;
$gnv = isset($_POST['gnv']) && $_POST['gnv'] == '1' ? 1 : 0;
$ConsumoGNV = isset($_POST['ConsumoGNV']) ? $_POST['ConsumoGNV'] : '';

$Motor = $_POST['Motor'] ?? '';
$MotorAntigo = $_POST['MotorAntigo'] ?? '';
$SwampMotor = isset($_POST['SwampMotor']) && $_POST['SwampMotor'] == '1' ? 1 : 0;
$Transmissao = $_POST['Transmissao'] ?? '';
$TransmissaoAntiga = $_POST['TransmissaoAntiga'] ?? '';
$SwampTransmissao = isset($_POST['SwampTransmissao']) && $_POST['SwampTransmissao'] == '1' ? 1 : 0;
$TipoTransmissao = $_POST['TipoTransmissao'] ?? '';
$TipoTransmissaoOriginal = $_POST['TipoTransmissaoOriginal'] ?? '';

// Monta o SQL usando o valor correto de CarConfort
$sql = "INSERT INTO veiculos_venda 
    (nome, descricao, categoria_id, categoria_idop1, categoria_idop2, marca, ano, Consumo, Consumo2, Consumo3, Consumo4, CarConfort, CarDire, CarCombus, km, preco, contato, imagem_principal, imagem_adicional_1, imagem_adicional_2, logid, data_cadastro, gnv, ConsumoGNV, Motor, MotorAntigo, SwampMotor, Transmissao, TransmissaoAntiga, SwampTransmissao, TipoTransmissao, TipoTransmissaoOriginal, visivel, pausa)
    VALUES (
        '$nome', '$descricao', $categoria, $categoria2, $categoria3, '$marca', $ano, '$consumo', '$consumo2', '$consumo3', '$consumo4', '$conforto', '$direcao', '$combustivel', $km, '$preco', '$contato',
        '$imagem_principal', '$imagem_adicional_1', '$imagem_adicional_2', '$logid', datetime('now'), '$gnv', '$ConsumoGNV',
        '$Motor', '$MotorAntigo', '$SwampMotor', '$Transmissao', '$TransmissaoAntiga', '$SwampTransmissao', '$TipoTransmissao', '$TipoTransmissaoOriginal',
        1, 1
    )";
$res = mysqlexecuta($con, $sql);
?><!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastro de Veículo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('/CRUDTCC/images/Background.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .main-box {
      background: rgba(255,255,220,0.92);
      border-radius: 18px;
      margin: 48px auto 0 auto;
      box-shadow: 0 2px 16px rgba(0,0,0,0.10);
      padding: 40px 32px 32px 32px;
      max-width: 480px;
      text-align: center;
      animation: fadein 0.7s;
    }
    .main-box h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #4a3e8e;
      margin-bottom: 18px;
    }
    .main-box p {
      font-size: 1.1rem;
      margin-bottom: 10px;
    }
    .main-box a {
      display: inline-block;
      margin: 8px 6px;
      padding: 8px 18px;
      border-radius: 6px;
      background: #a7d3d6;
      color: #222;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.2s, color 0.2s;
    }
    .main-box a:hover {
      background: #7b5be6;
      color: #fff;
    }
    .icon-success {
      font-size: 3.2rem;
      color: #4caf50;
      margin-bottom: 10px;
    }
    .icon-error {
      font-size: 3.2rem;
      color: #e53935;
      margin-bottom: 10px;
    }
    @keyframes fadein { from { opacity:0; transform:translateY(30px);} to { opacity:1; transform:translateY(0);} }
  </style>
</head>
<body>
  <div class="main-box">
    <?php if ($res): ?>
      <div class="icon-success">&#10004;</div>
      <h1>Veículo cadastrado com sucesso!</h1>
      <p>Seu veículo foi cadastrado para venda.</p>
      <a href='CrudVendas.php'>Cadastrar outro veículo</a>
      <a href='../../index.php'>Menu Principal</a>
    <?php else: ?>
      <div class="icon-error">&#10008;</div>
      <h1>Erro ao cadastrar veículo</h1>
      <p>Ocorreu um erro ao tentar cadastrar o veículo para venda.</p>
      <a href='CrudVendas.php'>Tentar novamente</a>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
