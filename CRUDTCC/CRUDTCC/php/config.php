<?php
function conectar()
{
    try {
        // Caminho absoluto para o banco na raiz do projeto
        $dbPath = realpath(dirname(__DIR__, 1) . '/hydramotors.db');
        if (!$dbPath) {
            throw new Exception('Arquivo hydramotors.db não encontrado na raiz do projeto CRUDTCC. Caminho tentado: ' . dirname(__DIR__, 1) . '/hydramotors.db');
        }
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        echo "Ocorreu um erro na conexão com o banco de dados: " . $e->getMessage();
        return null;
    }
}
?>
