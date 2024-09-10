<?php

require_once __DIR__ . '/config.php';

$data = json_decode(file_get_contents("php://input"), true);

// Recebendo os dados do JSON
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $data['role'];  // Recebendo o papel do usuário no registro
$nome = $data['nome'];
$tel = $data['tel'];
$city = $data['city'];
$endereco = $data['endereco'];

// Validando o papel do usuário
$validRoles = ['admin', 'tecnico']; 
if (!in_array($role, $validRoles)) {
    http_response_code(400);
    echo json_encode(["message" => "Papel invalido fornecido."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role, nome, tel, city, endereco) VALUES (:email, :password, :role, :nome, :tel, :city, :endereco)");
    
    // Vinculando os parâmetros
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
    // Verifica se o erro é de duplicação de chave única
    if ($e->getCode() == 23000) { // Código de erro SQL para duplicação de entrada
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