<HTML>
<HEAD>
 <TITLE>Recebe Dados - Insere no Banco</TITLE>
 <style>
    /* Estilo para a página */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
        text-align: center;
        padding: 20px;
    }
    .container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        max-width: 500px;
        margin: 50px auto;
    }
    h1 {
        color: #4CAF50;
    }
    a {
        text-decoration: none;
        color: #fff;
        background-color: #4CAF50;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
    }
    a:hover {
        background-color: #45a049;
    }
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
</HEAD>
<BODY>
<div class="container">
<?php
session_start();
include 'config.php';
include 'mysqlexecuta.php';

// Conexão com o banco de dados
$con = conectar();

// Gerador de ID único para o carro
function generateCarId($con) {
    $sql = "SELECT MAX(carid) AS max_id FROM car";
    $res = mysqlexecuta($con, $sql);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    return $row['max_id'] ? $row['max_id'] + 1 : 1; // Incrementa o maior ID ou começa em 1
}

// Função para salvar imagens e retornar o nome do arquivo
function saveImage($file, $imageDir) {
    if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
        $randomName = uniqid('img_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $imagePath = $imageDir . $randomName;
        move_uploaded_file($file['tmp_name'], $imagePath);
        return $randomName; // Retorna o nome do arquivo salvo
    }
    return null;
}

// Dados do formulário
$nome = $_POST["nome"];
$descricao = $_POST['descricao'];
// Novo campo motores
$carMotors = isset($_POST['motores']) ? $_POST['motores'] : null;
$marca = $_POST["marca"];
$categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : null;
$categoria2 = (isset($_POST['categoria2']) && $_POST['categoria2'] !== '') ? intval($_POST['categoria2']) : null;
$categoria3 = (isset($_POST['categoria3']) && $_POST['categoria3'] !== '') ? intval($_POST['categoria3']) : null;
$valor = $_POST["valor"];
$pdm = $_POST["pdm"];
$km = isset($_POST['km']) ? $_POST['km'] : null;
$ano_inicio = isset($_POST['ano_inicio']) ? intval($_POST['ano_inicio']) : null;
$ano_fim = isset($_POST['ano_fim']) ? intval($_POST['ano_fim']) : null;
$consumo = isset($_POST['consumo']) ? str_replace([','], ['.'], $_POST['consumo']) : '';
$consumo2 = isset($_POST['consumo2']) ? str_replace([','], ['.'], $_POST['consumo2']) : '';
$consumo3 = isset($_POST['consumo3']) ? str_replace([','], ['.'], $_POST['consumo3']) : '';
$consumo4 = isset($_POST['consumoGNV']) ? str_replace([','], ['.'], $_POST['consumoGNV']) : '';
$consumo5 = null;

// Ajuste para Etanol: se combustível for Etanol, Consumo2 recebe o valor de consumo e Consumo fica NULL
if (isset($_POST['CarCombus']) && $_POST['CarCombus'] === 'Etanol') {
    $consumo2 = (is_numeric($consumo) && floatval($consumo) != 0) ? floatval($consumo) : null;
    $consumo = null;
} else {
    $consumo = (is_numeric($consumo)) ? (floatval($consumo) == 0 ? null : floatval($consumo)) : null;
    $consumo2 = (is_numeric($consumo2)) ? (floatval($consumo2) == 0 ? null : floatval($consumo2)) : null;
}
$consumo3 = (is_numeric($consumo3)) ? (floatval($consumo3) == 0 ? null : floatval($consumo3)) : null;
$consumo4 = (is_numeric($consumo4)) ? (floatval($consumo4) == 0 ? null : floatval($consumo4)) : null;
$history = $_POST["history"];
$imagens = $_FILES["imagens"];
$imagem2 = $_FILES["imagem2"];
$imagem3 = $_FILES["imagem3"];
$gen = isset($_POST["gen"]) && $_POST["gen"] !== '' ? $_POST["gen"] : ''; // Corrigido para não ser null
$genname = isset($_POST["Genname"]) ? $_POST["Genname"] : null;

// Novos campos do formulário
$direcao = isset($_POST["CarDire"]) ? $_POST["CarDire"] : '';
$conforto = isset($_POST["CarConfort"]) ? intval($_POST["CarConfort"]) : 1;
$esportividade = isset($_POST["CarSport"]) ? intval($_POST["CarSport"]) : 1;
$combustivel = isset($_POST["CarCombus"]) ? $_POST["CarCombus"] : '';

