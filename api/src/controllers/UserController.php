<?php
// src/controllers/UserController.php

class UserController {

    public static function getUser() {
        $userData = authMiddleware();  // Verifica a autenticação e obtém os dados do usuário
        
        echo json_encode([
            'id' => $userData->id,
            'username' => $userData->username
        ]);
    }
}
?>
