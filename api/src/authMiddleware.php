<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'minha_chave_secreta';

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

if ($token) {
    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $userId = $decoded->data->id;
        // O usuário foi autenticado com sucesso
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['message' => 'Acesso negado.']);
        die();
    }
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Token não fornecido.']);
    die();
}
?>
