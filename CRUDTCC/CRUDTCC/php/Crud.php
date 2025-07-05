<?php
session_start();
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
    <title>Cadastro de Carros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/crud.css" rel="stylesheet">
</head>
<body style="background: url('/CRUDTCC/images/Background.png') no-repeat center center fixed; background-size: cover;">
    <?php include_once("../header.php"); ?>
    <div class="container mt-4" style="max-width: 700px;">
        <div class="card mx-auto" style="border-radius: 18px; background: rgba(255,255,245,0.97);">
            <div class="card-header text-center">
                <h1>Cadastro de Carros</h1>
                <small class="d-block mt-1">Campos com <span style="color:#d00">*</span> são obrigatórios</small>
            </div>
            <div class="card-body">
                <form id="carForm" action="processaCadastro.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="mb-3">
                        <label for="nome" class="form-label required">Nome do Carro:</label>
                        <input type="text" id="nome" name="nome" class="form-control" required placeholder="Ex: Corolla Altis 2.0 Flex">
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label required">Descrição do Carro:</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3" required placeholder="Breve descrição do modelo, versão, diferenciais..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="motores" class="form-label">
                            Motor(es):
                            <span 
                                style="cursor:help;color:#888;" 
                                title="Informe o(s) motor(es) disponíveis para este modelo, por exemplo: 2.0 16V Flex, 1.8 Híbrido, etc.">
                                &#x3f;
                            </span>
                        </label>
                        <textarea id="motores" name="motores" class="form-control" rows="2" placeholder="Ex: 2.0 16V Flex, 1.8 Híbrido, etc."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="CarFabIni" class="form-label required">Ano de Fabricação (Início):</label>
                            <input type="number" id="CarFabIni" name="ano_inicio" class="form-control" min="1900" max="2100" required placeholder="Ex: 2015">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="CarFabFim" class="form-label required">Ano de Fabricação (Fim):</label>
                            <input type="number" id="CarFabFim" name="ano_fim" class="form-control" min="1900" max="2100" required placeholder="Ex: 2022">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label required">Categoria do Carro:</label>
                            <select id="categoria" name="categoria" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="1">Antiguidade</option>
                                <option value="2">Sedan</option>
                                <option value="3">SUV</option>
                                <option value="4">Hatch</option>
                                <option value="5">UTE/Picape</option>
                                <option value="6">Elétrico</option>
                                <option value="7">Coupé</option>
                                <option value="8">Híbrido</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria2" class="form-label">Categoria Secundária:</label>
                            <select id="categoria2" name="categoria2" class="form-select">
                                <option value="">Nenhuma</option>
                                <option value="1">Antiguidade</option>
                                <option value="2">Sedan</option>
                                <option value="3">SUV</option>
                                <option value="4">Hatch</option>
                                <option value="5">UTE/Picape</option>
                                <option value="6">Elétrico</option>
                                <option value="7">Coupé</option>
                                <option value="8">Híbrido</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="categoria3-group" style="display:none;">
                        <label for="categoria3" class="form-label">Categoria Terciária:</label>
                        <select id="categoria3" name="categoria3" class="form-select">
                            <option value="">Nenhuma</option>
                            <option value="1">Antiguidade</option>
                            <option value="2">Sedan</option>
                            <option value="3">SUV</option>
                            <option value="4">Hatch</option>
                            <option value="5">UTE/Picape</option>
                            <option value="6">Elétrico</option>
                            <option value="7">Coupé</option>
                            <option value="8">Híbrido</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="geracao" class="form-label required">Geração:</label>
                            <select id="geracao" name="geracao" class="form-select" required>
                                <option value="">Selecione a geração</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?= $i ?>ª Geração"><?= $i ?>ª Geração</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nome_geracao" class="form-label">Nome da Geração:</label>
                            <input type="text" id="nome_geracao" name="nome_geracao" class="form-control" placeholder="Ex: E210, New Civic...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="marca" class="form-label required">Marca do Carro:</label>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Preço FIPE:</label>
                            <input type="number" id="valor" name="valor" class="form-control" step="0.01" required placeholder="Ex: 95000.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Preço de Manutenção (PDM):</label>
                            <textarea id="pdm" name="pdm" class="form-control" rows="2" required placeholder="Ex: 3500/ano, detalhes, observações..."></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:normal;color:#555;">
                                Caso o carro tenha mais de uma opção de combustível selecione "Híbrido" ou "Flex"
                            </label>
                            <label class="form-label required">Tipo de Combustível:</label>
                            <select name="CarCombus" class="form-select" required id="CarCombus-select">
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
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo de Direção</label>
                            <select name="CarDire" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Hidráulica">Hidráulica</option>
                                <option value="Elétrica">Elétrica</option>
                                <option value="Mecânica">Mecânica</option>
                                <option value="Eletro-Hidráulica">Eletro-Hidráulica</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="consumo-group">
                            <label for="consumo" class="form-label required" id="consumo-label">Consumo (km/l):</label>
                            <input type="number" id="consumo" name="consumo" class="form-control" step="0.01" min="0" required placeholder="Ex: 12.5">
                        </div>
                        <div class="col-md-6 mb-3" id="consumo2-group">
                            <label class="form-label" for="consumo2">Consumo Etanol (km/l):</label>
                            <input type="number" id="consumo2" name="consumo2" class="form-control" step="0.01" min="0" placeholder="Ex: 8.5">
                        </div>
                        <div class="col-md-6 mb-3" id="consumoGnv-group" style="display:none;">
                            <label class="form-label" for="consumoGnv">Consumo (GNV) (km/m³):</label>
                            <input type="number" id="consumoGnv" name="consumoGNV" class="form-control" step="0.01" min="0" placeholder="Ex: 14.2">
                        </div>
                        <div class="col-md-6 mb-3" id="consumoDiesel-group" style="display:none;">
                            <label class="form-label" for="consumoDiesel">Consumo Diesel (km/l):</label>
                            <input type="number" id="consumoDiesel" name="consumoDiesel" class="form-control" step="0.01" min="0" placeholder="Ex: 15.0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="consumo3-group" style="display:none;">
                            <label class="form-label" for="consumo3">Autonomia Elétrica (km):</label>
                            <input type="number" id="consumo3" name="consumo3" class="form-control" step="0.1" min="0" placeholder="Ex: 350">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label required">Nível de Conforto:</label>
                            <div class="mb-2">
                                <span id="conforto-value" style="font-size:1.2em;font-weight:bold;"></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="range" id="conforto" name="CarConfort" min="1" max="5" value="1" step="1" class="form-range" style="flex:1;">
                            </div>
                            <div id="conforto-desc" style="margin-top:7px;font-size:0.98rem;color:#555;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label required">Nível de Esportividade:</label>
                            <div class="mb-2">
                                <span id="exportividade-value" style="font-size:1.2em;font-weight:bold;"></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="range" id="exportividade" name="CarSport" min="1" max="5" value="1" step="1" class="form-range" style="flex:1;">
                            </div>
                            <div id="exportividade-desc" style="margin-top:7px;font-size:0.98rem;color:#555;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="history" class="form-label required">História:</label>
                        <textarea id="history" name="history" class="form-control" rows="6" required placeholder="Conte a história do modelo, curiosidades, evolução..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imagens" class="form-label required">Imagem do Exterior do Carro:</label>
                        <input type="file" id="imagens" name="imagens[]" class="form-control" accept="image/*" required>
                        <img id="preview1" class="img-preview" style="display:none; max-width:120px; height:auto;">
                    </div>
                    <div class="mb-3">
                        <label for="imagem2" class="form-label required">Imagem do Painel:</label>
                        <input type="file" id="imagem2" name="imagem2" class="form-control" accept="image/*" required>
                        <img id="preview2" class="img-preview" style="display:none; max-width:120px; height:auto;">
                    </div>
                    <div class="mb-3">
                        <label for="imagem3" class="form-label required">Imagem dos Bancos:</label>
                        <input type="file" id="imagem3" name="imagem3" class="form-control" accept="image/*" required>
                        <img id="preview3" class="img-preview" style="display:none; max-width:120px; height:auto;">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 mt-2">Cadastrar</button>
                        <div class="mt-3 text-center">
                            <a href="../index.php" class="btn btn-secondary w-100">Retornar ao Menu Principal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mostrar categoria terciária só se categoria2 for selecionada
    document.getElementById('categoria2').addEventListener('change', function() {
        document.getElementById('categoria3-group').style.display = this.value ? 'block' : 'none';
    });

    // Impedir seleção duplicada de categorias
    function updateCategoryOptions() {
        const cat1 = document.getElementById('categoria').value;
        const cat2 = document.getElementById('categoria2').value;
        const cat3 = document.getElementById('categoria3').value;
        // Para cada select, desabilite as opções já escolhidas nos outros
        ['categoria', 'categoria2', 'categoria3'].forEach(function(id) {
            const select = document.getElementById(id);
            if (!select) return;
            Array.from(select.options).forEach(function(opt) {
                if (opt.value && (opt.value === cat1 || opt.value === cat2 || opt.value === cat3) && select.value !== opt.value) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });
    }
    ['categoria', 'categoria2', 'categoria3'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', updateCategoryOptions);
    });

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

    // Exibe/oculta campos de consumo conforme o tipo de combustível
    function toggleConsumoFields() {
        var combusSelect = document.querySelector('select[name="CarCombus"]');
        if (!combusSelect) return;
        var tipo = combusSelect.value;
        var consumoGroup = document.getElementById('consumo-group');
        var consumo2Input = document.getElementById('consumo2');
        var consumo2Group = consumo2Input ? consumo2Input.closest('.col-md-6.mb-3') : null;
        var consumo3Group = document.getElementById('consumo3-group');
        var consumoGnvGroup = document.getElementById('consumoGnv-group');
        var consumoDieselGroup = document.getElementById('consumoDiesel-group');

        // Sempre oculta todos primeiro
        if (consumoGroup) consumoGroup.style.display = 'none';
        if (consumo2Group) consumo2Group.style.display = 'none';
        if (consumo3Group) consumo3Group.style.display = 'none';
        if (consumoGnvGroup) consumoGnvGroup.style.display = 'none';
        if (consumoDieselGroup) consumoDieselGroup.style.display = 'none';

        // Exibe conforme o tipo
        if (tipo === 'Híbrido') {
            if (consumoGroup) consumoGroup.style.display = '';
            if (consumo2Group) consumo2Group.style.display = '';
            if (consumo3Group) consumo3Group.style.display = '';
            if (consumoGnvGroup) consumoGnvGroup.style.display = '';
            if (consumoDieselGroup) consumoDieselGroup.style.display = '';
        } else if (tipo === 'Flex') {
            if (consumoGroup) consumoGroup.style.display = '';
            if (consumo2Group) consumo2Group.style.display = '';
        } else if (tipo === 'Elétrico') {
            if (consumo3Group) consumo3Group.style.display = '';
        } else if (tipo === 'GNV') {
            if (consumoGnvGroup) consumoGnvGroup.style.display = '';
        } else if (tipo === 'Diesel') {
            if (consumoDieselGroup) consumoDieselGroup.style.display = '';
        } else if (tipo === 'Etanol' || tipo === 'Gasolina' || tipo === 'Outro') {
            if (consumoGroup) consumoGroup.style.display = '';
        }
        // Garante que o campo GNV apareça para Híbrido
        if (tipo === 'Híbrido' && consumoGnvGroup) consumoGnvGroup.style.display = '';
    }
    window.addEventListener('DOMContentLoaded', function() {
        var combusSelect = document.querySelector('select[name="CarCombus"]');
        if (combusSelect) {
            combusSelect.addEventListener('change', toggleConsumoFields);
            toggleConsumoFields();
        }
    });

    // Adiciona obrigatoriedade dinâmica aos campos de consumo
function toggleRequiredConsumoFields() {
  var combusSelect = document.querySelector('select[name="CarCombus"]');
  var tipo = combusSelect ? combusSelect.value : '';
  var consumo = document.getElementById('consumo');
  var consumoGroup = document.getElementById('consumo-group');
  var consumoLabel = document.getElementById('consumo-label');
  var consumo2 = document.getElementById('consumo2');
  var consumo3 = document.getElementById('consumo3');
  var consumo4 = document.getElementById('consumoGnv');
  var consumoGnvGroup = document.getElementById('consumoGnv-group');
  var consumoDieselGroup = document.getElementById('consumoDiesel-group');

  // Se Híbrido, nenhum campo de consumo é obrigatório, mas todos aparecem
  if (tipo === 'Híbrido') {
    if (consumo) consumo.required = false;
    if (consumo2) consumo2.required = false;
    if (consumo3) consumo3.required = false;
    if (consumo4) consumo4.required = false;
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
      if (consumo2Group) consumo2Group.style.display = '';
      if (consumo2) consumo2.required = true;
    } else {
      if (consumo2Group) consumo2Group.style.display = 'none';
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
    if (consumo4) consumo4.required = false;
    if (consumoGnvGroup) consumoGnvGroup.style.display = 'none';
    if (consumo4) consumo4.required = false;
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
    <script src="/CRUDTCC/script/consumo_campos.js"></script>
</body>
</html>
