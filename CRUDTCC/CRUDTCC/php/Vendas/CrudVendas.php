<?php
session_start();
require_once("../config.php");
if (!isset($_SESSION['logid'])) {
    header('Location: /CRUDTCC/php/Usuários/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anunciar Veículo à Venda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/crudvendas.css" rel="stylesheet">
</head>
<body style="background: url('/CRUDTCC/images/Background.png') no-repeat center center fixed; background-size: cover;">
    <?php include_once("headervendas.php"); ?>
    <div class="container mt-4" style="max-width: 700px;">
        <div class="card mx-auto" style="border-radius: 18px; background: rgba(255,255,245,0.97);">
            <div class="card-header text-center">
                <h1>Anunciar Veículo à Venda</h1>
                <small class="d-block mt-1">Campos com <span style="color:#d00">*</span> são obrigatórios</small>
            </div>
            <div class="card-body">
                <form id="vendaForm" action="processoCadastrovendas.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="mb-3">
                        <label for="nome" class="form-label required">Nome do Veículo:</label>
                        <input type="text" id="nome" name="nome" class="form-control" required placeholder="Ex: Corolla Altis 2.0 Flex">
                    </div>
                    <!-- INÍCIO: Combustível + Consumo agrupados (MOVIDO PARA CIMA) -->
                    <div class="mb-3">
                        <label for="combustivel" class="form-label required">Tipo de Combustível:</label>
                        <select id="combustivel" name="combustivel" class="form-select" required onchange="toggleOutroCombustivel();toggleConsumoFields();">
                            <option value="">Selecione</option>
                            <option value="Gasolina">Gasolina</option>
                            <option value="Etanol">Etanol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Flex">Flex</option>
                            <option value="Elétrico">Elétrico</option>
                            <option value="Híbrido">Híbrido</option>
                            <option value="GNV">GNV</option>
                            <option value="Outro">Outro</option>
                        </select>
                        <input type="text" id="combustivel_outro" name="combustivel_outro" class="form-control mt-2" placeholder="Informe o combustível" style="display:none;">
                        <div class="row mt-2">
                            <div class="col-md-6 mb-3" id="consumo-group">
                                <label for="consumo" class="form-label required" id="consumo-label">Consumo (km/l):</label>
                                <input type="number" id="consumo" name="consumo" class="form-control" step="0.01" min="0" required placeholder="Ex: 12.5">
                            </div>
                            <div class="col-md-6 mb-3" id="consumo2-group" style="display:none;">
                                <label class="form-label" for="consumo2">Consumo Etanol (km/l):</label>
                                <input type="number" id="consumo2" name="consumo2" class="form-control" step="0.01" min="0" placeholder="Ex: 8.5">
                            </div>
                            <div class="col-md-6 mb-3" id="consumo4-group" style="display:none;">
                                <label class="form-label" for="consumo4">Consumo Diesel (km/l):</label>
                                <input type="number" id="consumo4" name="consumo4" class="form-control" step="0.01" min="0" placeholder="Ex: 10.0">
                            </div>
                            <div class="col-md-6 mb-3" id="consumo3-group" style="display:none;">
                                <label class="form-label" for="consumo3">Autonomia (KM/Bateria):</label>
                                <input type="number" id="consumo3" name="consumo3" class="form-control" step="0.1" min="0" placeholder="Ex: 350">
                            </div>
                            <div class="col-md-6 mb-3" id="kit-gnv-group" style="display:none;">
                                <label class="custom-checkbox-swamp mb-0">
                                    <input type="checkbox" name="gnv" id="kit-gnv-checkbox" value="1">
                                    <span class="checkmark"></span>
                                    <span class="form-check-label ms-2">Kit GNV</span>
                                </label>
                            </div>
                            <div class="col-md-6 mb-3" id="consumo_gnv_group" style="display:none;">
                                <label for="ConsumoGNV" class="form-label">Consumo GNV (km/m³):</label>
                                <input type="number" id="ConsumoGNV" name="ConsumoGNV" class="form-control" step="0.01" min="0" placeholder="Ex: 13.5">
                            </div>
                        </div>
                    </div>
                    <!-- FIM: Combustível + Consumo agrupados (MOVIDO PARA CIMA) -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label required">Descrição do Veículo:</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3" required placeholder="Breve descrição do modelo, versão, diferenciais..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="categoria" class="form-label required">Categoria:</label>
                            <select id="categoria" name="categoria" class="form-select" required>
                                <option value="4">Antiguidade</option>
                                <option value="5">Coupe</option>
                                <option value="1">Hatch</option>
                                <option value="3">Sedan</option>
                                <option value="2">SUV</option>
                                <option value="6">UTE/Picape</option>
                                <option value="7">Híbrido</option>
                                <option value="8">Elétrico</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="categoria2" class="form-label">Categoria Opcional 2:</label>
                            <select id="categoria2" name="categoria2" class="form-select">
                                <option value="0">Nenhuma</option>
                                <option value="4">Antiguidade</option>
                                <option value="5">Coupe</option>
                                <option value="1">Hatch</option>
                                <option value="3">Sedan</option>
                                <option value="2">SUV</option>
                                <option value="6">UTE/Picape</option>
                                <option value="7">Híbrido</option>
                                <option value="8">Elétrico</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="categoria3" class="form-label">Categoria Opcional 3:</label>
                            <select id="categoria3" name="categoria3" class="form-select">
                                <option value="0">Nenhuma</option>
                                <option value="4">Antiguidade</option>
                                <option value="5">Coupe</option>
                                <option value="1">Hatch</option>
                                <option value="3">Sedan</option>
                                <option value="2">SUV</option>
                                <option value="6">UTE/Picape</option>
                                <option value="7">Híbrido</option>
                                <option value="8">Elétrico</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label required">Marca:</label>
                            <select id="marca" name="marca" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Toyota">Toyota</option>
                                <option value="Ford">Ford</option>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Honda">Honda</option>
                                <option value="BMW">BMW</option>
                                <option value="Mercedes">Mercedes</option>
                                <option value="Volkswagen">Volkswagen</option>
                                <option value="Hyundai">Hyundai</option>
                                <option value="Nissan">Nissan</option>
                                <option value="Kia">Kia</option>
                                <option value="Fiat">Fiat</option>
                                <option value="Renault">Renault</option>
                                <option value="Peugeot">Peugeot</option>
                                <option value="Citroën">Citroën</option>
                                <option value="Subaru">Subaru</option>
                                <option value="Mitsubishi">Mitsubishi</option>
                                <option value="Land Rover">Land Rover</option>
                                <option value="Porsche">Porsche</option>
                                <option value="Lamborghini">Lamborghini</option>
                                <option value="Ferrari">Ferrari</option>
                                <option value="Audi">Audi</option>
                                <option value="Volvo">Volvo</option>
                                <option value="Mazda">Mazda</option>
                                <option value="Chrysler">Chrysler</option>
                                <option value="Dodge">Dodge</option>
                                <option value="Jeep">Jeep</option>
                                <option value="Buick">Buick</option>
                                <option value="GMC">GMC</option>
                                <option value="Tesla">Tesla</option>
                                <option value="Jaguar">Jaguar</option>
                                <option value="Infiniti">Infiniti</option>
                                <option value="Acura">Acura</option>
                                <option value="Lincoln">Lincoln</option>
                                <option value="Alfa Romeo">Alfa Romeo</option>
                                <option value="Maserati">Maserati</option>
                                <option value="Aston Martin">Aston Martin</option>
                                <option value="Bentley">Bentley</option>
                                <option value="Rolls Royce">Rolls Royce</option>
                                <option value="Bugatti">Bugatti</option>
                                <option value="McLaren">McLaren</option>
                                <option value="Pagani">Pagani</option>
                                <option value="Koenigsegg">Koenigsegg</option>
                                <option value="Lotus">Lotus</option>
                                <option value="Lexus">Lexus</option>
                                <option value="Genesis">Genesis</option>
                                <option value="Scion">Scion</option>
                                <option value="Smart">Smart</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ano" class="form-label required">Ano:</label>
                            <input type="text" id="ano" name="ano" class="form-control" required placeholder="Ex: 2015">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="direcao" class="form-label required">Direção:</label>
                            <select id="direcao" name="direcao" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Hidráulica">Hidráulica</option>
                                <option value="Elétrica">Elétrica</option>
                                <option value="Eletro-Hidráulica">Eletro-Hidráulica</option>
                                <option value="Mecânica">Mecânica</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="km" class="form-label required">Quilometragem:</label>
                            <input type="number" id="km" name="km" class="form-control" required min="0" placeholder="Ex: 45000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="conforto" class="form-label required">Nível de Conforto:</label>
                        <div class="mb-2">
                            <span id="conforto-value" style="font-size:1.2em;font-weight:bold;"></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="range" id="conforto" name="CarConfort" class="form-range" min="1" max="5" step="1" value="1" required style="flex:1;">
                        </div>
                        <div id="conforto-desc" style="margin-top:7px;font-size:0.98rem;color:#555;"></div>
                    </div>
                    <div class="mb-3">
                        <label for="contato" class="form-label required">Contato para Venda:</label>
                        <input type="text" id="contato" name="contato" class="form-control" required placeholder="Ex: (11) 91234-5678">
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label required">Valor do Veículo (R$):</label>
                        <input type="text" id="preco" name="preco" class="form-control" required placeholder="Ex: 45000,00">
                    </div>
                    <div class="mb-3">
                        <label for="imagens" class="form-label required">Imagem do Veículo:</label>
                        <input type="file" id="imagens" name="imagens[]" class="form-control" accept="image/*" required>
                        <img id="preview1" class="img-preview" style="display:none; max-width: 120px; max-height: 90px; border-radius: 8px; box-shadow: 0 1px 6px #0002; margin-top: 6px;">
                    </div>
                    <div class="mb-3">
                        <label for="imagem2" class="form-label">Imagem Adicional:</label>
                        <input type="file" id="imagem2" name="imagem2" class="form-control" accept="image/*">
                        <img id="preview2" class="img-preview" style="display:none; max-width: 120px; max-height: 90px; border-radius: 8px; box-shadow: 0 1px 6px #0002; margin-top: 6px;">
                    </div>
                    <div class="mb-3">
                        <label for="imagem3" class="form-label">Imagem Adicional:</label>
                        <input type="file" id="imagem3" name="imagem3" class="form-control" accept="image/*">
                        <img id="preview3" class="img-preview" style="display:none; max-width: 120px; max-height: 90px; border-radius: 8px; box-shadow: 0 1px 6px #0002; margin-top: 6px;">
                    </div>
                    <!-- SWAPS DE MOTOR E TRANSMISSÃO -->
                    <div class="mb-4">
                        <div class="card border-primary shadow-sm" style="border-radius: 14px;">
                            <div class="card-header bg-primary text-white" style="border-radius: 12px 12px 0 0; font-weight:600; font-size:1.1rem;">
                                Motor e Transmissão
                            </div>
                            <div class="card-body pb-2 pt-3">
                                <div class="row mb-3">
                                    <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                                        <label class="custom-checkbox-swamp mb-0 w-100">
                                            <input type="checkbox" name="SwampMotor" id="swamp-input" value="1">
                                            <span class="checkmark"></span>
                                            <span class="form-check-label ms-2">Swamp de Motor</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <label class="custom-checkbox-swamp mb-0 w-100">
                                            <input type="checkbox" name="SwampTransmicao" id="swamp-transmicao-input" value="1">
                                            <span class="checkmark"></span>
                                            <span class="form-check-label ms-2">Swamp de Transmissão</span>
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label" id="motor-label">Motor</label>
                                        <input type="text" name="Motor" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0" id="motor-antigo-group" style="display:none;">
                                        <label class="form-label">Motor Original</label>
                                        <input type="text" name="MotorAntigo" class="form-control">
                                    </div>
                                </div>
                                <div class="row align-items-end">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label" id="transmicao-label">Transmissão</label>
                                        <input type="text" name="Transmicao" class="form-control">
                                        <label class="form-label mt-2" id="tipo-transmicao-label">Tipo de Transmissão</label>
                                        <select name="TipoTransmicao" class="form-select" id="tipo-transmicao-select">
                                            <option value="">Selecione</option>
                                            <option value="Manual">Manual</option>
                                            <option value="Automatico">Automático</option>
                                            <option value="Automatizado">Automatizado</option>
                                            <option value="CVT">CVT (Transmissão Continuamente Variável)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0" id="transmicao-antiga-group" style="display:none;">
                                        <label class="form-label">Transmissão Original</label>
                                        <input type="text" name="TransmicaoAntiga" class="form-control mb-2">
                                        <label class="form-label">Tipo de Transmissão Original</label>
                                        <select name="TipoTransmicaoOriginal" class="form-select">
                                            <option value="">Selecione</option>
                                            <option value="Manual">Manual</option>
                                            <option value="Automatico">Automático</option>
                                            <option value="Automatizado">Automatizado</option>
                                            <option value="CVT">CVT (Transmissão Continuamente Variável)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-2">Anunciar</button>
                    <div class="mt-3 text-center">
                        <a href="../../index.php" class="btn btn-secondary w-100">Retornar ao Menu Principal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Preview das imagens
    function previewImage(inputId, imgId) {
        const input = document.getElementById(inputId);
        const img = document.getElementById(imgId);
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                img.src = URL.createObjectURL(this.files[0]);
                img.style.display = 'block';
            } else {
                img.style.display = 'none';
            }
        });
    }
    previewImage('imagens', 'preview1');
    previewImage('imagem2', 'preview2');
    previewImage('imagem3', 'preview3');

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

    // Combustível: mostrar campo "Outro" se selecionado
    function toggleOutroCombustivel() {
        var select = document.getElementById('combustivel');
        var outro = document.getElementById('combustivel_outro');
        if (select.value === 'Outro') {
            outro.style.display = 'block';
            outro.required = true;
        } else {
            outro.style.display = 'none';
            outro.required = false;
        }
    }
    document.getElementById('combustivel').addEventListener('change', toggleOutroCombustivel);

    // Exibe/oculta campos de consumo conforme o tipo de combustível e kit GNV
    function toggleConsumoFields() {
        var combusSelect = document.getElementById('combustivel');
        var tipo = combusSelect.value;
        var consumoGroup = document.getElementById('consumo-group');
        var consumoInput = document.getElementById('consumo');
        var consumo2Input = document.getElementById('consumo2');
        var consumo2Group = consumo2Input ? consumo2Input.closest('.col-md-6.mb-3') : null;
        var consumo3Group = document.getElementById('consumo3-group');
        var consumo4Group = document.getElementById('consumo4-group');
        var consumoLabel = document.getElementById('consumo-label');
        var kitGNVGroup = document.getElementById('kit-gnv-group');
        var kitGNVCheckbox = document.getElementById('kit-gnv-checkbox');
        var consumoGNVGroup = document.getElementById('consumo_gnv_group');
        // Esconde consumo (km/l) se elétrico
        if (tipo === 'Elétrico') {
            if (consumoGroup) consumoGroup.style.display = 'none';
            if (consumoInput) {
                consumoInput.value = '';
                consumoInput.required = false;
            }
        } else {
            if (consumoGroup) consumoGroup.style.display = '';
            if (consumoInput) consumoInput.required = true;
        }
        // Consumo2: Flex ou Híbrido
        if (tipo === 'Flex' || tipo === 'Híbrido') {
            if (consumo2Group) consumo2Group.style.display = '';
            if (consumoLabel) consumoLabel.textContent = 'Consumo Gasolina (km/l):';
        } else {
            if (consumo2Group) consumo2Group.style.display = 'none';
            if (consumo2Input) consumo2Input.value = '';
            if (consumoLabel) consumoLabel.textContent = 'Consumo (km/l):';
        }
        // Consumo3: Híbrido ou Elétrico
        if (tipo === 'Híbrido' || tipo === 'Elétrico') {
            if (consumo3Group) consumo3Group.style.display = '';
        } else {
            if (consumo3Group) consumo3Group.style.display = 'none';
            var consumo3Input = document.getElementById('consumo3');
            if (consumo3Input) consumo3Input.value = '';
        }
        // Consumo4: Apenas Híbrido
        if (tipo === 'Híbrido') {
            if (consumo4Group) consumo4Group.style.display = '';
        } else {
            if (consumo4Group) consumo4Group.style.display = 'none';
            var consumo4Input = document.getElementById('consumo4');
            if (consumo4Input) consumo4Input.value = '';
        }
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
    }
    if (document.getElementById('kit-gnv-checkbox')) {
        document.getElementById('kit-gnv-checkbox').addEventListener('change', toggleConsumoFields);
    }
    document.getElementById('combustivel').addEventListener('change', toggleConsumoFields);
    toggleConsumoFields();

    // Swamp de Transmissão: se marcado, mostra Transmissão Original e renomeia Transmissão para Transmissão Atual
    var swampTransmicaoInput = document.getElementById('swamp-transmicao-input');
    var transmicaoAntigaGroup = document.getElementById('transmicao-antiga-group');
    var transmicaoLabel = document.getElementById('transmicao-label');
    var tipoTransmicaoLabel = document.getElementById('tipo-transmicao-label');
    function toggleTransmicaoSwap() {
        if (swampTransmicaoInput && swampTransmicaoInput.checked) {
            if (transmicaoAntigaGroup) transmicaoAntigaGroup.style.display = '';
            if (transmicaoLabel) transmicaoLabel.textContent = 'Transmissão Atual';
            if (tipoTransmicaoLabel) tipoTransmicaoLabel.textContent = 'Tipo de Transmissão Atual';
        } else {
            if (transmicaoAntigaGroup) transmicaoAntigaGroup.style.display = 'none';
            if (transmicaoLabel) transmicaoLabel.textContent = 'Transmissão';
            if (tipoTransmicaoLabel) tipoTransmicaoLabel.textContent = 'Tipo de Transmissão';
        }
    }
    if (swampTransmicaoInput) {
        swampTransmicaoInput.addEventListener('change', toggleTransmicaoSwap);
        toggleTransmicaoSwap();
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
    </script>
</body>
</html>