// Recebe os anos de fabricação
$ano_inicio = isset($_POST["ano_inicio"]) ? intval($_POST["ano_inicio"]) : null;
$ano_fim = isset($_POST["ano_fim"]) ? intval($_POST["ano_fim"]) : null;

// Diretório para salvar as imagens
$imageDir = __DIR__ . '/../images/Carimg/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true); // Cria o diretório se não existir
}

// Salva as imagens e obtém os nomes dos arquivos
$image1Name = isset($imagens['tmp_name'][0]) ? saveImage([
    'tmp_name' => $imagens['tmp_name'][0],
    'name' => $imagens['name'][0]
], $imageDir) : null;

$image2Name = isset($imagem2['tmp_name']) ? saveImage($imagem2, $imageDir) : null;
$image3Name = isset($imagem3['tmp_name']) ? saveImage($imagem3, $imageDir) : null;

// Pega o logid do usuário logado
$logid = isset($_SESSION['logid']) ? $_SESSION['logid'] : null;

// Inserção no banco de dados usando prepared statement
$sql = "INSERT INTO car (
    logid, carnome, descricao, carmarca, catid, catidop1, catidop2, carfipe, CarFabIni, CarFabFim, pdm, Consumo, Consumo2, Consumo3, Consumo4, Consumo5,
    carimg, carimg2, carimg3, carhistory, cargen, genname,
    CarDire, CarConfort, CarSport, CarCombus, CarMotors, Revid
) VALUES (
    :logid, :carnome, :descricao, :carmarca, :catid, :catidop1, :catidop2, :carfipe, :CarFabIni, :CarFabFim, :pdm, :Consumo, :Consumo2, :Consumo3, :Consumo4, :Consumo5,
    :carimg, :carimg2, :carimg3, :carhistory, :cargen, :genname,
    :CarDire, :CarConfort, :CarSport, :CarCombus, :CarMotors, :Revid
)";
$stmt = $con->prepare($sql);
$stmt->bindValue(':logid', $logid);
$stmt->bindValue(':carnome', $nome);
$stmt->bindValue(':descricao', $descricao);
$stmt->bindValue(':carmarca', $marca);
$stmt->bindValue(':catid', $categoria);
$stmt->bindValue(':catidop1', $categoria2);
$stmt->bindValue(':catidop2', $categoria3);
$stmt->bindValue(':carfipe', $valor);
$stmt->bindValue(':CarFabIni', $ano_inicio);
$stmt->bindValue(':CarFabFim', $ano_fim);
$stmt->bindValue(':pdm', $pdm);
$stmt->bindValue(':Consumo', $consumo);
$stmt->bindValue(':Consumo2', $consumo2);
$stmt->bindValue(':Consumo3', $consumo3);
$stmt->bindValue(':Consumo4', $consumo4);
$stmt->bindValue(':Consumo5', $consumo5);
$stmt->bindValue(':carimg', $image1Name);
$stmt->bindValue(':carimg2', $image2Name);
$stmt->bindValue(':carimg3', $image3Name);
$stmt->bindValue(':carhistory', $history);
$stmt->bindValue(':cargen', $gen);
$stmt->bindValue(':genname', $genname);
$stmt->bindValue(':CarDire', $direcao);
$stmt->bindValue(':CarConfort', $conforto);
$stmt->bindValue(':CarSport', $esportividade);
$stmt->bindValue(':CarCombus', $combustivel);
$stmt->bindValue(':CarMotors', $carMotors);
$stmt->bindValue(':Revid', 0, PDO::PARAM_INT); // Valor padrão para Revid
$res = $stmt->execute();
?>
<div class="main-box">
<?php
if ($res) {
    echo '<div class="icon-success">&#10004;</div>';
    echo '<h1>Cadastro realizado com sucesso!</h1>';
    echo '<p>Seu veículo foi cadastrado no sistema.</p>';
    echo '<a href="../index.php">Menu Principal</a>';
} else {
    echo '<div class="icon-error">&#10008;</div>';
    echo '<h1>Erro ao cadastrar veículo</h1>';
    echo '<p>Ocorreu um erro ao tentar cadastrar o veículo.</p>';
    echo '<a href="../index.php">Menu Principal</a>';
}
?>
</div>
</div>
</BODY>
</HTML>