<?php
include __DIR__ . '/../../config.php';
include __DIR__ . '/../../mysqlexecuta.php';

$con = conectar();
$imageDir = '/images/Carimg/';

// Características disponíveis para filtro
$caracteristicas = [
  'ano' => 'Ano',
  'carmarca' => 'Marca',
  'CarCombus' => 'Combustível',
  'CarDire' => 'Direção',
  'CarConfort' => 'Conforto',
  'CarSport' => 'Esportividade'
];
$caracteristica = isset($_GET['caracteristica']) ? $_GET['caracteristica'] : 'ano';
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : '';
$autonomia = isset($_GET['autonomia']) ? trim($_GET['autonomia']) : '';

$where = [];
if (isset($_GET['ano']) && $_GET['ano'] !== '') {
    $ano = (int)$_GET['ano'];
    $where[] = "CarFabIn <= $ano AND CarFabFim >= $ano";
}
if (isset($_GET['carmarca']) && $_GET['carmarca'] !== '') {
    $marcaEsc = addslashes($_GET['carmarca']);
    $where[] = "carmarca = '$marcaEsc'";
}
if (isset($_GET['CarCombus']) && $_GET['CarCombus'] !== '') {
    $combEsc = addslashes($_GET['CarCombus']);
    $where[] = "CarCombus = '$combEsc'";
}
if (isset($_GET['CarConfort']) && $_GET['CarConfort'] !== '') {
    $conf = (int)$_GET['CarConfort'];
    $where[] = "CarConfort >= $conf";
}
if (isset($_GET['CarSport']) && $_GET['CarSport'] !== '') {
    $esp = (int)$_GET['CarSport'];
    $where[] = "CarSport >= $esp";
}
if (isset($_GET['CarDire']) && $_GET['CarDire'] !== '') {
    $dirEsc = addslashes($_GET['CarDire']);
    $where[] = "CarDire = '$dirEsc'";
}
// Monta SQL final
$sql = "SELECT carimg, carnome FROM car";
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$res = mysqlexecuta($con, $sql);

$marcasCrud = [
    "Toyota", "Ford", "Chevrolet", "Honda", "BMW", "Mercedes", "Volkswagen", "Hyundai", "Nissan", "Kia",
    "Fiat", "Renault", "Peugeot", "Citroën", "Subaru", "Mitsubishi", "Land Rover", "Porsche", "Lamborghini", "Ferrari",
    "Audi", "Volvo", "Mazda", "Chrysler", "Dodge", "Jeep", "Buick", "GMC", "Tesla", "Jaguar", "Infiniti", "Acura", "Lincoln",
    "Alfa Romeo", "Maserati", "Aston Martin", "Bentley", "Rolls Royce", "Bugatti", "McLaren", "Pagani", "Koenigsegg", "Lotus",
    "Lexus", "Genesis", "Scion", "Smart"
];
$confortoCrud = [1, 2, 3, 4, 5];
$esportividadeCrud = [1, 2, 3, 4, 5];
$confortoLabels = [1 => "Básico", 2 => "Intermediário", 3 => "Confortável", 4 => "Luxuoso", 5 => "Premium/Executivo"];
$esportividadeLabels = [1 => "Básico", 2 => "Intermediário", 3 => "Esportivo", 4 => "Muito Esportivo", 5 => "Extremo"];
$combustiveisCrud = ["Gasolina", "Etanol", "Diesel", "Flex", "Elétrico", "Híbrido"];
$direcoesCrud = ["Mecânica", "Hidráulica", "Elétrica", "Eletro-Hidráulica"];

function nivelTexto($valor) {
    $valor = (int)$valor;
    if ($valor <= 3) return "Básico";
    if ($valor <= 7) return "Intermediário";
    return "Avançado";
}

