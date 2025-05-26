<?php
// editar_cliente.php
header('Content-Type: application/json');
require_once 'conexion.php';

// Permitir tanto JSON como x-www-form-urlencoded
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}
$id = $data['id'] ?? '';
$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$telefono = $data['telefono'] ?? '';
$direccion = $data['direccion'] ?? '';

$respuesta = ['success' => false];

if ($id && $nombre && $apellido) {
    $stmt = $conn->prepare("UPDATE clientes SET nombre=?, apellido=?, telefono=?, direccion=? WHERE id=?");
    $stmt->bind_param('ssssi', $nombre, $apellido, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        $respuesta['success'] = true;
        $respuesta['msg'] = 'Cliente editado correctamente';
    } else {
        $respuesta['msg'] = 'Error al editar cliente';
    }

    $stmt->close();
}

$conn->close();
echo json_encode($respuesta);
