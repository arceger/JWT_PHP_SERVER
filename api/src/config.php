<?php

$host = "127.0.0.1";
$db_name = "jwt_api";
$username = "root";
$password = ""; 

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Erro de conexão com banco",
        "error" => $e->getMessage()
    ]);
    die();
}
