<?php
// registrar_producto.php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'regular') {
    echo "<script>
            alert('Acceso denegado. Solo usuarios REGULAR pueden ingresar.');
            window.location.href = 'index.php';
          </script>";
    exit();
}

require_once 'conexion.php'; // Asegúrate de tener un archivo de conexión mysqli $conn

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = $_POST['nombre_producto'] ?? '';
    $descripcion = $_POST['descripcion_producto'] ?? '';
    $precio = $_POST['precio_producto'] ?? '';

    if ($producto && $descripcion && $precio !== '') {
        $stmt = $conn->prepare("INSERT INTO productos (`Producto`, `descripcion`, `precio`) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $producto, $descripcion, $precio);
        if ($stmt->execute()) {
            $mensaje = "Producto registrado exitosamente.";
        } else {
            $mensaje = "Error al registrar producto.";
        }
        $stmt->close();
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #bfc9d9; }
        h2 { text-align: center; color: #2d3e50; }
        form input, form button { width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #bfc9d9; }
        form button { background: #2d3e50; color: #fff; border: none; font-weight: bold; cursor: pointer; }
        form button:hover { background: #f9d923; color: #2d3e50; }
        .mensaje { text-align: center; color: green; margin-bottom: 10px; }
        .error { color: red; }
        a { display: block; text-align: center; margin-top: 15px; color: #2d3e50; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrar Producto</h2>
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo strpos($mensaje, 'exitosamente') === false ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
            <input type="text" name="descripcion_producto" placeholder="Descripción" required>
            <input type="number" step="0.01" name="precio_producto" placeholder="Precio" required>
            <button type="submit">Registrar Producto</button>
        </form>
        <a href="src/regular.php">Volver al menú principal</a>
    </div>
</body>
</html>
