<?php
// api/auth.php
require_once 'config.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Leer datos JSON del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email y contraseña son obligatorios']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nombre, email, password_hash, sector FROM lideres WHERE email = ?");
    $stmt->execute([$email]);
    $lider = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lider && password_verify($password, $lider['password_hash'])) {
        // No enviar el hash al frontend
        unset($lider['password_hash']);
        echo json_encode(['success' => true, 'lider' => $lider]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
}
?>