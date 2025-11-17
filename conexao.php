<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=fretmax;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // NÃO pode dar echo nem die aqui!
    error_log("Erro na conexão: " . $e->getMessage());
    http_response_code(500);
    exit; // encerra sem imprimir nada
}
?>
