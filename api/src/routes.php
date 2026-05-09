<?php
// src/routes.php

require_once __DIR__ . '/authMiddleware.php';  // proteger rotas
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Roteamento básico
switch ($requestUri) {
    // Rota de login
    case '/login':
        if ($requestMethod === 'POST') {
            AuthController::login();
        } else {
            http_response_code(405);  //não permitido
        }
        break;

    // Rota de registro
    case '/register':
        if ($requestMethod === 'POST') {
            AuthController::register();
        } else {
            http_response_code(405);  //não permitido
        }
        break;

    // Rota protegida
    case '/user':
        if ($requestMethod === 'GET') {
            authMiddleware();  // verificar o token JWT
            UserController::getUser();
        } else {
            http_response_code(405);  // não permitido
        }
        break;

    // Qualquer outra rota
    default:
        http_response_code(404);  //não encontrada
        echo json_encode(["message" => "Rota não encontrada"]);
        break;
}
?>
