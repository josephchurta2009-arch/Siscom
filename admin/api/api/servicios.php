<?php
require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$lider_id = $_GET['lider_id'] ?? null;
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM servicios_comunitarios WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } elseif ($lider_id) {
        $stmt = $pdo->prepare("SELECT * FROM servicios_comunitarios WHERE creado_por = ?");
        $stmt->execute([$lider_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        // Para página pública: todos los activos
        $stmt = $pdo->query("SELECT * FROM servicios_comunitarios WHERE activo = 1");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO servicios_comunitarios (nombre, tipo, direccion, latitud, longitud, horario, telefono, descripcion, activo, creado_por) 
            VALUES (:nombre, :tipo, :dir, :lat, :lng, :horario, :tel, :desc, :activo, :creador)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $data['nombre'],
        ':tipo' => $data['tipo'],
        ':dir' => $data['direccion'],
        ':lat' => $data['latitud'],
        ':lng' => $data['longitud'],
        ':horario' => $data['horario'],
        ':tel' => $data['telefono'],
        ':desc' => $data['descripcion'],
        ':activo' => $data['activo'],
        ':creador' => $data['creado_por']
    ]);
    echo json_encode(['success' => true, 'message' => 'Servicio creado']);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "UPDATE servicios_comunitarios SET nombre=:nombre, tipo=:tipo, direccion=:dir, latitud=:lat, longitud=:lng, horario=:horario, telefono=:tel, descripcion=:desc, activo=:activo WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $data['nombre'],
        ':tipo' => $data['tipo'],
        ':dir' => $data['direccion'],
        ':lat' => $data['latitud'],
        ':lng' => $data['longitud'],
        ':horario' => $data['horario'],
        ':tel' => $data['telefono'],
        ':desc' => $data['descripcion'],
        ':activo' => $data['activo'],
        ':id' => $id
    ]);
    echo json_encode(['success' => true, 'message' => 'Servicio actualizado']);
} elseif ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM servicios_comunitarios WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
?>