<?php
require_once 'config.php';

$lider_id = $_GET['lider_id'] ?? 0;

// Total habitantes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM habitantes WHERE registrado_por = ?");
$stmt->execute([$lider_id]);
$total_habitantes = $stmt->fetchColumn();

// Conteo por vulnerabilidad
$stmt = $pdo->prepare("SELECT nivel_vulnerabilidad, COUNT(*) as cantidad FROM habitantes WHERE registrado_por = ? GROUP BY nivel_vulnerabilidad");
$stmt->execute([$lider_id]);
$vuln = ['alta'=>0,'media'=>0,'baja'=>0];
while($row = $stmt->fetch()) $vuln[$row['nivel_vulnerabilidad']] = $row['cantidad'];

// Donaciones del mes actual
$stmt = $pdo->prepare("SELECT COUNT(*) FROM donaciones WHERE registrado_por = ? AND MONTH(fecha_donacion) = MONTH(CURDATE())");
$stmt->execute([$lider_id]);
$donaciones_mes = $stmt->fetchColumn();

// Servicios activos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios_comunitarios WHERE creado_por = ? AND activo = 1");
$stmt->execute([$lider_id]);
$servicios_activos = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'total_habitantes' => $total_habitantes,
    'alta_vulnerabilidad' => $vuln['alta'],
    'media' => $vuln['media'],
    'baja' => $vuln['baja'],
    'donaciones_mes' => $donaciones_mes,
    'servicios_activos' => $servicios_activos,
    'meses' => ['Ene','Feb','Mar','Abr'], // Placeholder
    'evolucion' => [8,15,22,30]
]);
?>