function confortoLabelDesc($valor) {
    $labels = [
        1 => "Básico",
        2 => "Intermediário",
        3 => "Confortável",
        4 => "Luxuoso",
        5 => "Premium/Executivo"
    ];
    $descs = [
        1 => "Básico – Carros com bancos simples, pouca ou nenhuma tecnologia de suspensão avançada, ruído interno perceptível e poucos recursos de conforto, como ar-condicionado básico e acabamento simples.",
        2 => "Intermediário – Veículos com bancos um pouco mais ergonômicos, ar-condicionado eficiente, suspensão melhor ajustada e menos ruído interno. Podem incluir alguns extras como direção elétrica ou regulagem de altura do banco.",
        3 => "Confortável – Modelos com bancos mais anatômicos e materiais melhores, suspensão bem ajustada para reduzir impactos, isolamento acústico aprimorado e recursos como ar-condicionado digital, piloto automático e mais ajustes elétricos nos bancos.",
        4 => "Luxuoso – Carros com bancos de couro, climatização individual, excelente isolamento acústico, suspensão adaptativa, sistema multimídia sofisticado e recursos como bancos aquecidos, ventilados e com ajustes automáticos.",
        5 => "Premium/Executivo – O nível máximo de conforto, encontrado em sedãs de alto padrão e SUVs de luxo. Aqui há bancos com função de massagem, suspensão a ar, silêncio absoluto na cabine, acabamento refinado, materiais nobres e tecnologias avançadas para o bem-estar dos passageiros."
    ];
    $v = intval($valor);
    return [
        'label' => $labels[$v] ?? '',
        'desc' => $descs[$v] ?? ''
    ];
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesquisar Carros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/header.css" rel="stylesheet">
    <style>
      body.dark-mode {
        background: url('/CRUDTCC/images/BackgroundDM.jpg') center center/cover no-repeat fixed !important;
        color: #f5f4ec;
      }
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
      body.dark-mode .card-title {
        color: #ffe066 !important;
        text-shadow: 0 2px 8px #0008;
      }
      body.dark-mode .card-body {
        background: #2c2f34 !important;
        color: #f5f4ec !important;
        border-top: 1px solid #444;
      }
      body.dark-mode .card img {
        filter: brightness(0.88);
      }
      .card:hover { transform: scale(1.03); box-shadow:0 4px 18px rgba(0,0,0,0.13); }
      .main-box { animation: fadein 0.7s; }
      @keyframes fadein { from { opacity:0; transform:translateY(30px);} to { opacity:1; transform:translateY(0);} }
      @media (max-width: 576px) {
        .main-box { padding:8px; }
        .card-img-top { height:120px!important; }
      }
      .filtro-card {
        background: rgba(255,255,255,0.92);
        border-radius: 16px;
        box-shadow: 0 2px 16px #0001;
        padding: 18px 18px 10px 18px;
        margin-bottom: 32px;
        max-width: 950px;
      }
      body.dark-mode .filtro-card {
        background: rgba(44,43,40,0.98);
        box-shadow: 0 2px 16px #0006;
      }
      .filtro-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 14px 18px;
        align-items: end;
      }
      .filtro-card label {
        font-size: 0.98rem;
        font-weight: 600;
        color: #232323;
        margin-bottom: 4px;
      }
      body.dark-mode .filtro-card label {
        color: #ffe066;
      }
      .filtro-card .form-control, .filtro-card .form-select {
        font-size: 1rem;
        border-radius: 8px;
        border: 1.5px solid #bfa13a;
        background: #fffbe6;
        color: #232323;
      }
      body.dark-mode .filtro-card .form-control, body.dark-mode .filtro-card .form-select {
        background: #23272a;
        color: #ffe066;
        border: 1.5px solid #ffe066;
      }
      .filtro-card .btn-primary {
        font-weight: bold;
        font-size: 1.08rem;
        padding: 8px 28px;
        border-radius: 8px;
        background: linear-gradient(90deg,#ffe066 60%,#bfa13a 100%);
        color: #232323;
        border: none;
        box-shadow: 0 2px 8px #bfa13a33;
        margin-top: 8px;
      }
      .filtro-card .btn-primary:hover {
        background: linear-gradient(90deg,#fffbe6 60%,#ffe066 100%);
        color: #bfa13a;
      }
      @media (max-width: 900px) {
        .filtro-card { padding: 10px 4vw 6px 4vw; }
        .filtro-grid { grid-template-columns: 1fr 1fr; }
      }
      @media (max-width: 600px) {
        .filtro-grid { grid-template-columns: 1fr; }
      }
    </style>
  </head>
  <body>
    <?php include_once(__DIR__ . '/../../header.php'); ?>
    <div class="main-box" style="background:rgba(255,255,220,0.92);border-radius:18px;margin:24px 0 0 0;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:32px 0 48px 0;min-height:unset;">
      <h1 class="text-center mb-4" style="font-weight:700;letter-spacing:1px;">Pesquisar Carros</h1>
      <div class="container mb-4 filtro-card">
        <button type="button" id="btnMostrarFiltros" class="btn btn-primary w-100 mb-3" style="font-size:1.1rem;">Mostrar filtros</button>
        <form method="get" id="filtroForm" class="filtro-grid" autocomplete="off" style="display:none;">
          <div class="text-end" style="grid-column:1/-1;">
            <button type="button" id="btnOcultarFiltros" class="btn btn-secondary mb-2">Ocultar filtros</button>
          </div>
          <div>
            <label for="ano" class="form-label mb-0">Ano:</label>
            <input type="number" name="ano" id="ano" class="form-control" placeholder="Ex: 2010" value="<?= isset($_GET['ano']) ? htmlspecialchars($_GET['ano']) : '' ?>" min="1900" max="2100">
          </div>
          <div>
            <label for="carmarca" class="form-label mb-0">Marca:</label>
            <select name="carmarca" id="carmarca" class="form-select">
              <option value="">Todas</option>
              <?php foreach($marcasCrud as $marca): ?>
                <option value="<?= $marca ?>" <?= (isset($_GET['carmarca']) && $_GET['carmarca']==$marca)?'selected':'' ?>><?= $marca ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="CarCombus" class="form-label mb-0">Combustível:</label>
            <select name="CarCombus" id="CarCombus" class="form-select">
              <option value="">Todos</option>
              <?php foreach($combustiveisCrud as $comb): ?>
                <option value="<?= $comb ?>" <?= (isset($_GET['CarCombus']) && $_GET['CarCombus']==$comb)?'selected':'' ?>><?= $comb ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="CarConfort" class="form-label mb-0">Conforto:</label>
            <select name="CarConfort" id="CarConfort" class="form-select">
              <option value="">Todos</option>
              <?php foreach($confortoCrud as $c): ?>
                <option value="<?= $c ?>" <?= (isset($_GET['CarConfort']) && $_GET['CarConfort']==$c)?'selected':'' ?>><?= $confortoLabels[$c] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="CarSport" class="form-label mb-0">Esportividade:</label>
            <select name="CarSport" id="CarSport" class="form-select">
              <option value="">Todos</option>
              <?php foreach($esportividadeCrud as $e): ?>
                <option value="<?= $e ?>" <?= (isset($_GET['CarSport']) && $_GET['CarSport']==$e)?'selected':'' ?>><?= $esportividadeLabels[$e] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="CarDire" class="form-label mb-0">Direção:</label>
            <select name="CarDire" id="CarDire" class="form-select">
              <option value="">Todas</option>
              <?php foreach($direcoesCrud as $dir): ?>
                <option value="<?= $dir ?>" <?= (isset($_GET['CarDire']) && $_GET['CarDire']==$dir)?'selected':'' ?>><?= $dir ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="text-center" style="grid-column:1/-1;">
            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
            <a href="?" class="btn btn-outline-danger mt-2 ms-2">Limpar</a>
          </div>
        </form>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <?php if ($res->rowCount() == 0): ?>
            <p class="text-center">Nenhum carro encontrado.</p>
          <?php endif; ?>
          <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
            <?php 
              $carName = $row['carnome'];
              $carImg = $row['carimg'];
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center mb-4">
              <div class="card" style="width:100%;max-width:260px;min-width:220px;box-shadow:0 2px 10px rgba(0,0,0,0.10);border-radius:10px;overflow:hidden;transition:transform 0.15s;">
                <a href="Carro.php?nome=<?= urlencode($carName) ?>" style="display:block;">
                  <?php if (!empty($carImg) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageDir . $carImg)): ?>
                    <img src="<?= $imageDir . $carImg ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
                  <?php else: ?>
                    <img src="/images/default.jpg" class="card-img-top" alt="Imagem não disponível" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
                  <?php endif; ?>
                </a>
                <div class="card-body text-center p-2" style="background:#fff;">
                  <h5 class="card-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;margin:0 auto;font-size:1.1rem;font-weight:600;">
                    <?= htmlspecialchars($carName) ?>
                  </h5>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Dark mode toggle
      const btn = document.getElementById('toggle-dark');
      if(btn) {
        btn.onclick = function() {
          document.body.classList.toggle('dark-mode');
          localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode') ? '1' : '');
        };
      }
      // Persist dark mode
      if (localStorage.getItem('dark-mode')) {
        document.body.classList.add('dark-mode');
      }
      function mostrarCampoAutonomia() {
        var comb = document.getElementById('combustivel');
        var campo = document.getElementById('campoAutonomia');
        if (comb && campo) {
          if (comb.value) {
            campo.style.display = 'inline-block';
          } else {
            campo.style.display = 'none';
            campo.value = '';
          }
        }
      }
      document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnMostrarFiltros');
        var form = document.getElementById('filtroForm');
        var btnOcultar = document.getElementById('btnOcultarFiltros');
        // Se algum filtro estiver preenchido, mostrar o formulário direto
        var algumPreenchido = false;
        ['ano','carmarca','CarCombus','CarConfort','CarSport','CarDire'].forEach(function(id){
          var el = document.getElementById(id);
          if(el && el.value && el.value !== '') algumPreenchido = true;
        });
        if(algumPreenchido) {
          form.style.display = 'grid';
          btn.style.display = 'none';
        } else {
          form.style.display = 'none';
          btn.style.display = 'block';
        }
        btn.onclick = function() {
          form.style.display = 'grid';
          btn.style.display = 'none';
        };
        if(btnOcultar) {
          btnOcultar.onclick = function() {
            form.style.display = 'none';
            btn.style.display = 'block';
          };
        }
      });
    </script>
  </body>
</html>
