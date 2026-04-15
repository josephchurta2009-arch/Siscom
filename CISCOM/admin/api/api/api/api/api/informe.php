<?php
require_once 'config.php';

$lider_id = $_GET['lider_id'] ?? 0;
$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

// Total habitantes del líder
$stmt = $pdo->prepare("SELECT COUNT(*) FROM habitantes WHERE registrado_por = ?");
$stmt->execute([$lider_id]);
$total_habitantes = $stmt->fetchColumn();

// Conteo por vulnerabilidad
$stmt = $pdo->prepare("SELECT nivel_vulnerabilidad, COUNT(*) as c FROM habitantes WHERE registrado_por = ? GROUP BY nivel_vulnerabilidad");
$stmt->execute([$lider_id]);
$vuln = ['alta'=>0,'media'=>0,'baja'=>0];
while($row = $stmt->fetch()) $vuln[$row['nivel_vulnerabilidad']] = $row['c'];

// Donaciones del mes
$stmt = $pdo->prepare("SELECT COUNT(*), SUM(cantidad) FROM donaciones WHERE registrado_por = ? AND MONTH(fecha_donacion) = ? AND YEAR(fecha_donacion) = ?");
$stmt->execute([$lider_id, $mes, $anio]);
$donaciones = $stmt->fetch();
$total_donaciones = $donaciones[0];
$valor_donaciones = $donaciones[1];

// Servicios activos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios_comunitarios WHERE creado_por = ? AND activo = 1");
$stmt->execute([$lider_id]);
$servicios_activos = $stmt->fetchColumn();

// Crecimiento (nuevos registros este mes)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM habitantes WHERE registrado_por = ? AND MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = ?");
$stmt->execute([$lider_id, $mes, $anio]);
$crecimiento = $stmt->fetchColumn();

// Distribución por tipo
$stmt = $pdo->prepare("SELECT tipo_persona, COUNT(*) as cantidad FROM habitantes WHERE registrado_por = ? GROUP BY tipo_persona");
$stmt->execute([$lider_id]);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Detalle donaciones
$stmt = $pdo->prepare("SELECT nombre_donante, tipo_donacion, cantidad, unidad_medida FROM donaciones WHERE registrado_por = ? AND MONTH(fecha_donacion) = ? AND YEAR(fecha_donacion) = ?");
$stmt->execute([$lider_id, $mes, $anio]);
$donaciones_detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total_habitantes' => $total_habitantes,
    'alta' => $vuln['alta'],
    'media' => $vuln['media'],
    'baja' => $vuln['baja'],
    'total_donaciones' => $total_donaciones,
    'valor_donaciones' => $valor_donaciones,
    'servicios_activos' => $servicios_activos,
    'crecimiento' => $crecimiento,
    'tipos_persona' => $tipos,
    'donaciones_detalle' => $donaciones_detalle
]);
?>