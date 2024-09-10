<?php
// src/routes.php

require_once __DIR__ . '/authMiddleware.php';  // Middleware para proteger rotas
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';

// Obtenha a URI da requisição
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Roteamento básico
switch ($requestUri) {
    // Rota de login
    case '/login':
        if ($requestMethod === 'POST') {
            AuthController::login();
        } else {
            http_response_code(405);  // Método não permitido
        }
        break;

    // Rota de registro
    case '/register':
        if ($requestMethod === 'POST') {
            AuthController::register();
        } else {
            http_response_code(405);  // Método não permitido
        }
        break;

    // Rota protegida (exemplo de rota para obter dados do usuário)
    case '/user':
        if ($requestMethod === 'GET') {
            authMiddleware();  // Middleware para verificar o token JWT
            UserController::getUser();
        } else {
            http_response_code(405);  // Método não permitido
        }
        break;

    // Qualquer outra rota
    default:
        http_response_code(404);  // Rota não encontrada
        echo json_encode(["message" => "Rota não encontrada"]);
        break;
}
?>
