<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO habitantes (nombre_completo, tipo_documento, numero_documento, fecha_nacimiento, genero, telefono, direccion, sector, tipo_persona, puntaje_sisben, es_desplazado, es_victima_conflicto, nivel_vulnerabilidad, registrado_por) 
            VALUES (:nombre, :tipo_doc, :num_doc, :fecha_nac, :genero, :tel, :dir, :sector, :tipo_per, :sisben, :desplazado, :victima, :nivel, :reg_por)";
    
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            ':nombre' => $data['nombre_completo'],
            ':tipo_doc' => $data['tipo_documento'],
            ':num_doc' => $data['numero_documento'],
            ':fecha_nac' => $data['fecha_nacimiento'] ?: null,
            ':genero' => $data['genero'] ?? null,
            ':tel' => $data['telefono'] ?? null,
            ':dir' => $data['direccion'] ?? null,
            ':sector' => $data['sector'] ?? null,
            ':tipo_per' => $data['tipo_persona'],
            ':sisben' => $data['puntaje_sisben'] ?: null,
            ':desplazado' => $data['es_desplazado'],
            ':victima' => $data['es_victima_conflicto'],
            ':nivel' => $data['nivel_vulnerabilidad'],
            ':reg_por' => $data['registrado_por']
        ]);
        echo json_encode(['success' => true, 'message' => 'Habitante registrado exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>