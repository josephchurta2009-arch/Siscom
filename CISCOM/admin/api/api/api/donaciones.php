<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO donaciones 
            (nombre_donante, tipo_donacion, cantidad, unidad_medida, descripcion, fecha_donacion, como_se_repartio, beneficiarios_estimados, registrado_por) 
            VALUES (:nombre, :tipo, :cant, :unidad, :desc, :fecha, :repartio, :benef, :reg)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            ':nombre' => $data['nombre_donante'],
            ':tipo' => $data['tipo_donacion'],
            ':cant' => $data['cantidad'] ?: null,
            ':unidad' => $data['unidad_medida'] ?? null,
            ':desc' => $data['descripcion'] ?? null,
            ':fecha' => $data['fecha_donacion'],
            ':repartio' => $data['como_se_repartio'] ?? null,
            ':benef' => $data['beneficiarios_estimados'] ?? null,
            ':reg' => $data['registrado_por']
        ]);
        echo json_encode(['success' => true, 'message' => 'Donación registrada exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>