<?php
// src/controllers/AuthController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

class AuthController {

    public static function login() {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'];
        $password = $data['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $secretKey = $_ENV['JWT_SECRET'];
            $payload = [
                'iss' => "localhost",
                'aud' => "localhost",
                'iat' => time(),
                'exp' => time() + 3600,  // Token válido por 1 hora
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ]
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Credenciais inválidas.']);
        }
    }

    public static function register() {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário registrado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao registrar usuário."]);
        }
    }
}
?>
