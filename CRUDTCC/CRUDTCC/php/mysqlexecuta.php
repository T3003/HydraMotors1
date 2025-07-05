<?php
/*
$id - Ponteiro da Conexão (agora um objeto PDO)
$sql - Cláusula SQL a executar
*/
function mysqlexecuta($pdo, $sql) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        // Se for SELECT, retorna os resultados
        if (stripos(trim($sql), 'select') === 0) {
            return $stmt;
        }
        // Para INSERT/UPDATE/DELETE, retorna true
        return true;
    } catch (PDOException $e) {
        echo "Ocorreu um erro na conexão com o banco de dados: " . $e->getMessage();
        exit;
    }
}
?>
