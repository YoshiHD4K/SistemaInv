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
$precio = isset($data['precio']) ? floatval($data['precio']) : 0.0;
$cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 0;
$fecha = $data['fecha'] ?? date('Y-m-d'); 
$proveedor = $data['proveedor'] ?? '';

$respuesta = ['success' => false];

if ($producto && $descripcion && $cantidad > 0) {
    $conn->begin_transaction();

    try {
        // Insertar en inventario
        $stmt = $conn->prepare("INSERT INTO inventario (`Producto`, `descripcion`, `cantidad`) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error en prepare inventario: " . $conn->error);
        }
        $stmt->bind_param('ssi', $producto, $descripcion, $cantidad);
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar en inventario: " . $stmt->error);
        }
        $stmt->close();

        // Insertar en reporte_entradas
        $stmt_reporte = $conn->prepare("INSERT INTO reporte_entradas (`Nombre`, `Descripcion`, `Precio`, `Cantidad`) VALUES (?, ?, ?, ?)");
        if (!$stmt_reporte) {
            throw new Exception("Error en prepare reporte_entradas: " . $conn->error);
        }
        $stmt_reporte->bind_param('ssdi', $producto, $descripcion, $precio, $cantidad);
        if (!$stmt_reporte->execute()) {
            throw new Exception("Error al insertar en reporte_entradas: " . $stmt_reporte->error);
        }
        $stmt_reporte->close();

        $conn->commit();

        $respuesta['success'] = true;
        $respuesta['msg'] = 'Entrada de producto registrada y guardada en el historial';
        $respuesta['fecha'] = $fecha;
        $respuesta['proveedor'] = $proveedor;

    } catch (Exception $e) {
        $conn->rollback();
        $respuesta['msg'] = $e->getMessage();
    }

} else {
    $respuesta['msg'] = 'Todos los campos son obligatorios y cantidad debe ser mayor a 0';
}

$conn->close();
echo json_encode($respuesta);
