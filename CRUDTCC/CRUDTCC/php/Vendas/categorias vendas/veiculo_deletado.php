<?php
include '../config.php'; // Garante a função conectar()
include 'header.php'; // Herda o header e o sistema de tema
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Veículo Deletado</title>
    <link rel="stylesheet" href="/CRUDTCC/css/carro.css">
    <style>
        .container { background: #fff; display: inline-block; padding: 40px 60px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; margin-top: 80px; }
        .btn { margin-top: 30px; padding: 12px 32px; background: #007bff; color: #fff; border: none; border-radius: 4px; font-size: 18px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #0056b3; }
        body.dark-mode .container { background: #23272a; color: #ffe066; box-shadow: 0 2px 16px rgba(0,0,0,0.30); }
        body.dark-mode .btn { background: #ffe066; color: #23272a; }
        body.dark-mode .btn:hover { background: #e6c200; color: #23272a; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Veículo deletado com sucesso!</h2>
        <a href="/CRUDTCC/php/Vendas/Vendas.php" class="btn">Voltar para Vendas</a>
    </div>
</body>
</html>
