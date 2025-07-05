<?php
// processaCadastro.php (categorias) - Suporte a CarCombus2 e Consumo2
session_start();
include '../config.php';
include '../mysqlexecuta.php';

$con = conectar();
mysqli_select_db($con, 'hydramotors');

function generateCarId($con) {
    $sql = "SELECT MAX(carid) AS max_id FROM car";
    $res = mysqlexecuta($con, $sql);
    $row = mysqli_fetch_assoc($res);
    return $row['max_id'] ? $row['max_id'] + 1 : 1;
}

$carId = generateCarId($con);
$nome = mysqli_real_escape_string($con, $_POST["nome"]);
$descricao = mysqli_real_escape_string($con, $_POST['descricao']);
$marca = mysqli_real_escape_string($con, $_POST["marca"]);
$categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : null;
$categoria2 = (isset($_POST['categoria2']) && $_POST['categoria2'] !== '') ? intval($_POST['categoria2']) : null;
$categoria3 = (isset($_POST['categoria3']) && $_POST['categoria3'] !== '') ? intval($_POST['categoria3']) : null;
$valor = mysqli_real_escape_string($con, $_POST["valor"]);
$pdm = mysqli_real_escape_string($con, $_POST["pdm"]);
$km = isset($_POST['km']) ? mysqli_real_escape_string($con, $_POST['km']) : null;
$ano_inicio = isset($_POST['ano_inicio']) ? intval($_POST['ano_inicio']) : null;
$ano_fim = isset($_POST['ano_fim']) ? intval($_POST['ano_fim']) : null;
$consumo = isset($_POST['Consumo']) ? str_replace([','], ['.'], $_POST['Consumo']) : '';
$consumo2 = isset($_POST['Consumo2']) ? str_replace([','], ['.'], $_POST['Consumo2']) : '';
$consumo = (is_numeric($consumo)) ? (floatval($consumo) == 0 ? 1 : floatval($consumo)) : null;
$consumo2 = (is_numeric($consumo2)) ? (floatval($consumo2) == 0 ? 1 : floatval($consumo2)) : null;
$history = mysqli_real_escape_string($con, $_POST["history"]);
$imagens = $_FILES["imagens"];
$imagem2 = $_FILES["imagem2"];
$imagem3 = $_FILES["imagem3"];
$gen = isset($_POST["gen"]) ? mysqli_real_escape_string($con, $_POST["gen"]) : null;
$genname = isset($_POST["Genname"]) ? mysqli_real_escape_string($con, $_POST["Genname"]) : null;
$direcao = isset($_POST["CarDire"]) ? mysqli_real_escape_string($con, $_POST["CarDire"]) : null;
$conforto = isset($_POST["CarConfort"]) ? intval($_POST["CarConfort"]) : null;
$esportividade = isset($_POST["CarSport"]) ? intval($_POST["CarSport"]) : null;
$combustivel = isset($_POST["CarCombus"]) ? mysqli_real_escape_string($con, $_POST["CarCombus"]) : null;
$car_combus2 = isset($_POST['CarCombus2']) && $_POST['CarCombus2'] !== '' ? mysqli_real_escape_string($con, $_POST['CarCombus2']) : null;

// Imagens (mock, pois não há upload nesta versão)
$image1Name = null;
$image2Name = null;
$image3Name = null;
$logid = isset($_SESSION['logid']) ? $_SESSION['logid'] : null;

$sql = "INSERT INTO car (
    carid, logid, carnome, cardesc, carmarca, catid, catidop1, catidop2, carfipe, CarFabIn, CarFabFim, carpdm, Consumo, Consumo2,
    carimg, carimg2, carimg3, carhistory, cargen, genname,
    CarDire, CarConfort, CarSport, CarCombus, CarCombus2
) VALUES (
    '$carId', " . ($logid ? "'$logid'" : "NULL") . ", '$nome', '$descricao', '$marca', '$categoria', " . ($categoria2 !== null ? $categoria2 : "NULL") . ", " . ($categoria3 !== null ? $categoria3 : "NULL") . ", '$valor', " . ($ano_inicio !== null ? $ano_inicio : "NULL") . ", " . ($ano_fim !== null ? $ano_fim : "NULL") . ", '$pdm', " . ($consumo !== null ? $consumo : "NULL") . ", " . ($consumo2 !== null ? $consumo2 : "NULL") . ",
    '$image1Name', '$image2Name', '$image3Name', '$history', '$gen', '$genname',
    " . ($direcao !== null ? "'$direcao'" : "NULL") . ", " . ($conforto !== null ? $conforto : "NULL") . ", " . ($esportividade !== null ? $esportividade : "NULL") . ", " . ($combustivel !== null ? "'$combustivel'" : "NULL") . ", " . ($car_combus2 !== null ? "'$car_combus2'" : "NULL") . "
)";
$res = mysqlexecuta($con, $sql);
if ($res) {
    echo 'Cadastro realizado com sucesso!';
} else {
    echo 'Erro ao cadastrar veículo.';
}
?>
