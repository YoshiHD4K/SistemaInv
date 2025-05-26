<?php
// buscar_cliente.php
header('Content-Type: application/json');
require_once 'conexion.php';

// Permitir tanto GET como POST
$tipo = $_POST['tipo'] ?? $_GET['tipo'] ?? '';
$cedula = $_POST['cedula'] ?? $_GET['cedula'] ?? '';
$respuesta = [];

if ($tipo && $cedula) {
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE tipo = ? AND cedula = ? LIMIT 1");
    $stmt->bind_param('ss', $tipo, $cedula);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // Normaliza claves a minúsculas para el frontend JS
        $row = array_change_key_case($row, CASE_LOWER);
        $row['cedula'] = (string)$row['cedula'];
        $row['tipo'] = (string)$row['tipo'];
        $respuesta = $row;
    }

    $stmt->close();
}

$conn->close();
echo json_encode($respuesta);
