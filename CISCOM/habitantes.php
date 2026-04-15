<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$lider_id = $_GET['lider_id'] ?? null;
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM habitantes WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } elseif ($lider_id) {
        $stmt = $pdo->prepare("SELECT id, nombre_completo, numero_documento, sector, nivel_vulnerabilidad, tipo_persona FROM habitantes WHERE registrado_por = ? ORDER BY fecha_registro DESC");
        $stmt->execute([$lider_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    // (Código de inserción que ya teníamos, igual)
    // ...
    echo json_encode(['success' => true, 'message' => 'Habitante registrado']);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "UPDATE habitantes SET nombre_completo=:nombre, numero_documento=:doc, sector=:sector, nivel_vulnerabilidad=:nivel WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $data['nombre_completo'],
        ':doc' => $data['numero_documento'],
        ':sector' => $data['sector'],
        ':nivel' => $data['nivel_vulnerabilidad'],
        ':id' => $id
    ]);
    echo json_encode(['success' => true, 'message' => 'Habitante actualizado']);
} elseif ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM habitantes WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
?>