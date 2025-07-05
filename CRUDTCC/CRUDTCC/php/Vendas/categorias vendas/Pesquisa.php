<?php
include '../../config.php'; // garantir que conectar() retorna sempre PDO/SQLite
// NÃO incluir mysqlexecuta.php ou qualquer arquivo que defina MySQLi

$con = conectar();
$imageDir = '../../Imagens/';

// Opções de filtro (adaptadas para veiculos_venda)
$marcasCrud = [
    "Toyota", "Ford", "Chevrolet", "Honda", "BMW", "Mercedes", "Volkswagen", "Hyundai", "Nissan", "Kia",
    "Fiat", "Renault", "Peugeot", "Citroën", "Subaru", "Mitsubishi", "Land Rover", "Porsche", "Lamborghini", "Ferrari",
    "Audi", "Volvo", "Mazda", "Chrysler", "Dodge", "Jeep", "Buick", "GMC", "Tesla", "Jaguar", "Infiniti", "Acura", "Lincoln",
    "Alfa Romeo", "Maserati", "Aston Martin", "Bentley", "Rolls Royce", "Bugatti", "McLaren", "Pagani", "Koenigsegg", "Lotus",
    "Lexus", "Genesis", "Scion", "Smart"
];
$confortoCrud = [1, 2, 3, 4, 5];
$confortoLabels = [1 => "Básico", 2 => "Intermediário", 3 => "Confortável", 4 => "Luxuoso", 5 => "Premium/Executivo"];
$combustiveisCrud = ["Gasolina", "Etanol", "Diesel", "Flex", "Elétrico", "Híbrido"];
$direcoesCrud = ["Mecânica", "Hidráulica", "Elétrica", "Eletro-Hidráulica"];
$transmissoesCrud = ["Manual", "Automático", "Automatizado", "CVT"];

// Características disponíveis para filtro (adiciona transmissão)
$caracteristicas = [
  'ano' => 'Ano',
  'marca' => 'Marca',
  'CarCombus' => 'Combustível',
  'CarDire' => 'Direção',
  'CarConfort' => 'Conforto',
  'TipoTransmicao' => 'Transmissão'
];
$caracteristica = isset($_GET['caracteristica']) ? $_GET['caracteristica'] : 'ano';
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : '';
$autonomia = isset($_GET['autonomia']) ? trim($_GET['autonomia']) : '';
$ano_inicio = isset($_GET['ano_inicio']) ? trim($_GET['ano_inicio']) : '';
$ano_fim = isset($_GET['ano_fim']) ? trim($_GET['ano_fim']) : '';
$tipo_ano = isset($_GET['tipo_ano']) ? $_GET['tipo_ano'] : 'intervalo';
$ano_exato = isset($_GET['ano_exato']) ? trim($_GET['ano_exato']) : '';

