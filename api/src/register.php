<?php

require_once __DIR__ . '/config.php';

$data = json_decode(file_get_contents("php://input"), true);


$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $data['role'];  
$nome = $data['nome'];
$tel = $data['tel'];
$city = $data['city'];
$endereco = $data['endereco'];


$validRoles = ['admin', 'tecnico']; 
if (!in_array($role, $validRoles)) {
    http_response_code(400);
    echo json_encode(["message" => "Papel invalido fornecido."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role, nome, tel, city, endereco) VALUES (:email, :password, :role, :nome, :tel, :city, :endereco)");
    
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':endereco', $endereco);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Usuario registrado com sucesso!"]);
    } else {
        throw new Exception("Falha ao registrar usuário.");
    }
} catch (PDOException $e) {
    
    if ($e->getCode() == 23000) { 
        http_response_code(409);
        echo json_encode(["message" => "Email ja registrado."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Erro no servidor.", "error" => $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro no servidor.", "error" => $e->getMessage()]);
}

?>