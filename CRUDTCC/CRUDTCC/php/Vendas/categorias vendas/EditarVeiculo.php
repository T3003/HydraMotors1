<?php
// EditarVeiculo.php - Edição de veículo compatível com Carro.php
include '../config.php';
include '../mysqlexecuta.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = conectar();
// DEBUG: Exibe o caminho real do banco de dados


// Busca o veículo pelo ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<div class='alert alert-danger'>ID inválido.</div>";
    exit;
}

$sql = "SELECT * FROM veiculos_venda WHERE id = :id LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    echo "<div class='alert alert-danger'>Veículo não encontrado.</div>";
    exit;
}

// Atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação básica
    $nome = trim($_POST['nome'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $ano = intval($_POST['ano'] ?? 0);
    $km = intval($_POST['km'] ?? 0);
    $preco = trim($_POST['preco'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $contato = trim($_POST['contato'] ?? '');
    $CarDire = trim($_POST['CarDire'] ?? '');
    $CarConfort = isset($_POST['conforto']) ? intval($_POST['conforto']) : (isset($_POST['CarConfort']) ? intval($_POST['CarConfort']) : 1);
    $CarCombus = trim($_POST['CarCombus'] ?? '');
    $Consumo = trim($_POST['Consumo'] ?? '');
    $Consumo2 = trim($_POST['Consumo2'] ?? '');
    $Consumo3 = trim($_POST['Consumo3'] ?? '');
    $Consumo4 = trim($_POST['Consumo4'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $categoria_idop1 = intval($_POST['categoria_idop1'] ?? 0);
    $categoria_idop2 = intval($_POST['categoria_idop2'] ?? 0);
    $gnv = intval($_POST['gnv'] ?? 0);
    $ConsumoGNV = trim($_POST['ConsumoGNV'] ?? '');
    $swamp_motor = trim($_POST['swamp_motor'] ?? '');
    $swamp_transmissao = trim($_POST['swamp_transmissao'] ?? '');
    $Transmissao = trim($_POST['Transmissao'] ?? '');
    $TransmissaoAntiga = trim($_POST['TransmissaoAntiga'] ?? '');
    $TipoTransmissao = trim($_POST['TipoTransmissao'] ?? '');
    $TipoTransmissaoOriginal = trim($_POST['TipoTransmissaoOriginal'] ?? '');

    // Imagens (mantém as antigas se não enviar novas)
    $imagem_principal = $car['imagem_principal'];
    $imagem_adicional_1 = $car['imagem_adicional_1'];
    $imagem_adicional_2 = $car['imagem_adicional_2'];
    $imgDir = realpath(__DIR__ . '/../../Vendas/Imagens/');
    if (!empty($_FILES['imagem_principal']['name']) && $_FILES['imagem_principal']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem_principal']['name'], PATHINFO_EXTENSION);
        $newName = 'img_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagem_principal']['tmp_name'], $imgDir . DIRECTORY_SEPARATOR . $newName);
        $imagem_principal = $newName;
    }
    if (!empty($_FILES['imagem_adicional_1']['name']) && $_FILES['imagem_adicional_1']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem_adicional_1']['name'], PATHINFO_EXTENSION);
        $newName = 'img_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagem_adicional_1']['tmp_name'], $imgDir . DIRECTORY_SEPARATOR . $newName);
        $imagem_adicional_1 = $newName;
    }
    if (!empty($_FILES['imagem_adicional_2']['name']) && $_FILES['imagem_adicional_2']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem_adicional_2']['name'], PATHINFO_EXTENSION);
        $newName = 'img_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagem_adicional_2']['tmp_name'], $imgDir . DIRECTORY_SEPARATOR . $newName);
        $imagem_adicional_2 = $newName;
    }

    $sqlUpdate = "UPDATE veiculos_venda SET 
        nome = :nome, marca = :marca, ano = :ano, km = :km, preco = :preco, descricao = :descricao, contato = :contato,
        CarDire = :CarDire, CarConfort = :CarConfort, CarCombus = :CarCombus, Consumo = :Consumo, Consumo2 = :Consumo2, Consumo3 = :Consumo3, Consumo4 = :Consumo4, categoria_id = :categoria_id, categoria_idop1 = :categoria_idop1, categoria_idop2 = :categoria_idop2,
        gnv = :gnv, ConsumoGNV = :ConsumoGNV,
        Transmissao = :Transmissao, TransmissaoAntiga = :TransmissaoAntiga, SwampTransmissao = :SwampTransmissao, TipoTransmissao = :TipoTransmissao, TipoTransmissaoOriginal = :TipoTransmissaoOriginal, Motor = :Motor, SwampMotor = :SwampMotor, MotorAntigo = :MotorAntigo,
        imagem_principal = :imagem_principal, imagem_adicional_1 = :imagem_adicional_1, imagem_adicional_2 = :imagem_adicional_2
        WHERE id = :id";
    $stmt = $con->prepare($sqlUpdate);
    $ok = $stmt->execute([
        ':nome' => $nome,
        ':marca' => $marca,
        ':ano' => $ano,
        ':km' => $km,
        ':preco' => $preco,
        ':descricao' => $descricao,
        ':contato' => $contato,
        ':CarDire' => $CarDire,
        ':CarConfort' => $CarConfort,
        ':CarCombus' => $CarCombus,
        ':Consumo' => $Consumo,
        ':Consumo2' => $Consumo2,
        ':Consumo3' => $Consumo3,
        ':Consumo4' => $Consumo4,
        ':categoria_id' => $categoria_id,
        ':categoria_idop1' => $categoria_idop1,
        ':categoria_idop2' => $categoria_idop2,
        ':gnv' => $gnv,
        ':ConsumoGNV' => $ConsumoGNV,
        ':imagem_principal' => $imagem_principal,
        ':imagem_adicional_1' => $imagem_adicional_1,
        ':imagem_adicional_2' => $imagem_adicional_2,
        ':Transmissao' => $Transmissao,
        ':TransmissaoAntiga' => trim($_POST['TransmissaoAntiga'] ?? ''),
        ':SwampTransmissao' => isset($_POST['SwampTransmissao']) ? 1 : 0,
        ':TipoTransmissao' => $TipoTransmissao,
        ':TipoTransmissaoOriginal' => $TipoTransmissaoOriginal,
        ':Motor' => $Motor,
        ':SwampMotor' => isset($_POST['SwampMotor']) ? 1 : 0,
        ':MotorAntigo' => $MotorAntigo,
        ':id' => $id
    ]);
    if ($ok) {
        header('Location: Carro.php?nome=' . urlencode($nome));
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar veículo. Tente novamente.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Veículo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Checkbox customizada para Swamp */
    .custom-checkbox-swamp {
      position: relative;
      padding-left: 2.1em;
      cursor: pointer;
      font-size: 1.08em;
      user-select: none;
      display: inline-block;
    }
    .custom-checkbox-swamp input[type="checkbox"] {
      opacity: 0;
      position: absolute;
      left: 0;
      top: 0;
      width: 1.5em;
      height: 1.5em;
      margin: 0;
      z-index: 2;
      cursor: pointer;
    }
    .custom-checkbox-swamp .checkmark {
      position: absolute;
      left: 0;
      top: 0;
      height: 1.5em;
      width: 1.5em;
      background-color: #fff;
      border: 2px solid #007bff;
      border-radius: 0.35em;
      transition: box-shadow 0.2s;
      box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    }
    .custom-checkbox-swamp input[type="checkbox"]:checked ~ .checkmark {
      background-color: #007bff;
      border-color: #007bff;
    }
    .custom-checkbox-swamp .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }
    .custom-checkbox-swamp input[type="checkbox"]:checked ~ .checkmark:after {
      display: block;
    }
    .custom-checkbox-swamp .checkmark:after {
      left: 0.45em;
      top: 0.18em;
      width: 0.35em;
      height: 0.7em;
      border: solid #fff;
      border-width: 0 0.22em 0.22em 0;
      transform: rotate(45deg);
      content: "";
    }

    /* Check azul customizado para a checkbox Swamp de Motor */
    #swamp-input.form-check-input:checked {
        background-color: #2196f3;
        border-color: #2196f3;
    }
    #swamp-input.form-check-input:checked[type="checkbox"] {
        box-shadow: 0 0 0 0.2rem rgba(33,150,243,.25);
    }
    #swamp-input.form-check-input:checked:after {
        content: "";
        position: absolute;
        left: 0.35em;
        top: 0.15em;
        width: 0.25em;
        height: 0.5em;
        border: solid #fff;
        border-width: 0 0.2em 0.2em 0;
        transform: rotate(45deg);
        display: block;
    }
    #swamp-input.form-check-input {
        position: relative;
    }

    /* Removido: Cor preta no modo claro para a descrição de conforto */
    </style>
