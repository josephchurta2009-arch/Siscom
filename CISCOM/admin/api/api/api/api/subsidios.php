<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM subsidios WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
} else {
    $stmt = $pdo->query("SELECT id, titulo, descripcion_corta, dirigido_a FROM subsidios WHERE activo = 1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>