<?php
// api/auth.php
require_once __DIR__ . '/../config.php';

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

    $validPassword = false;
    if ($lider) {
        $storedPassword = $lider['password_hash'];
        if (password_verify($password, $storedPassword)) {
            $validPassword = true;
        } elseif ($password === $storedPassword) {
            // Contraseña almacenada en texto plano, migrar a hash seguro
            $validPassword = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE lideres SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $lider['id']]);
            $lider['password_hash'] = $newHash;
        }
    }

    if ($validPassword) {
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