</head>
<body>
<?php include_once("../headervendas.php"); ?>
<div class="container mt-4" style="max-width: 800px;">
    <div class="card shadow-lg" style="border-radius: 18px; background: rgba(255,255,245,0.97);">
        <div class="card-body p-4">
            <h2 class="mb-4 text-center" style="font-weight:700;letter-spacing:1px;">Editar Veículo</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($car['nome']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="marca" class="form-label required">Marca do Carro:</label>
                        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($car['marca']) ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Imagem Principal</label>
                        <?php if (!empty($car['imagem_principal'])): ?>
                            <div class="mb-2">
                                <img src="/CRUDTCC/php/Vendas/Imagens/<?= htmlspecialchars($car['imagem_principal']) ?>" alt="Imagem Principal" style="max-width:100%;max-height:120px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="imagem_principal" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Imagem Adicional 1</label>
                        <?php if (!empty($car['imagem_adicional_1'])): ?>
                            <div class="mb-2">
                                <img src="/CRUDTCC/php/Vendas/Imagens/<?= htmlspecialchars($car['imagem_adicional_1']) ?>" alt="Imagem 2" style="max-width:100%;max-height:120px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="imagem_adicional_1" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Imagem Adicional 2</label>
                        <?php if (!empty($car['imagem_adicional_2'])): ?>
                            <div class="mb-2">
                                <img src="/CRUDTCC/php/Vendas/Imagens/<?= htmlspecialchars($car['imagem_adicional_2']) ?>" alt="Imagem 3" style="max-width:100%;max-height:120px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="imagem_adicional_2" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ano</label>
                        <input type="number" name="ano" class="form-control" value="<?= htmlspecialchars($car['ano']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quilometragem</label>
                        <input type="number" name="km" class="form-control" value="<?= htmlspecialchars($car['km']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Preço</label>
                        <input type="text" name="preco" class="form-control" value="<?= htmlspecialchars($car['preco']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3" style="font-size: 1.1rem; font-family: 'Times New Roman', Times, serif; background: #fff; color: #222;"><?= htmlspecialchars($car['descricao']) ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contato</label>
                        <input type="text" name="contato" class="form-control" value="<?= htmlspecialchars($car['contato']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Direção</label>
                        <input type="text" name="CarDire" class="form-control" value="<?= htmlspecialchars($car['CarDire']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Conforto</label>
                        <input type="range" name="conforto" id="conforto" min="1" max="5" value="<?= isset($car['CarConfort']) ? (int)$car['CarConfort'] : 1 ?>" class="form-range">
                        <div class="d-flex justify-content-between small mt-1">
                            <span>Básico</span>
                            <span>Intermediário</span>
                            <span>Confortável</span>
                            <span>Luxuoso</span>
                            <span>Premium</span>
                        </div>
                        <div id="conforto-value" class="fw-bold mt-1"></div>
                        <div id="conforto-desc" class="small form-label"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Combustível</label>
                        <select name="CarCombus" class="form-select" id="CarCombus-select" required>
                            <option value="">Selecione</option>
                            <option value="Gasolina" <?php if($car['CarCombus']=='Gasolina') echo 'selected'; ?>>Gasolina</option>
                            <option value="Etanol" <?php if($car['CarCombus']=='Etanol') echo 'selected'; ?>>Etanol</option>
                            <option value="Diesel" <?php if($car['CarCombus']=='Diesel') echo 'selected'; ?>>Diesel</option>
                            <option value="Flex" <?php if($car['CarCombus']=='Flex') echo 'selected'; ?>>Flex</option>
                            <option value="Elétrico" <?php if($car['CarCombus']=='Elétrico') echo 'selected'; ?>>Elétrico</option>
                            <option value="Híbrido" <?php if($car['CarCombus']=='Híbrido') echo 'selected'; ?>>Híbrido</option>
                            <option value="GNV" <?php if($car['CarCombus']=='GNV') echo 'selected'; ?>>GNV</option>
                            <option value="Outro" <?php if($car['CarCombus']=='Outro') echo 'selected'; ?>>Outro</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3" id="kit-gnv-group" style="display:none;">
                        <label class="custom-checkbox-swamp mb-0">
                            <input type="checkbox" name="gnv" id="kit-gnv-checkbox" value="1" <?= !empty($car['gnv']) && $car['gnv'] ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            <span class="form-check-label ms-2">Kit GNV</span>
                        </label>
                    </div>
                    <div class="col-md-4 mb-3" id="consumo-group">
                        <label class="form-label">Consumo</label>
                        <input type="text" name="Consumo" id="consumo" class="form-control" value="<?= htmlspecialchars($car['Consumo']) ?>">
                    </div>
                    <div class="col-md-4 mb-3" id="consumo2-group">
                        <label class="form-label">Consumo Etanol (km/l)</label>
                        <input type="text" name="Consumo2" id="consumo2" class="form-control" value="<?= htmlspecialchars($car['Consumo2']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3" id="consumo3-group">
                        <label class="form-label">Autonomia (KM/Bateria)</label>
                        <input type="text" name="Consumo3" id="consumo3" class="form-control" value="<?= htmlspecialchars($car['Consumo3']) ?>">
                    </div>
                    <div class="col-md-4 mb-3" id="consumoDiesel-group">
                        <label class="form-label">Consumo Diesel (km/l)</label>
                        <input type="text" name="Consumo4" id="consumoDiesel" class="form-control" value="<?= htmlspecialchars($car['Consumo4']) ?>">
                    </div>
                    <div class="col-md-4 mb-3" id="consumoGNV-group">
                        <label class="form-label">Consumo GNV (km/m³)</label>
                        <input type="text" name="ConsumoGNV" id="consumoGNV" class="form-control" value="<?= htmlspecialchars($car['ConsumoGNV']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Categoria 1</label>
                        <select name="categoria_id" class="form-select">
                            <option value="1" <?php echo ($car['categoria_id']==1 ? 'selected' : ''); ?>>Antiguidade</option>
                            <option value="2" <?php echo ($car['categoria_id']==2 ? 'selected' : ''); ?>>Coupé</option>
                            <option value="3" <?php echo ($car['categoria_id']==3 ? 'selected' : ''); ?>>Sedan</option>
                            <option value="4" <?php echo ($car['categoria_id']==4 ? 'selected' : ''); ?>>Hatch</option>
                            <option value="5" <?php echo ($car['categoria_id']==5 ? 'selected' : ''); ?>>SUV</option>
                            <option value="6" <?php echo ($car['categoria_id']==6 ? 'selected' : ''); ?>>UTE/Picape</option>
                            <option value="7" <?php echo ($car['categoria_id']==7 ? 'selected' : ''); ?>>Híbrido</option>
                            <option value="8" <?php echo ($car['categoria_id']==8 ? 'selected' : ''); ?>>Elétrico</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Categoria 2</label>
                        <select name="categoria_idop1" class="form-select">
                            <option value="0" <?php echo ($car['categoria_idop1']==0 ? 'selected' : ''); ?>>Nenhuma</option>
                            <option value="1" <?php echo ($car['categoria_idop1']==1 ? 'selected' : ''); ?>>Antiguidade</option>
                            <option value="2" <?php echo ($car['categoria_idop1']==2 ? 'selected' : ''); ?>>Coupé</option>
                            <option value="3" <?php echo ($car['categoria_idop1']==3 ? 'selected' : ''); ?>>Sedan</option>
                            <option value="4" <?php echo ($car['categoria_idop1']==4 ? 'selected' : ''); ?>>Hatch</option>
                            <option value="5" <?php echo ($car['categoria_idop1']==5 ? 'selected' : ''); ?>>SUV</option>
                            <option value="6" <?php echo ($car['categoria_idop1']==6 ? 'selected' : ''); ?>>UTE/Picape</option>
                            <option value="7" <?php echo ($car['categoria_idop1']==7 ? 'selected' : ''); ?>>Híbrido</option>
                            <option value="8" <?php echo ($car['categoria_idop1']==8 ? 'selected' : ''); ?>>Elétrico</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Categoria 3</label>
                        <select name="categoria_idop2" class="form-select">
                            <option value="0" <?php echo (($car['categoria_idop2']==0) ? 'selected' : ''); ?>>Nenhuma</option>
                            <option value="1" <?php echo (($car['categoria_idop2']==1) ? 'selected' : ''); ?>>Antiguidade</option>
                            <option value="2" <?php echo (($car['categoria_idop2']==2) ? 'selected' : ''); ?>>Coupé</option>
                            <option value="3" <?php echo (($car['categoria_idop2']==3) ? 'selected' : ''); ?>>Sedan</option>
                            <option value="4" <?php echo (($car['categoria_idop2']==4) ? 'selected' : ''); ?>>Hatch</option>
                            <option value="5" <?php echo (($car['categoria_idop2']==5) ? 'selected' : ''); ?>>SUV</option>
                            <option value="6" <?php echo (($car['categoria_idop2']==6) ? 'selected' : ''); ?>>UTE/Picape</option>
                            <option value="7" <?php echo (($car['categoria_idop2']==7) ? 'selected' : ''); ?>>Híbrido</option>
                            <option value="8" <?php echo (($car['categoria_idop2']==8) ? 'selected' : ''); ?>>Elétrico</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card border-primary shadow-sm" style="border-radius: 14px;">
                            <div class="card-header bg-primary text-white" style="border-radius: 12px 12px 0 0; font-weight:600; font-size:1.1rem;">
                                Motor e Transmissão
                            </div>
                            <div class="card-body pb-2 pt-3">
                                <div class="row mb-3">
                                    <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                                        <label class="custom-checkbox-swamp mb-0 w-100">
                                            <input type="checkbox" name="SwampMotor" id="swamp-input" value="1" <?= !empty($car['SwampMotor']) && $car['SwampMotor'] ? 'checked' : '' ?>>
                                            <span class="checkmark"></span>
                                            <span class="form-check-label ms-2">Swamp de Motor</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <label class="custom-checkbox-swamp mb-0 w-100">
                                            <input type="checkbox" name="SwampTransmissao" id="swamp-transmissao-input" value="1" <?= !empty($car['SwampTransmissao']) && $car['SwampTransmissao'] ? 'checked' : '' ?>>
                                            <span class="checkmark"></span>
                                            <span class="form-check-label ms-2">Swamp de Transmissão</span>
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label" id="motor-label">Motor</label>
                                        <input type="text" name="Motor" class="form-control" value="<?= htmlspecialchars($car['Motor'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0" id="motor-antigo-group" style="display:none;">
                                        <label class="form-label">Motor Original</label>
                                        <input type="text" name="MotorAntigo" class="form-control" value="<?= htmlspecialchars($car['MotorAntigo'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="row align-items-end">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label" id="transmissao-label">Transmissão</label>
                                        <input type="text" name="Transmissao" class="form-control" value="<?= htmlspecialchars($car['Transmissao'] ?? '') ?>">
                                        <label class="form-label mt-2" id="tipo-transmissao-label">Tipo de Transmissão</label>
                                        <select name="TipoTransmissao" class="form-select" id="tipo-transmissao-select">
                                            <option value="">Selecione</option>
                                            <option value="Manual" <?php if(isset($car['TipoTransmissao']) && $car['TipoTransmissao'] === 'Manual') echo 'selected'; ?>>Manual</option>
                                            <option value="Automatico" <?php if(isset($car['TipoTransmissao']) && $car['TipoTransmissao'] === 'Automatico') echo 'selected'; ?>>Automático</option>
                                            <option value="Automatizado" <?php if(isset($car['TipoTransmissao']) && $car['TipoTransmissao'] === 'Automatizado') echo 'selected'; ?>>Automatizado</option>
                                            <option value="CVT" <?php if(isset($car['TipoTransmissao']) && $car['TipoTransmissao'] === 'CVT') echo 'selected'; ?>>CVT (Transmissão Continuamente Variável)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0" id="transmissao-antiga-group" style="display:none;">
                                        <label class="form-label">Transmissão Original</label>
                                        <input type="text" name="TransmissaoAntiga" class="form-control mb-2" value="<?= htmlspecialchars($car['TransmissaoAntiga'] ?? '') ?>">
                                        <label class="form-label">Tipo de Transmissão Original</label>
                                        <select name="TipoTransmissaoOriginal" class="form-select">
                                            <option value="">Selecione</option>
                                            <option value="Manual" <?php if(isset($car['TipoTransmissaoOriginal']) && $car['TipoTransmissaoOriginal'] === 'Manual') echo 'selected'; ?>>Manual</option>
                                            <option value="Automatico" <?php if(isset($car['TipoTransmissaoOriginal']) && $car['TipoTransmissaoOriginal'] === 'Automatico') echo 'selected'; ?>>Automático</option>
                                            <option value="Automatizado" <?php if(isset($car['TipoTransmissaoOriginal']) && $car['TipoTransmissaoOriginal'] === 'Automatizado') echo 'selected'; ?>>Automatizado</option>
                                            <option value="CVT" <?php if(isset($car['TipoTransmissaoOriginal']) && $car['TipoTransmissaoOriginal'] === 'CVT') echo 'selected'; ?>>CVT (Transmissão Continuamente Variável)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-success px-4">Salvar Alterações</button>
                    <a href="javascript:history.back()" class="btn btn-secondary ms-2 px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Lógica de exibição dos campos de consumo e Kit GNV igual ao CrudVendas.php
    var kitGNVCheckbox = document.getElementById('kit-gnv-checkbox');
    // Ajuste para o JS de exibição dos campos de consumo GNV
    function toggleConsumoFields() {
        var combusSelect = document.getElementById('CarCombus-select');
        if (!combusSelect) return;
        var tipo = combusSelect.value;
        var consumoGroup = document.getElementById('consumo-group');
        var consumo2Group = document.getElementById('consumo2-group');
        var consumo3Group = document.getElementById('consumo3-group');
        var consumoGNVGroup = document.getElementById('consumoGNV-group');
        var consumoDieselGroup = document.getElementById('consumoDiesel-group');
        var kitGNVGroup = document.getElementById('kit-gnv-group');
        // Kit GNV: aparece para todos exceto GNV, Elétrico e Híbrido
        if (tipo === 'GNV' || tipo === 'Elétrico' || tipo === 'Híbrido') {
            if (kitGNVGroup) kitGNVGroup.style.display = 'none';
        } else {
            if (kitGNVGroup) kitGNVGroup.style.display = '';
        }
        // Consumo GNV: aparece para GNV, Híbrido (sempre), e para outros combustíveis só se Kit GNV = Sim
        var showConsumoGNV = false;
        if (tipo === 'GNV' || tipo === 'Híbrido') {
            showConsumoGNV = true;
        } else if (kitGNVGroup && kitGNVGroup.style.display !== 'none' && kitGNVCheckbox && kitGNVCheckbox.checked) {
            showConsumoGNV = true;
        }
        if (consumoGNVGroup) consumoGNVGroup.style.display = showConsumoGNV ? '' : 'none';

        // Consumo principal
        if (tipo === 'Elétrico') {
            if (consumoGroup) consumoGroup.style.display = 'none';
        } else {
            if (consumoGroup) consumoGroup.style.display = '';
        }
        // Consumo Etanol
        if (tipo === 'Flex' || tipo === 'Etanol' || tipo === 'Híbrido') {
            if (consumo2Group) consumo2Group.style.display = '';
        } else {
            if (consumo2Group) consumo2Group.style.display = 'none';
        }
        // Consumo Diesel
        if (tipo === 'Diesel' || tipo === 'Híbrido') {
            if (consumoDieselGroup) consumoDieselGroup.style.display = '';
        } else {
            if (consumoDieselGroup) consumoDieselGroup.style.display = 'none';
        }
        // Consumo Bateria
        if (tipo === 'Elétrico' || tipo === 'Híbrido') {
            if (consumo3Group) consumo3Group.style.display = '';
        } else {
            if (consumo3Group) consumo3Group.style.display = 'none';
        }
    }
    window.addEventListener('DOMContentLoaded', function() {
        var combusSelect = document.getElementById('CarCombus-select');
        var kitGNVSelect = document.getElementById('kit-gnv-select');
        if (combusSelect) {
            combusSelect.addEventListener('change', toggleConsumoFields);
        }
        if (kitGNVSelect) {
            kitGNVSelect.addEventListener('change', toggleConsumoFields);
        }
        toggleConsumoFields();

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
        if (confortoRange) {
          confortoRange.addEventListener('input', function() {
            updateConfortoBar(this.value);
          });
          updateConfortoBar(confortoRange.value);
        }

        // Motor swap: se Swamp marcado, mostra Motor Original e renomeia Motor para Motor Atual
        var swampInput = document.getElementById('swamp-input');
        var motorAntigoGroup = document.getElementById('motor-antigo-group');
        var motorLabel = document.getElementById('motor-label');
        function toggleMotorSwap() {
            if (swampInput && swampInput.checked) {
                if (motorAntigoGroup) motorAntigoGroup.style.display = '';
                if (motorLabel) motorLabel.textContent = 'Motor Atual';
            } else {
                if (motorAntigoGroup) motorAntigoGroup.style.display = 'none';
                if (motorLabel) motorLabel.textContent = 'Motor';
            }
        }
        if (swampInput) {
            swampInput.addEventListener('change', toggleMotorSwap);
            toggleMotorSwap();
        }

        // Swamp de Transmissão: se marcado, mostra Transmissão Original e renomeia Transmissão para Transmissão Atual
        var swampTransmissaoInput = document.getElementById('swamp-transmissao-input');
        var transmissaoAntigaGroup = document.getElementById('transmissao-antiga-group');
        var transmissaoLabel = document.getElementById('transmissao-label');
        var tipoTransmissaoLabel = document.getElementById('tipo-transmissao-label');
        function toggleTransmissaoSwap() {
            if (swampTransmissaoInput && swampTransmissaoInput.checked) {
                if (transmissaoAntigaGroup) transmissaoAntigaGroup.style.display = '';
                if (transmissaoLabel) transmissaoLabel.textContent = 'Transmissão Atual';
                if (tipoTransmissaoLabel) tipoTransmissaoLabel.textContent = 'Tipo de Transmissão Atual';
            } else {
                if (transmissaoAntigaGroup) transmissaoAntigaGroup.style.display = 'none';
                if (transmissaoLabel) transmissaoLabel.textContent = 'Transmissão';
                if (tipoTransmissaoLabel) tipoTransmissaoLabel.textContent = 'Tipo de Transmissão';
            }
        }
        if (swampTransmissaoInput) {
            swampTransmissaoInput.addEventListener('change', toggleTransmissaoSwap);
            toggleTransmissaoSwap();
        }

        // Consumo GNV: aparece quando Kit GNV for sim
        var kitGNVCheckbox = document.getElementById('kit-gnv-checkbox');
        var consumoGNVGroup = document.getElementById('consumoGNV-group');
        function toggleConsumoGNV() {
            if (kitGNVCheckbox && kitGNVCheckbox.checked) {
                if (consumoGNVGroup) consumoGNVGroup.style.display = '';
            } else {
                if (consumoGNVGroup) consumoGNVGroup.style.display = 'none';
            }
        }
        if (kitGNVCheckbox) {
            kitGNVCheckbox.addEventListener('change', toggleConsumoGNV);
            toggleConsumoGNV();
        }
    });
    </script>
</body>
</html>
