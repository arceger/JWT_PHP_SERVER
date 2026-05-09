<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authMiddleware() {

    $secretKey = 'minha_chave_secreta';
    //$secretKey = $_ENV['JWT_SECRET'] ?? 'minha_chave_secreta';

    // Compatibilidade com diferentes ambientes
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    $authHeader = $headers['Authorization'] 
        ?? $headers['authorization'] 
        ?? '';

    // Extrai o token
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["message" => "Token NAO fornecido."]);
        exit;
    }

    $token = $matches[1];

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

        // Retorna os dados do token para uso posterior
        return $decoded;

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Token inválido ou expirado."]);
        exit;
    }
}