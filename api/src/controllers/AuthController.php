<?php
// src/controllers/AuthController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

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
    $secretKey = "meu_segredo_super_forte";

    $payload = [
        'iss' => "localhost",
        'aud' => "localhost",
        'iat' => time(),
        'exp' => time() + 3600,
        'data' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ];

    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    echo json_encode([
        'token' => $jwt,
        'user' => [
            'email' => $user['email'],
            'username' => $user['username'],
            'role' => $user['role']
        ]
    ]);
}else {
            http_response_code(401);
            echo json_encode(['message' => 'Credenciais inválidas.']);
        }
    }

    public static function register() {
    global $pdo;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["message" => "JSON inválido"]);
        return;
    }

    $email = $data['email'] ?? null;
    $passwordRaw = $data['password'] ?? null;
    $role = $data['role'] ?? null;
    $username = $data['username'] ?? null;
    $tel = $data['tel'] ?? null;
    $city = $data['city'] ?? null;
    $endereco = $data['endereco'] ?? null;

    if (!$email || !$passwordRaw || !$role || !$username) {
        http_response_code(400);
        echo json_encode(["message" => "Campos obrigatorios em falta"]);
        return;
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users 
            (email, password, role, username, tel, city, endereco)
            VALUES 
            (:email, :password, :role, :username, :tel, :city, :endereco)
        ");

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':endereco', $endereco);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuario registrado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao registrar usuario."]);
        }

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(409);
            echo json_encode(["message" => "Email ja existe"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro no servidor", "error" => $e->getMessage()]);
        }
    }
}

/*
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
    } */
}
?>
