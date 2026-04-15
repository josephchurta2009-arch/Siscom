<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$lider_id = $_GET['lider_id'] ?? null;
$id = $_GET['id'] ?? null;
$public = $_GET['public'] ?? null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM donaciones WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } elseif ($lider_id) {
        $stmt = $pdo->prepare("SELECT * FROM donaciones WHERE registrado_por = ? ORDER BY fecha_donacion DESC");
        $stmt->execute([$lider_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($public) {
        // Para página pública: solo donaciones con distribución registrada
        $stmt = $pdo->query("SELECT fecha_donacion, nombre_donante, tipo_donacion, cantidad, unidad_medida, como_se_repartio FROM donaciones WHERE como_se_repartio IS NOT NULL AND como_se_repartio != '' ORDER BY fecha_donacion DESC LIMIT 50");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
} elseif ($method === 'POST') {
    // (Código de inserción ya existente)
    // ...
    echo json_encode(['success' => true]);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "UPDATE donaciones SET nombre_donante=:nombre, tipo_donacion=:tipo, cantidad=:cant, unidad_medida=:unidad, descripcion=:desc, fecha_donacion=:fecha, como_se_repartio=:repartio, beneficiarios_estimados=:benef WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $data['nombre_donante'],
        ':tipo' => $data['tipo_donacion'],
        ':cant' => $data['cantidad'],
        ':unidad' => $data['unidad_medida'],
        ':desc' => $data['descripcion'],
        ':fecha' => $data['fecha_donacion'],
        ':repartio' => $data['como_se_repartio'],
        ':benef' => $data['beneficiarios_estimados'],
        ':id' => $id
    ]);
    echo json_encode(['success' => true]);
} elseif ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM donaciones WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
?>