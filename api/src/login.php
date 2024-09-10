<?php
require 'config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

$secretKey = 'minha_chave_secreta';  // Chave secreta usada para assinar o JWT

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $payload = [
        'sub' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'exp' => time() + (60 * 60)  // Token válido por 1 hora
    ];

    // Geração do JWT usando a chave secreta correta
    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    http_response_code(200);
    echo json_encode([
        'message' => 'Login bem-sucedido',
        'token' => $jwt,
        'name' => $user['nome'], 
        'role' => $user['role']   
    ]);
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Credenciais invalidas']);
}
?>
