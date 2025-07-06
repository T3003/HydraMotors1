<?php
include __DIR__ . '/../../config.php';
include __DIR__ . '/../../mysqlexecuta.php';

$con = conectar();
$imageDir = '/images/Carimg/';

// Recebe o nome do carro via GET (exemplo: Carro.php?nome=Fusca)
$carName = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// Inicializa a variável que irá armazenar os dados do carro
$car = null;
// Se um nome de carro foi informado
if ($carName !== '') {
    // Monta a query para buscar o carro pelo nome
    $sql = "SELECT * FROM car WHERE carnome = :nome LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':nome', $carName, PDO::PARAM_STR);
    $stmt->execute();
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Função para deletar veículo e imagens
if (isset($_POST['delete']) && $car) {
    // Deleta imagens se existirem
    foreach (['carimg', 'carimg2', 'carimg3'] as $imgField) {
        if (!empty($car[$imgField])) {
            $imgPath = $imageDir . $car[$imgField];
            if (file_exists($imgPath)) {
                unlink($imgPath); // Remove o arquivo da imagem do servidor
            }
        }
    }
    // Deleta o registro do banco
    $sqlDelete = "DELETE FROM car WHERE carnome = :nome";
    $stmtDel = $con->prepare($sqlDelete);
    $stmtDel->bindValue(':nome', $car['carnome'], PDO::PARAM_STR);
    $stmtDel->execute();
    // Redireciona para a página principal após deletar
    header("Location: /CRUDTCC/php/categorias/GERAL.php?deleted=1");
    exit;
}

// Função para verificar permissão de edição/deleção
session_start();
$canEditDelete = false;
$canEditOnly = false;
if (isset($_SESSION['logid']) && $car) {
    if ($_SESSION['logid'] === $car['logid']) {
        $canEditDelete = true;
    } else {
        // Verifica se Adm=1 ou Rev=1 na tabela clilogin
        $logid = $_SESSION['logid'];
        $sqlPerm = "SELECT Adm, Rev FROM clilogin WHERE logid = :logid LIMIT 1";
        $stmtPerm = $con->prepare($sqlPerm);
        $stmtPerm->bindValue(':logid', $logid, PDO::PARAM_STR);
        $stmtPerm->execute();
        $rowPerm = $stmtPerm->fetch(PDO::FETCH_ASSOC);
        if ($rowPerm) {
            if (isset($rowPerm['Adm']) && $rowPerm['Adm'] == 1) {
                $canEditDelete = true;
            } elseif (isset($rowPerm['Rev']) && $rowPerm['Rev'] == 1) {
                $canEditOnly = true;
            }
        }
        // Permite edição se o usuário está em RevId do carro
        if (!$canEditDelete && !$canEditOnly && !empty($car['RevId'])) {
            $revIds = array_map('trim', explode(',', $car['RevId']));
            if (in_array($_SESSION['logid'], $revIds)) {
                $canEditOnly = true;
            }
        }
    }
}

// Obtém nome e foto do autor do carro
$autorNome = '-';
$autorFoto = '/CRUDTCC/php/Usuários/Fotos de perfil/default.jpg';
if (!empty($car['logid'])) {
    $sqlAutor = "SELECT logname, logpfp FROM clilogin WHERE logid = :logid LIMIT 1";
    $stmtAutor = $con->prepare($sqlAutor);
    $stmtAutor->bindValue(':logid', $car['logid'], PDO::PARAM_STR);
    $stmtAutor->execute();
    $rowAutor = $stmtAutor->fetch(PDO::FETCH_ASSOC);
    if ($rowAutor) {
        $autorNome = htmlspecialchars($rowAutor['logname']);
        if (!empty($rowAutor['logpfp'])) {
            $autorFoto = '/CRUDTCC/php/Usuários/Fotos de perfil/' . htmlspecialchars($rowAutor['logpfp']);
        }
    }
}
// Inclui o cabeçalho do site APENAS da pasta categorias
include_once("header.php");
// Define a classe do body de acordo com o tema herdado do header.php
$bodyClass = isset($bodyClass) ? $bodyClass : 'light-mode';
?>
<!doctype html>
<html lang="en">
  <head>
    <script>
    (function() {
      var isDark = localStorage.getItem('dark-mode') === '1';
      var className = isDark ? 'dark-mode' : 'light-mode';
      document.documentElement.classList.remove('dark-mode', 'light-mode');
      if (document.body) document.body.classList.remove('dark-mode', 'light-mode');
      document.documentElement.classList.add(className);
      if (document.body) {
        document.body.classList.add(className);
      } else {
        window.addEventListener('DOMContentLoaded', function() {
          document.body.classList.add(className);
        });
      }
    })();
    </script>
    <style>
      html, body {
        font-family: Georgia, Times New Roman, serif !important;
      }
      html, body, h1, h2, h3, h4, h5, h6, strong, b, .car-info-box, .btn, .badge, .form-control, .main-box, .container, .row, .col-12, .col-6, .car-thumbs, .carousel-inner, .zoom-modal, .zoom-close, .zoom-nav-btn, .car-info-box-center, .text-center, .text-justify, .mb-3, .py-2, .mt-2, .mt-3, .mt-4, .mb-2, .gap-2, .rounded-circle, .zoomable-img {
        font-family: Georgia, Times New Roman, serif !important;
      }
    </style>
    <!-- Metadados e links de CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Define o título da página com o nome do carro ou mensagem de erro -->
    <title><?= $car ? htmlspecialchars($car['carnome']) : 'Carro não encontrado' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/header.css" rel="stylesheet">
  </head>
  <body>
    <!-- Inclui o cabeçalho do site -->
    <?php include_once(__DIR__ . '/../../header.php'); ?>
    <!-- Caixa principal com estilização -->
    <div class="main-box d-flex flex-column align-items-center">
      <?php if ($car): ?>
        <h1 class="text-center" style="font-family: 'Arial Black', Arial, sans-serif; font-size: 3rem; margin-bottom: 20px;">
          <?= htmlspecialchars($car['carnome']) ?>
          <?php if (!empty($car['genname'])): ?>
            - <span style="font-size:3rem; font-weight:400; color:#ffe066;"> <?= nl2br(htmlspecialchars($car['genname'])) ?> </span>
          <?php elseif (!empty($car['cargen'])): ?>
            - <span style="font-size:3rem; font-weight:400; color:#ffe066;"> <?= htmlspecialchars($car['cargen']) ?> </span>
          <?php endif; ?>
        </h1>
        <div class="text-center mb-3" style="font-size:1.1rem;color:#888;display:flex;align-items:center;justify-content:center;gap:10px;">
          <!-- Foto do autor do carro -->
          <div style="flex-shrink: 0;">
            <img src="<?= $autorFoto ?>" alt="Foto de <?= $autorNome ?>" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ffe066;">
          </div>
          <!-- Nome do autor do carro -->
          Por <strong style="color:#ffe066;"><?= htmlspecialchars($autorNome ?: 'Usuário desconhecido') ?></strong>
        </div>
        <div class="d-flex flex-column align-items-center" style="width: 100%;">
          <!-- Carrossel de imagens principais -->
          <div class="carousel-dark-bg">
            <!-- Carrossel Bootstrap para as imagens do carro -->
            <div id="carroCarousel" class="carousel slide" data-bs-ride="carousel" style="max-width: 400px; margin: 0 auto 10px auto;">
              <div class="carousel-inner">
                <?php
                  // Monta o array de imagens principais
                  $imgs = [];
                  if (!empty($car['carimg']) && file_exists($imageDir . $car['carimg'])) $imgs[] = $car['carimg'];
                  if (!empty($car['carimg2']) && file_exists($imageDir . $car['carimg2'])) $imgs[] = $car['carimg2'];
                  if (!empty($car['carimg3']) && file_exists($imageDir . $car['carimg3'])) $imgs[] = $car['carimg3'];
                  if (empty($imgs)) $imgs[] = '/CRUDTCC/images/default.jpg'; // Imagem padrão se não houver imagens
                  foreach ($imgs as $i => $img):
                    $src = strpos($img, '/CRUDTCC/images/') === 0 ? $img : $imageDir . $img;
                ?>
                  <!-- Cada imagem do carrossel -->
                  <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <img src="<?= $src ?>" class="d-block w-100" style="max-width:400px; height:auto;" alt="Imagem <?= $i+1 ?>">
                  </div>
                <?php endforeach; ?>
              </div>
              <!-- Setas de navegação do carrossel -->
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
            </div>
            <!-- Miniaturas das imagens adicionais abaixo do carrossel -->
            <div class="d-flex justify-content-center gap-2 mt-3 car-thumbs">
              <?php
                // Exibe as três miniaturas, se existirem, sempre na ordem carimg, carimg2, carimg3
                $thumbs = [
                  (!empty($car['carimg']) && file_exists($imageDir . $car['carimg'])) ? $imageDir . $car['carimg'] : '/CRUDTCC/images/default.jpg',
                  (!empty($car['carimg2']) && file_exists($imageDir . $car['carimg2'])) ? $imageDir . $car['carimg2'] : '/CRUDTCC/images/default.jpg',
                  (!empty($car['carimg3']) && file_exists($imageDir . $car['carimg3'])) ? $imageDir . $car['carimg3'] : '/CRUDTCC/images/default.jpg'
                ];
                foreach ($thumbs as $thumb):
              ?>
                <!-- Miniatura de cada imagem -->
                <img src="<?= $thumb ?>" style="max-width: 130px; width: 100%; height: 80px; object-fit: cover; cursor: zoom-in;" class="zoomable-img">
              <?php endforeach; ?>
            </div>
          </div>
          <!-- Modal de Zoom para imagens -->
          <div id="zoomModal" class="zoom-modal" style="display:none;">
            <span class="zoom-close" id="zoomCloseBtn">&times;</span>
            <button id="zoomPrevBtn" class="zoom-nav-btn" style="left: 2vw;">&#10094;</button>
            <img class="zoom-modal-content" id="zoomImg">
            <button id="zoomNextBtn" class="zoom-nav-btn" style="right: 2vw;">&#10095;</button>
          </div>
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              // Seleciona todas as imagens do carrossel e miniaturas
              var zoomableImgs = Array.from(document.querySelectorAll('.carousel-inner img, .car-thumbs img'));
              var zoomModal = document.getElementById('zoomModal');
              var zoomImg = document.getElementById('zoomImg');
              var zoomCloseBtn = document.getElementById('zoomCloseBtn');
              var zoomPrevBtn = document.getElementById('zoomPrevBtn');
              var zoomNextBtn = document.getElementById('zoomNextBtn');
              var currentIndex = 0;

              function showZoom(index) {
                if (index < 0) index = zoomableImgs.length - 1;
                if (index >= zoomableImgs.length) index = 0;
                currentIndex = index;
                zoomImg.src = zoomableImgs[currentIndex].src;
                zoomModal.style.display = 'flex';
              }

              zoomableImgs.forEach(function(img, idx) {
                img.classList.add('zoomable-img');
                img.addEventListener('click', function() {
                  showZoom(idx);
                });
              });

              zoomPrevBtn.onclick = function(e) {
                e.stopPropagation();
                showZoom(currentIndex - 1);
              };
              zoomNextBtn.onclick = function(e) {
                e.stopPropagation();
                showZoom(currentIndex + 1);
              };

              // Fecha o modal ao clicar no X
              zoomCloseBtn.onclick = function() {
                zoomModal.style.display = 'none';
                zoomImg.src = '';
              };
              // Fecha o modal ao clicar fora da imagem e botões
              zoomModal.onclick = function(event) {
                if (event.target === zoomModal) {
                  zoomModal.style.display = 'none';
                  zoomImg.src = '';
                }
              };
              // Fecha com ESC e navega com setas
              document.addEventListener('keydown', function(e) {
                if (zoomModal.style.display === 'flex') {
                  if (e.key === 'Escape') {
                    zoomModal.style.display = 'none';
                    zoomImg.src = '';
                  } else if (e.key === 'ArrowLeft') {
                    showZoom(currentIndex - 1);
                  } else if (e.key === 'ArrowRight') {
                    showZoom(currentIndex + 1);
                  }
                }
              });
            });
          </script>
          <!-- Exibe as informações principais em blocos lado a lado -->
          <div class="container mt-4">
            <div class="row justify-content-center w-100" style="margin:0;">
              <div class="col-12 col-md-6 mb-3">
                <div class="car-info-box car-info-box-center w-100" style="width:100%;">
                  <strong style="font-size:1.3rem;display:block;">Preço Fipe:</strong>
                  <div style="font-size: 1.2rem; font-weight: 500;">
                    <?php
                      $preco = isset($car['carfipe']) ? trim($car['carfipe']) : '';
                      if ($preco !== '' && is_numeric($preco)) {
                        echo 'R$ ' . number_format(floatval($preco), 2, ',', '.');
                      } else {
                        echo '-';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <div class="car-info-box car-info-box-center w-100" style="width:100%;">
                  <strong style="font-size:1.3rem;display:block;">Ano:</strong>
                  <div style="font-size: 1.2rem; font-weight: 500;">
                    <?php
                      $anoInicio = isset($car['CarFabIn']) ? (int)$car['CarFabIn'] : 0;
                      $anoFim = isset($car['CarFabFim']) ? (int)$car['CarFabFim'] : 0;
                      if ($anoInicio > 0 && $anoFim > 0) {
                        echo $anoInicio . ' até ' . $anoFim;
                      } elseif ($anoInicio > 0) {
                        echo $anoInicio;
                      } elseif ($anoFim > 0) {
                        echo $anoFim;
                      } else {
                        echo '-';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12 mb-3">
                <div class="car-info-box car-info-box-center w-100" style="width:100%;">
                  <strong style="font-size:1.3rem;display:block;">Descrição:</strong>
                  <div style="font-size: 1.1rem; text-align:left;">
                    <?= nl2br(htmlspecialchars($car['descricao'] ?? '')) ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <div class="car-info-box mb-2 text-center py-2">
                  <strong style="font-size:1.1rem;">Consumo:</strong>
                  <div style="font-size:0.98rem;">
                    <?php
                      // Função para determinar unidade do combustível
                      function unidadeConsumo($comb) {
                        $comb = strtolower($comb);
                        if (strpos($comb, 'elétrico') !== false || strpos($comb, 'eletrico') !== false) return 'km/kWh';
                        if (strpos($comb, 'gnv') !== false) return 'km/m³';
                        return 'km/l';
                      }
                      // Função para classificar consumo
                      function classificaConsumo($comb, $valor) {
                        $comb = strtolower($comb);
                        $valor = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $valor)));
                        if (strpos($comb, 'gasolina') !== false) {
                          if ($valor > 12) return ['Econômico','success'];
                          if ($valor >= 8) return ['Neutro','warning text-dark'];
                          return ['Gastão','danger'];
                        }
                        if (strpos($comb, 'etanol') !== false) {
                          if ($valor > 9) return ['Econômico','success'];
                          if ($valor >= 6) return ['Neutro','warning text-dark'];
                          return ['Gastão','danger'];
                        }
                        if (strpos($comb, 'diesel') !== false) {
                          if ($valor > 14) return ['Econômico','success'];
                          if ($valor >= 12) return ['Neutro','warning text-dark'];
                          return ['Gastão','danger'];
                        }
                        if (strpos($comb, 'elétrico') !== false || strpos($comb, 'eletrico') !== false) {
                          if ($valor > 7) return ['Econômico','success'];
                          if ($valor >= 5) return ['Neutro','warning text-dark'];
                          return ['Gastão','danger'];
                        }
                        if (strpos($comb, 'gnv') !== false) {
                          if ($valor > 13) return ['Econômico','success'];
                          if ($valor >= 10) return ['Neutro','warning text-dark'];
                          return ['Gastão','danger'];
                        }
                        // Default para outros
                        return ['-','secondary'];
                      }
                      $consumoValor = isset($car['Consumo']) ? trim($car['Consumo']) : '';
                      $consumo2Valor = isset($car['Consumo2']) ? trim($car['Consumo2']) : '';
                      $consumo3Valor = isset($car['Consumo3']) ? trim($car['Consumo3']) : '';
                      $consumo4Valor = isset($car['Consumo4']) ? trim($car['Consumo4']) : '';
                      $consumo5Valor = isset($car['Consumo5']) ? trim($car['Consumo5']) : '';
                      $combustivel = isset($car['CarCombus']) ? $car['CarCombus'] : '';
                      $isFlex = stripos($combustivel, 'flex') !== false;
                      $isEletrico = stripos($combustivel, 'elétrico') !== false || stripos($combustivel, 'eletrico') !== false;
                      $isHibrido = stripos($combustivel, 'híbrido') !== false || stripos($combustivel, 'hibrido') !== false;
                      if ($isEletrico && !$isHibrido) {
                        // Elétrico puro: só autonomia
                        $autonomia = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo3Valor)));
                        if ($autonomia <= 0) {
                          echo '-';
                        } else {
                          if ($autonomia <= 250) {
                            $classif = 'Gastão'; $badge = 'danger';
                          } elseif ($autonomia <= 400) {
                            $classif = 'Neutro'; $badge = 'warning text-dark';
                          } else {
                            $classif = 'Econômico'; $badge = 'success';
                          }
                          echo 'Autonomia: ' . nl2br(htmlspecialchars($consumo3Valor)) . ' km';
                          echo ' <span class="badge bg-' . $badge . '" style="font-size:1em;">' . $classif . '</span>';
                        }
                      } elseif ($isHibrido) {
                        // Híbrido: gasolina, etanol, autonomia elétrica, diesel, gnv
                        $comb1 = 'Gasolina';
                        $comb2 = 'Etanol';
                        $comb3 = 'Elétrico';
                        $comb4 = 'GNV';
                        $comb5 = 'Diesel';
                        $consumoNum = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumoValor)));
                        $consumo2Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo2Valor)));
                        $autonomia = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo3Valor)));
                        $consumo4Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo4Valor)));
                        $consumo5Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo5Valor)));
                        $temAlgum = false;
                        if ($consumoNum > 0) {
                          $temAlgum = true;
                          $unidade1 = unidadeConsumo($comb1);
                          list($classif1, $badge1) = classificaConsumo($comb1, $consumoValor);
                          echo '<span style="font-size:0.98em;">Gasolina: ' . nl2br(htmlspecialchars($consumoValor)) . ' ' . $unidade1;
                          if ($classif1 !== '-') {
                            echo ' <span class="badge bg-' . $badge1 . '" style="font-size:1em;">' . $classif1 . '</span>';
                          }
                          echo '</span>';
                        }
                        if ($consumo2Num > 0) {
                          $temAlgum = true;
                          $unidade2 = unidadeConsumo($comb2);
                          list($classif2, $badge2) = classificaConsumo($comb2, $consumo2Valor);
                          echo '<br><span style="font-size:0.98em;">Etanol: ' . nl2br(htmlspecialchars($consumo2Valor)) . ' ' . $unidade2;
                          if ($classif2 !== '-') {
                            echo ' <span class="badge bg-' . $badge2 . '" style="font-size:1em;">' . $classif2 . '</span>';
                          }
                          echo '</span>';
                        }
                        if ($autonomia > 0) {
                          $temAlgum = true;
                          if ($autonomia <= 250) {
                            $classif3 = 'Gastão'; $badge3 = 'danger';
                          } elseif ($autonomia <= 400) {
                            $classif3 = 'Neutro'; $badge3 = 'warning text-dark';
                          } else {
                            $classif3 = 'Econômico'; $badge3 = 'success';
                          }
                          echo '<br><span style="font-size:0.98em;">Elétrico: ' . nl2br(htmlspecialchars($consumo3Valor)) . ' km';
                          echo ' <span class="badge bg-' . $badge3 . '" style="font-size:1em;">' . $classif3 . '</span>';
                          echo '</span>';
                        }
                        if ($consumo4Num > 0) {
                          $temAlgum = true;
                          $unidade4 = unidadeConsumo($comb4);
                          list($classif4, $badge4) = classificaConsumo($comb4, $consumo4Valor);
                          echo '<br><span style="font-size:0.98em;">GNV: ' . nl2br(htmlspecialchars($consumo4Valor)) . ' ' . $unidade4;
                          if ($classif4 !== '-') {
                            echo ' <span class="badge bg-' . $badge4 . '" style="font-size:1em;">' . $classif4 . '</span>';
                          }
                          echo '</span>';
                        }
                        if ($consumo5Num > 0) {
                          $temAlgum = true;
                          $unidade5 = unidadeConsumo($comb5);
                          list($classif5, $badge5) = classificaConsumo($comb5, $consumo5Valor);
                          echo '<br><span style="font-size:0.98em;">Diesel: ' . nl2br(htmlspecialchars($consumo5Valor)) . ' ' . $unidade5;
                          if ($classif5 !== '-') {
                            echo ' <span class="badge bg-' . $badge5 . '" style="font-size:1em;">' . $classif5 . '</span>';
                          }
                          echo '</span>';
                        }
                        if (!$temAlgum) echo '-';
                      } else {
                        // ...lógica já existente para flex e outros...
                        $comb1 = $isFlex ? 'Gasolina' : $combustivel;
                        $comb2 = $isFlex ? 'Etanol' : $combustivel;
                        $consumoNum = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumoValor)));
                        $consumo2Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo2Valor)));
                        if ($consumoValor === '' || $consumoNum <= 0) {
                          echo '-';
                        } else {
                          $unidade1 = unidadeConsumo($comb1);
                          list($classif1, $badge1) = classificaConsumo($comb1, $consumoValor);
                          if ($isFlex) {
                            echo '<span style="font-size:0.98em;">Gasolina: ';
                          }
                          echo nl2br(htmlspecialchars($consumoValor)) . ' ' . $unidade1;
                          if ($classif1 !== '-') {
                            echo ' <span class="badge bg-' . $badge1 . '" style="font-size:1em;">' . $classif1 . '</span>';
                          }
                          if ($isFlex) {
                            echo '</span>';
                          }
                          // Exibe Consumo2 se diferente de 0
                          if ($consumo2Num > 0) {
                            $unidade2 = unidadeConsumo($comb2);
                            list($classif2, $badge2) = classificaConsumo($comb2, $consumo2Valor);
                            echo '<br><span style="font-size:0.98em;">';
                            if ($isFlex) {
                              echo 'Etanol: ';
                            } else {
                              echo 'Consumo 2: ';
                            }
                            echo nl2br(htmlspecialchars($consumo2Valor)) . ' ' . $unidade2;
                            if ($classif2 !== '-') {
                              echo ' <span class="badge bg-' . $badge2 . '" style="font-size:1em;">' . $classif2 . '</span>';
                            }
                            echo '</span>';
                          }
                        }
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="car-info-box mb-2 text-center py-2">
                  <strong style="font-size:1.1rem;">Tipo de Combustível:</strong>
                  <div style="font-size:0.98rem;">
                    <?php
                      $combustivel = isset($car['CarCombus']) ? $car['CarCombus'] : '';
                      $isFlex = stripos($combustivel, 'flex') !== false;
                      $isEletrico = stripos($combustivel, 'elétrico') !== false || stripos($combustivel, 'eletrico') !== false;
                      $isHibrido = stripos($combustivel, 'híbrido') !== false || stripos($combustivel, 'hibrido') !== false;
                      $consumoValor = isset($car['Consumo']) ? trim($car['Consumo']) : '';
                      $consumo2Valor = isset($car['Consumo2']) ? trim($car['Consumo2']) : '';
                      $consumo3Valor = isset($car['Consumo3']) ? trim($car['Consumo3']) : '';
                      $consumo4Valor = isset($car['Consumo4']) ? trim($car['Consumo4']) : '';
                      $consumo5Valor = isset($car['Consumo5']) ? trim($car['Consumo5']) : '';
                      $combustiveis = [];
                      if ($isFlex) {
                        // Flex: lista Gasolina e Etanol, cada um em uma linha e com cor
                        $combustiveis = [];
                        $consumoNum = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumoValor)));
                        $consumo2Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo2Valor)));
                        if ($consumoNum > 0) $combustiveis[] = '<span style="display:block;color:#d35400;font-weight:bold;background:#ffe5d0;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Gasolina</span>';
                        if ($consumo2Num > 0) $combustiveis[] = '<span style="display:block;color:#145a32;font-weight:bold;background:#d4efdf;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Etanol</span>';
                        echo count($combustiveis) ? implode('', $combustiveis) : '-';
                      } elseif ($isHibrido) {
                        // Híbrido: lista todos os combustíveis, cada um em uma linha e com cor
                        $combustiveis = [];
                        $consumoNum = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumoValor)));
                        $consumo2Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo2Valor)));
                        $autonomia = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo3Valor)));
                        $consumo4Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo4Valor)));
                        $consumo5Num = floatval(str_replace([','], ['.'], preg_replace('/[^0-9\.,]/', '', $consumo5Valor)));
                        if ($consumoNum > 0) $combustiveis[] = '<span style="display:block;color:#d35400;font-weight:bold;background:#ffe5d0;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Gasolina</span>';
                        if ($consumo2Num > 0) $combustiveis[] = '<span style="display:block;color:#145a32;font-weight:bold;background:#d4efdf;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Etanol</span>';
                        if ($autonomia > 0) $combustiveis[] = '<span style="display:block;color:#1565c0;font-weight:bold;background:#e3f2fd;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Elétrico</span>';
                        if ($consumo4Num > 0) $combustiveis[] = '<span style="display:block;color:#b7950b;font-weight:bold;background:#fff9c4;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">GNV</span>';
                        if ($consumo5Num > 0) $combustiveis[] = '<span style="display:block;color:#4e342e;font-weight:bold;background:#d7ccc8;border-radius:6px;padding:2px 8px 2px 8px;margin-bottom:2px;">Diesel</span>';
                        echo count($combustiveis) ? implode('', $combustiveis) : '-';
                      } else {
                        echo isset($car['CarCombus']) && $car['CarCombus'] !== '' ? htmlspecialchars($car['CarCombus']) : '-';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="car-info-box mb-2 text-center py-2">
                  <strong style="font-size:1.1rem;">Tipo de Direção:</strong>
                  <div style="font-size:0.98rem;">
                    <?= isset($car['CarDire']) && $car['CarDire'] !== '' ? htmlspecialchars($car['CarDire']) : '-' ?>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="car-info-box mb-2 text-center py-2">
                  <strong style="font-size:1.1rem;">Marca:</strong>
                  <div style="font-size:0.98rem;">
                    <?= isset($car['carmarca']) && $car['carmarca'] !== '' ? htmlspecialchars($car['carmarca']) : '-' ?>
                  </div>
                </div>
              </div>
              <!-- Campo Geração abaixo de Marca -->
              <div class="col-6">
                <div class="car-info-box mb-2 text-center py-2">
                  <strong style="font-size:1.1rem;">Geração:</strong>
                  <div style="font-size:0.98rem;">
                    <?php
                      $geracao = isset($car['cargen']) ? trim($car['cargen']) : '';
                      echo $geracao !== '' ? nl2br(htmlspecialchars($geracao)) : '-';
                    ?>
                  </div>
                </div>
              </div>
              <?php
  // Buscar nomes das categorias principais
  $catNome = '';
  $catNomeArr = [];
  if (!empty($car['catid'])) {
    $stmtCat = $con->prepare('SELECT nome FROM categoria WHERE id = :id LIMIT 1');
    $stmtCat->bindValue(':id', $car['catid'], PDO::PARAM_INT);
    $stmtCat->execute();
    $rowCat = $stmtCat->fetch(PDO::FETCH_ASSOC);
    if ($rowCat && !empty($rowCat['nome'])) $catNomeArr[] = htmlspecialchars($rowCat['nome']);
  }
  if (!empty($car['catidop1'])) {
    $stmtCat2 = $con->prepare('SELECT nome FROM categoria WHERE id = :id LIMIT 1');
    $stmtCat2->bindValue(':id', $car['catidop1'], PDO::PARAM_INT);
    $stmtCat2->execute();
    $rowCat2 = $stmtCat2->fetch(PDO::FETCH_ASSOC);
    if ($rowCat2 && !empty($rowCat2['nome'])) $catNomeArr[] = htmlspecialchars($rowCat2['nome']);
  }
  if (!empty($car['catidop2'])) {
    $stmtCat3 = $con->prepare('SELECT nome FROM categoria WHERE id = :id LIMIT 1');
    $stmtCat3->bindValue(':id', $car['catidop2'], PDO::PARAM_INT);
    $stmtCat3->execute();
    $rowCat3 = $stmtCat3->fetch(PDO::FETCH_ASSOC);
    if ($rowCat3 && !empty($rowCat3['nome'])) $catNomeArr[] = htmlspecialchars($rowCat3['nome']);
  }
  $catNome = count($catNomeArr) ? implode(', ', $catNomeArr) : '-';
?>
<div class="col-6">
  <div class="car-info-box mb-2 text-center py-2">
    <strong style="font-size:1.1rem;">Categoria:</strong>
    <div style="font-size:0.98rem;">
      <?= $catNome ?>
    </div>
  </div>
</div>
              <!-- Fim do bloco conforto/esportividade agrupados -->

              <!-- Exibição do nível de conforto -->
              <div class="col-12">
                <div class="car-info-box">
                  <strong style="font-size:1.3rem;display:block;font-family:Georgia,Times New Roman,serif;">Conforto:</strong>
                  <div style="font-size: 1.2rem; text-align: justify; color: inherit; font-family: Georgia, Times New Roman, serif;">
                    <?php
                      $confortoLabels = [1 => "Básico", 2 => "Intermediário", 3 => "Confortável", 4 => "Luxuoso", 5 => "Premium/Executivo"];
                      $confortoDescs = [
                        1 => "Básico – Carros com bancos simples, pouca ou nenhuma tecnologia de suspensão avançada, ruído interno perceptível e poucos recursos de conforto, como ar-condicionado básico e acabamento simples.",
                        2 => "Intermediário – Veículos com bancos um pouco mais ergonômicos, ar-condicionado eficiente, suspensão melhor ajustada e menos ruído interno. Podem incluir alguns extras como direção elétrica ou regulagem de altura do banco.",
                        3 => "Confortável – Modelos com bancos mais anatômicos e materiais melhores, suspensão bem ajustada para reduzir impactos, isolamento acústico aprimorado e recursos como ar-condicionado digital, piloto automático e mais ajustes elétricos nos bancos.",
                        4 => "Luxuoso – Carros com bancos de couro, climatização individual, excelente isolamento acústico, suspensão adaptativa, sistema multimídia sofisticado e recursos como bancos aquecidos, ventilados e com ajustes automáticos.",
                        5 => "Premium/Executivo – O nível máximo de conforto, encontrado em sedãs de alto padrão e SUVs de luxo. Aqui há bancos com função de massagem, suspensão a ar, silêncio absoluto na cabine, acabamento refinado, materiais nobres e tecnologias avançadas para o bem-estar dos passageiros."
                      ];
                      $conforto = isset($car['CarConfort']) ? intval($car['CarConfort']) : 0;
                      if ($conforto > 0) {
                        echo '<b>' . $confortoLabels[$conforto] . '</b><br>' . $confortoDescs[$conforto];
                      } else {
                        echo '-';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <!-- Exibição do nível de esportividade -->
              <div class="col-12">
                <div class="car-info-box">
                  <strong style="font-size:1.3rem;display:block;font-family:Georgia,Times New Roman,serif;">Esportividade:</strong>
                  <div style="font-size: 1.2rem; text-align: justify; color: inherit; font-family: Georgia, Times New Roman, serif;">
                    <?php
                      $esportividadeLabels = [1 => "Básico", 2 => "Intermediário", 3 => "Esportivo", 4 => "Muito Esportivo", 5 => "Extremo"];
                      $esportividadeDescs = [
                        1 => "Básico – Carros com pouca ou nenhuma proposta esportiva, suspensão macia, direção voltada ao conforto e motores de baixa potência.",
                        2 => "Intermediário – Veículos com leve apelo esportivo, suspensão um pouco mais firme, respostas um pouco mais rápidas e motores intermediários.",
                        3 => "Esportivo – Modelos com suspensão mais rígida, direção precisa, motores mais potentes e visual esportivo. Podem ter modos de condução esportivos.",
                        4 => "Muito Esportivo – Carros com foco em desempenho, suspensão esportiva, freios dimensionados, motores de alta potência e visual agressivo.",
                        5 => "Extremo – Nível máximo de esportividade, encontrado em superesportivos e carros de pista. Chassi, suspensão, freios e motor otimizados para performance máxima, com pouca ou nenhuma preocupação com conforto."
                      ];
                      $esportividade = isset($car['CarSport']) ? intval($car['CarSport']) : 0;
                      if ($esportividade > 0) {
                        echo '<b>' . $esportividadeLabels[$esportividade] . '</b><br>' . $esportividadeDescs[$esportividade];
                      } else {
                        echo '-';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <!-- Preço de manutenção -->
              <div class="col-12">
                <div class="car-info-box">
                  <strong style="font-size:1.3rem;display:block;">Preço de Manutenção:</strong>
                  <div style="font-size: 1.2rem; text-align: justify;">
                    <?= isset($car['pdm']) && $car['pdm'] !== '' ? nl2br(htmlspecialchars($car['pdm'])) : '-' ?>
                  </div>
                </div>
              </div>
              <!-- História do carro -->
              <div class="col-12">
                <div class="car-info-box">
                  <strong style="font-size:1.3rem;display:block;">História do carro:</strong>
                  <div style="font-size: 1.2rem; text-align: justify;"> <?= nl2br(htmlspecialchars($car['carhistory'] ?? '')) ?> </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php if ($canEditDelete): ?>
          <div class="d-flex justify-content-center align-items-center mt-2 mb-2" style="width:100%;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 24px; width: auto;">
              <!-- Botão para editar o veículo -->
              <a href="EditarCarro.php?nome=<?= urlencode($car['carnome']) ?>" class="btn btn-warning" style="background-color: #ffe066; color: #232323; font-weight: bold; text-decoration: none; border: none; min-width: 150px; text-align: center; display: inline-block;">Editar Veículo</a>
              <!-- Botão para deletar o veículo -->
              <form method="post" onsubmit="return confirm('Tem certeza que deseja deletar este veículo? Esta ação não pode ser desfeita.');" style="display:inline-block; min-width: 150px; text-align: center; margin: 0;">
                <button type="submit" name="delete" class="btn btn-danger" style="width:100%; min-width: 150px;">Deletar Veículo</button>
              </form>
            </div>
          </div>
        <?php elseif ($canEditOnly): ?>
          <div class="d-flex justify-content-center align-items-center mt-2 mb-2" style="width:100%;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 24px; width: auto;">
              <!-- Botão para editar o veículo -->
              <a href="EditarCarro.php?nome=<?= urlencode($car['carnome']) ?>" class="btn btn-warning" style="background-color: #ffe066; color: #232323; font-weight: bold; text-decoration: none; border: none; min-width: 150px; text-align: center; display: inline-block;">Editar Veículo</a>
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <!-- Mensagem caso o carro não seja encontrado -->
        <h2 class="text-center mt-5">Carro não encontrado.</h2>
      <?php endif; ?>
    </div>
    <!-- Script do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Removido script duplicado de alternância de tema, pois agora é herdado do header.php -->
  </body>
</html>