// Monta SQL dinâmico para multifiltro
$sql = "SELECT imagem_principal, nome FROM veiculos_venda";
$where = [];
if ($ano_inicio !== '') {
    $ano_inicio_esc = $con->quote($ano_inicio);
    $where[] = "ano >= $ano_inicio_esc";
}
if ($ano_fim !== '') {
    $ano_fim_esc = $con->quote($ano_fim);
    $where[] = "ano <= $ano_fim_esc";
}
if (isset($_GET['marca']) && $_GET['marca'] !== '') {
    $marcaEsc = $con->quote($_GET['marca']);
    $where[] = "marca = $marcaEsc";
}
if (isset($_GET['combustivel']) && $_GET['combustivel'] !== '') {
    $comb = $_GET['combustivel'];
    $combEsc = $con->quote($comb);
    $autonomia = isset($_GET['autonomia']) ? trim($_GET['autonomia']) : '';
    if ($autonomia !== '') {
        $autonomiaEscaped = $con->quote($autonomia);
        $combLower = strtolower($comb);
        if (strpos($combLower, 'flex') !== false) {
            $where[] = "(CarCombus LIKE '%Flex%' AND ((CAST(REPLACE(REPLACE(Consumo, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped) OR (CAST(REPLACE(REPLACE(Consumo2, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped)))";
        } elseif (strpos($combLower, 'híbrido') !== false || strpos($combLower, 'hibrido') !== false) {
            $where[] = "(CarCombus LIKE '%Híbrido%' OR CarCombus LIKE '%Hibrido%') AND ((CAST(REPLACE(REPLACE(Consumo, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped) OR (CAST(REPLACE(REPLACE(Consumo2, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped) OR (CAST(REPLACE(REPLACE(Consumo3, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped))";
        } elseif (strpos($combLower, 'elétrico') !== false || strpos($combLower, 'eletrico') !== false) {
            $where[] = "(CarCombus LIKE '%Elétrico%' OR CarCombus LIKE '%Eletrico%') AND (CAST(REPLACE(REPLACE(Consumo3, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped)";
        } elseif (strpos($combLower, 'etanol') !== false) {
            $where[] = "CarCombus LIKE '%Etanol%' AND (CAST(REPLACE(REPLACE(Consumo2, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped)";
        } else {
            $where[] = "CarCombus LIKE $combEsc AND (CAST(REPLACE(REPLACE(Consumo, ',', '.'), ' ', '') AS DECIMAL(10,2)) >= $autonomiaEscaped)";
        }
    } else {
        $where[] = "CarCombus LIKE $combEsc";
    }
}
if (isset($_GET['conforto']) && $_GET['conforto'] !== '') {
    $confortoEsc = $con->quote($_GET['conforto']);
    $where[] = "CarConfort >= $confortoEsc";
}
if (isset($_GET['direcao']) && $_GET['direcao'] !== '') {
    $direcaoEsc = $con->quote($_GET['direcao']);
    $where[] = "CarDire = $direcaoEsc";
}
if (isset($_GET['transmissao']) && $_GET['transmissao'] !== '') {
    $transEsc = $con->quote($_GET['transmissao']);
    $where[] = "TipoTransmicao = $transEsc";
}
// --- BACKEND: aplicar filtro de valor na query SQL ---
$tipo_valor = isset($_GET['tipo_valor']) ? $_GET['tipo_valor'] : 'intervalo';
$valor_min = isset($_GET['valor_min']) ? trim($_GET['valor_min']) : '';
$valor_max = isset($_GET['valor_max']) ? trim($_GET['valor_max']) : '';
$valor_exato = isset($_GET['valor_exato']) ? trim($_GET['valor_exato']) : '';
if ($tipo_valor === 'exato' && $valor_exato !== '') {
    $valor_exato_esc = $con->quote($valor_exato);
    $where[] = "preco = $valor_exato_esc";
} elseif ($tipo_valor === 'intervalo') {
    if ($valor_min !== '') {
        $valor_min_esc = $con->quote($valor_min);
        $where[] = "preco >= $valor_min_esc";
    }
    if ($valor_max !== '') {
        $valor_max_esc = $con->quote($valor_max);
        $where[] = "preco <= $valor_max_esc";
    }
}
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$res = $con->query($sql);
// Tema via GET
$tema = isset($_GET['tema']) ? $_GET['tema'] : '';
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesquisar Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/pesquisa.css" rel="stylesheet">
    <style>
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
  <body<?php if ($tema === 'dark') { echo ' class=\"dark-mode\"'; } elseif ($tema === 'light') { echo ' class=\"light-mode\"'; } ?>>
    <?php include_once("header.php"); ?>
    <div class="main-box" style="background:rgba(255,255,220,0.92);border-radius:18px;margin:24px 0 0 0;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:32px 0 48px 0;min-height:unset;">
      <h1 class="text-center mb-4" style="font-weight:700;letter-spacing:1px;">Pesquisar Veículos</h1>
      <div class="container mb-4 filtro-card">
        <button type="button" id="btnMostrarFiltros" class="btn btn-primary w-100 mb-3" style="font-size:1.1rem;">Mostrar filtros</button>
        <form method="get" id="filtroForm" autocomplete="off" style="display:none;">
          <div class="d-flex justify-content-between mb-3">
            <button type="button" id="btnOcultarFiltros" class="btn btn-secondary" style="min-width:120px;">Ocultar filtros</button>
            <button type="button" id="btnLimparFiltros" class="btn btn-outline-danger" style="min-width:120px;">Limpar filtros</button>
          </div>
          <div class="row g-2 align-items-end mb-2">
            <div class="col-md-3 col-12">
              <label for="tipo_ano" class="form-label mb-0">Filtrar ano por:</label>
              <select name="tipo_ano" id="tipo_ano" class="form-select">
                <option value="intervalo" <?= $tipo_ano==='intervalo'?'selected':'' ?>>Intervalo</option>
                <option value="exato" <?= $tipo_ano==='exato'?'selected':'' ?>>Ano exato</option>
              </select>
            </div>
            <div class="col-md-3 col-6" id="campo-intervalo" style="display:<?= $tipo_ano==='intervalo'?'block':'none' ?>;">
              <label for="ano_inicio" class="form-label mb-0">Ano (de):</label>
              <input type="number" name="ano_inicio" id="ano_inicio" class="form-control" placeholder="Inicial" value="<?= htmlspecialchars($ano_inicio) ?>" min="1900" max="2100" autocomplete="off">
            </div>
            <div class="col-md-3 col-6" id="campo-intervalo-fim" style="display:<?= $tipo_ano==='intervalo'?'block':'none' ?>;">
              <label for="ano_fim" class="form-label mb-0">Ano (até):</label>
              <input type="number" name="ano_fim" id="ano_fim" class="form-control" placeholder="Final" value="<?= htmlspecialchars($ano_fim) ?>" min="1900" max="2100" autocomplete="off">
            </div>
            <div class="col-md-3 col-12" id="campo-exato" style="display:<?= $tipo_ano==='exato'?'block':'none' ?>;">
              <label for="ano_exato" class="form-label mb-0">Ano exato:</label>
              <input type="number" name="ano_exato" id="ano_exato" class="form-control" placeholder="Ano exato" value="<?= htmlspecialchars($ano_exato) ?>" min="1900" max="2100" autocomplete="off">
            </div>
          </div>
          <!-- Filtro por valor (preço) -->
          <div class="row g-2 align-items-end mb-2">
            <div class="col-md-3 col-12">
              <label for="tipo_valor" class="form-label mb-0">Filtrar valor por:</label>
              <select name="tipo_valor" id="tipo_valor" class="form-select">
                <option value="intervalo" <?= (isset($_GET['tipo_valor']) && $_GET['tipo_valor'] === 'intervalo') ? 'selected' : '' ?>>Mínimo/Máximo</option>
                <option value="exato" <?= (isset($_GET['tipo_valor']) && $_GET['tipo_valor'] === 'exato') ? 'selected' : '' ?>>Valor exato</option>
              </select>
            </div>
            <div class="col-md-3 col-6" id="campo-valor-min" style="display:<?= (!isset($_GET['tipo_valor']) || $_GET['tipo_valor'] === 'intervalo') ? 'block' : 'none' ?>;">
              <label for="valor_min" class="form-label mb-0">Valor mínimo (R$):</label>
              <input type="number" step="0.01" min="0" name="valor_min" id="valor_min" class="form-control" placeholder="Mínimo" value="<?= isset($_GET['valor_min']) ? htmlspecialchars($_GET['valor_min']) : '' ?>" autocomplete="off">
            </div>
            <div class="col-md-3 col-6" id="campo-valor-max" style="display:<?= (!isset($_GET['tipo_valor']) || $_GET['tipo_valor'] === 'intervalo') ? 'block' : 'none' ?>;">
              <label for="valor_max" class="form-label mb-0">Valor máximo (R$):</label>
              <input type="number" step="0.01" min="0" name="valor_max" id="valor_max" class="form-control" placeholder="Máximo" value="<?= isset($_GET['valor_max']) ? htmlspecialchars($_GET['valor_max']) : '' ?>" autocomplete="off">
            </div>
            <div class="col-md-3 col-12" id="campo-valor-exato" style="display:<?= (isset($_GET['tipo_valor']) && $_GET['tipo_valor']==='exato')?'block':'none' ?>;">
              <label for="valor_exato" class="form-label mb-0">Valor exato (R$):</label>
              <input type="number" step="0.01" min="0" name="valor_exato" id="valor_exato" class="form-control" placeholder="Valor exato" value="<?= isset($_GET['valor_exato']) ? htmlspecialchars($_GET['valor_exato']) : '' ?>" autocomplete="off">
            </div>
          </div>
          <div class="row g-2 align-items-end mb-2">
            <div class="col-md-3 col-12">
              <label for="marca" class="form-label mb-0">Marca:</label>
              <select name="marca" id="marca" class="form-select">
                <option value="">Todas</option>
                <?php foreach($marcasCrud as $marcaOpt): ?>
                  <option value="<?= $marcaOpt ?>" <?= (isset($_GET['marca']) && $_GET['marca']==$marcaOpt)?'selected':'' ?>><?= $marcaOpt ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 col-12">
              <label for="combustivel" class="form-label mb-0">Combustível:</label>
              <select name="combustivel" id="combustivel" class="form-select">
                <option value="">Todos</option>
                <?php foreach($combustiveisCrud as $comb): ?>
                  <option value="<?= $comb ?>" <?= (isset($_GET['combustivel']) && $_GET['combustivel']==$comb)?'selected':'' ?>><?= $comb ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 col-12">
              <label class="form-label mb-0">Consumo/Autonomia:</label>
              <input type="number" step="0.1" min="0" name="autonomia" id="campoAutonomia" class="form-control" placeholder="km/l ou km" value="<?= htmlspecialchars($autonomia) ?>" autocomplete="off">
            </div>
            <div class="col-md-3 col-12">
              <label for="conforto" class="form-label mb-0">Conforto:</label>
              <select name="conforto" id="conforto" class="form-select">
                <option value="">Todos</option>
                <?php foreach($confortoCrud as $c): ?>
                  <option value="<?= $c ?>" <?= (isset($_GET['conforto']) && $_GET['conforto']==$c)?'selected':'' ?>><?= $confortoLabels[$c] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row g-2 align-items-end mb-2">
            <div class="col-md-6 col-12">
              <label for="direcao" class="form-label mb-0">Direção:</label>
              <select name="direcao" id="direcao" class="form-select">
                <option value="">Todas</option>
                <?php foreach($direcoesCrud as $dir): ?>
                  <option value="<?= $dir ?>" <?= (isset($_GET['direcao']) && $_GET['direcao']==$dir)?'selected':'' ?>><?= $dir ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 col-12">
              <label for="transmissao" class="form-label mb-0">Transmissão:</label>
              <select name="transmissao" id="transmissao" class="form-select">
                <option value="">Todas</option>
                <?php foreach($transmissoesCrud as $trans): ?>
                  <option value="<?= $trans ?>" <?= (isset($_GET['transmissao']) && $_GET['transmissao']==$trans)?'selected':'' ?>><?= $trans ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row"><div class="col-12 text-center"><button type="submit" class="btn btn-primary mt-2">Filtrar</button></div></div>
        </form>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <?php
            $veiculos = $res->fetchAll(PDO::FETCH_ASSOC);
            if(count($veiculos) == 0):
          ?>
            <p class="text-center">Nenhum veículo encontrado.</p>
          <?php
            else:
              foreach ($veiculos as $row):
                $carName = $row['nome'];
                $carImg = $row['imagem_principal'];
          ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center mb-4">
              <div class="card" style="width:100%;max-width:260px;min-width:220px;box-shadow:0 2px 10px rgba(0,0,0,0.10);border-radius:10px;overflow:hidden;transition:transform 0.15s;">
                <a href="Carro.php?nome=<?= urlencode($carName) ?>&tema=<?= urlencode($tema) ?>" style="display:block;">
                  <?php if (!empty($carImg) && file_exists(__DIR__ . '/../Imagens/' . $carImg)): ?>
                    <img src="/CRUDTCC/php/Vendas/Imagens/<?= htmlspecialchars($carImg) ?>" class="card-img-top" alt="<?= htmlspecialchars($carName) ?>" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
                  <?php else: ?>
                    <img src="/CRUDTCC/images/default.jpg" class="card-img-top" alt="Imagem não disponível" style="width:100%;height:170px;object-fit:cover;background:#eaeaea;">
                  <?php endif; ?>
                </a>
                <div class="card-body text-center p-2" style="background:#fff;">
                  <h5 class="card-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;margin:0 auto;font-size:1.1rem;font-weight:600;">
                    <?= htmlspecialchars($carName) ?>
                  </h5>
                </div>
              </div>
            </div>
          <?php
              endforeach;
            endif;
          ?>
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
      // Consumo/Autonomia: só aparece ao clicar no botão
      document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnMostrarAutonomia');
        var campo = document.getElementById('campoAutonomia');
        if(btn && campo) {
          btn.onclick = function() {
            campo.style.display = 'inline-block';
            btn.style.display = 'none';
            campo.focus();
          };
          if(campo.value && campo.value !== '') {
            campo.style.display = 'inline-block';
            btn.style.display = 'none';
          } else {
            campo.style.display = 'none';
            btn.style.display = 'inline-block';
          }
        }
      });
      document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnMostrarFiltros');
        var form = document.getElementById('filtroForm');
        // Se algum filtro estiver preenchido, mostrar o formulário direto
        var algumPreenchido = false;
        ['ano_inicio','ano_fim','marca','combustivel','autonomia','conforto','direcao','transmissao'].forEach(function(id){
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
      });
      document.addEventListener('DOMContentLoaded', function() {
        // Alterna campos de ano
        var tipoAno = document.getElementById('tipo_ano');
        var campoIntervalo = document.getElementById('campo-intervalo');
        var campoIntervaloFim = document.getElementById('campo-intervalo-fim');
        var campoExato = document.getElementById('campo-exato');
        if(tipoAno && campoIntervalo && campoIntervaloFim && campoExato) {
          tipoAno.onchange = function() {
            if(this.value === 'intervalo') {
              campoIntervalo.style.display = 'block';
              campoIntervaloFim.style.display = 'block';
              campoExato.style.display = 'none';
            } else {
              campoIntervalo.style.display = 'none';
              campoIntervaloFim.style.display = 'none';
              campoExato.style.display = 'block';
            }
          };
        }
      });
      document.addEventListener('DOMContentLoaded', function() {
        // Alterna campos de valor
        var tipoValor = document.getElementById('tipo_valor');
        var campoMin = document.getElementById('campo-valor-min');
        var campoMax = document.getElementById('campo-valor-max');
        var campoExato = document.getElementById('campo-valor-exato');
        if(tipoValor && campoMin && campoMax && campoExato) {
          tipoValor.onchange = function() {
            if(this.value === 'intervalo') {
              campoMin.style.display = 'block';
              campoMax.style.display = 'block';
              campoExato.style.display = 'none';
            } else {
              campoMin.style.display = 'none';
              campoMax.style.display = 'none';
              campoExato.style.display = 'block';
            }
          };
        }
      });
      document.addEventListener('DOMContentLoaded', function() {
        // Botão Ocultar filtros
        var btnOcultar = document.getElementById('btnOcultarFiltros');
        var btnMostrar = document.getElementById('btnMostrarFiltros');
        var form = document.getElementById('filtroForm');
        if(btnOcultar && btnMostrar && form) {
          btnOcultar.onclick = function() {
            form.style.display = 'none';
            btnMostrar.style.display = 'block';
          };
        }
        // Botão Limpar filtros
        var btnLimpar = document.getElementById('btnLimparFiltros');
        if(btnLimpar) {
          btnLimpar.onclick = function() {
            window.location.href = window.location.pathname + (window.location.search.includes('tema=dark') ? '?tema=dark' : (window.location.search.includes('tema=light') ? '?tema=light' : ''));
          };
        }
      });
    </script>
  </body>
</html>
