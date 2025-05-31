<?php
// buscar_producto.php
header('Content-Type: application/json');
require_once 'conexion.php'; // Asegúrate de tener un archivo de conexión mysqli $conn

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$resultados = [];

if ($term !== '') {
    $stmt = $conn->prepare("SELECT id, producto, cantidadDisp, precio FROM productos WHERE producto LIKE CONCAT('%', ?, '%') LIMIT 15");
    $stmt->bind_param('s', $term);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $resultados[] = $row;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($resultados);
