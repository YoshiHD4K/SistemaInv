<?php
// registrar_entrada.php
header('Content-Type: application/json');
require_once 'conexion.php';

// Permitir tanto JSON como x-www-form-urlencoded
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$producto = $data['producto'] ?? '';
$descripcion = $data['descripcion'] ?? '';
$cantidad = $data['cantidad'] ?? '';
$fecha = $data['fecha'] ?? date('Y-m-d'); // Solo para mostrar, no se guarda
$proveedor = $data['proveedor'] ?? '';     // Solo para mostrar, no se guarda

$respuesta = ['success' => false];

// Solo guardar producto, descripcion y cantidad en la tabla inventario
if ($producto && $descripcion && $cantidad !== '') {
    $stmt = $conn->prepare("INSERT INTO inventario (`Producto`, `descripcion`, `cantidad`) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $producto, $descripcion, $cantidad);
    if ($stmt->execute()) {
        $respuesta['success'] = true;
        $respuesta['msg'] = 'Entrada de producto registrada correctamente';
        $respuesta['id'] = $conn->insert_id;
        // También puedes devolver la fecha y proveedor para mostrar en pantalla si lo necesitas
        $respuesta['fecha'] = $fecha;
        $respuesta['proveedor'] = $proveedor;
    } else {
        $respuesta['msg'] = 'Error al registrar la entrada de producto';
    }
    $stmt->close();
} else {
    $respuesta['msg'] = 'Todos los campos son obligatorios';
}

$conn->close();
echo json_encode($respuesta);
