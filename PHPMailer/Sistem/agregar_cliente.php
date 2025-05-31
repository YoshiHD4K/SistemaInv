<?php
// agregar_cliente.php
header('Content-Type: application/json');
require_once 'conexion.php';

// Permitir tanto JSON como x-www-form-urlencoded
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}
$tipo = $data['tipo'] ?? '';
$cedula = $data['cedula'] ?? '';
$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$telefono = $data['telefono'] ?? '';
$direccion = $data['direccion'] ?? '';

$respuesta = ['success' => false];

if ($tipo && $cedula && $nombre && $apellido) {
    // Verificar si ya existe
    $stmt = $conn->prepare('SELECT 1 FROM clientes WHERE Tipo = ? AND Cedula = ? LIMIT 1');
    $stmt->bind_param('ss', $tipo, $cedula);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $respuesta['msg'] = 'El cliente ya existe';
        $stmt->close();
        $conn->close();
        echo json_encode($respuesta);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO clientes (tipo, cedula, nombre, apellido, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $tipo, $cedula, $nombre, $apellido, $telefono, $direccion);
    if ($stmt->execute()) {
        $respuesta['success'] = true;
        $respuesta['msg'] = 'Cliente agregado correctamente';
        $respuesta['id'] = $conn->insert_id;
    } else {
        $respuesta['msg'] = 'Error al agregar cliente';
    }
    $stmt->close();
}

$conn->close();
echo json_encode($respuesta);
