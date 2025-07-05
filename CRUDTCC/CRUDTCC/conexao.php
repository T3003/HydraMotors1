<?php
if (!function_exists('conectar')) {
    function conectar()
    {
        try {
            $pdo = new PDO("sqlite:" . __DIR__ . "/hydramotors.db");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
            exit;
        }
    }
}

// Conectar ao banco de dados
try {
    $pdo = conectar();

    // Criar a tabela Category, se não existir
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS Category (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        );
    ";
    $pdo->exec($createTableSQL);

    // Consultar as categorias
    $sql = "SELECT * FROM Category";
    $stmt = $pdo->query($sql);

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}