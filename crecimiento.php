<?php
require_once 'config.php';

// Crear tabla si no existe (ejecutar una vez)
$pdo->exec("CREATE TABLE IF NOT EXISTS crecimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lider_id INT,
    fecha_observacion DATE,
    nuevas_familias INT DEFAULT 0,
    nuevas_viviendas INT DEFAULT 0,
    mejoras_infraestructura TEXT,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lider_id) REFERENCES lideres(id)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO crecimiento (lider_id, fecha_observacion, nuevas_familias, nuevas_viviendas, mejoras_infraestructura, observaciones) 
            VALUES (:lid, :fecha, :fam, :viv, :mejoras, :obs)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':lid' => $data['lider_id'],
        ':fecha' => $data['fecha_observacion'],
        ':fam' => $data['nuevas_familias'],
        ':viv' => $data['nuevas_viviendas'],
        ':mejoras' => $data['mejoras_infraestructura'],
        ':obs' => $data['observaciones']
    ]);
    echo json_encode(['success' => true, 'message' => 'Crecimiento registrado']);
}
?>