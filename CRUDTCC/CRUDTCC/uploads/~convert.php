<?php
include '../php/config.php';
include '../php/mysqlexecuta.php'; // Para executar o script no MySQL

// Conexão com o banco de dados
$con = conectar();
mysqli_select_db($con, 'hydramotors');

// Diretório de uploads
$uploadDir = __DIR__;
$files = array_diff(scandir($uploadDir), array('.', '..', '~convert.php')); // Ignora arquivos especiais

foreach ($files as $file) {
    $filePath = $uploadDir . DIRECTORY_SEPARATOR . $file;

    // Verifica se é um arquivo válido
    if (is_file($filePath)) {
        // Lê o conteúdo do arquivo como blob
        $blob = file_get_contents($filePath);

        // Insere o blob no banco de dados
        $sql = "INSERT INTO images (filename, filedata) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sb', $file, $blob);
        mysqli_stmt_execute($stmt);

        // Remove o arquivo após a conversão
        unlink($filePath);
    }
}

echo "Conversão concluída!";
?>
