<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'regular') {
    echo "<script>
            alert('Acceso denegado. Solo usuarios REGULAR pueden ingresar.');
            window.location.href = '../index.php';
          </script>";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sisteminv";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Registrar proveedor en la tabla 'provedores'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_proveedor'])) {
    $nombre = $_POST['nombre_proveedor'];
    $direccion = $_POST['direccion_proveedor'];
    $telefono = $_POST['telefono_proveedor'];
    $rif = $_POST['rif_proveedor'];
    $stmt = $conn->prepare("INSERT INTO provedores (`nombre del proveedor`, `direccion del proveedor`, `nro de telefono del proveedor`, `rif del proveedor`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $direccion, $telefono, $rif);
    if ($stmt->execute()) {
        echo "<script>alert('Proveedor registrado exitosamente');</script>";
    } else {
        echo "<script>alert('Error al registrar proveedor');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Regular - SistemaInv</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/regular.css">
    <script src="src/js/regular.js"></script>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()" title="Expandir/Colapsar menú">&#9776;</button>
        <h2>StockWise<br><span style="font-size:0.8em;"> </span> <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1></h2>
        <ul>
            <li><a href="#" data-screen="pantalla-proveedores" class="active"><i class="fas fa-truck"></i><span>Crear Proveedores</span></a></li>
            <li><a href="#" data-screen="pantalla-productos"><i class="fas fa-box-open"></i><span>Registrar Productos</span></a></li>
            <li><a href="#" data-screen="pantalla-entrada"><i class="fas fa-sign-in-alt"></i><span>Registrar Entrada</span></a></li>
            <li><a href="#" data-screen="pantalla-salida"><i class="fas fa-sign-out-alt"></i><span>Registrar Salida</span></a></li>
            <li><a href="#" data-screen="pantalla-inventario"><i class="fas fa-warehouse"></i><span>Inventario</span></a></li>
            <li><a href="#" data-screen="pantalla-ordenes"><i class="fas fa-file-invoice"></i><span>Órdenes de Compra</span></a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="modulo-regular-titulo">
            <h1> Bienvenido a StockWise <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Usuario Regular)</h1>
        </div>
        <div id="pantalla-proveedores" class="pantalla">
            <h2>Crear Proveedores</h2>
            <form method="post">
                <input type="text" name="nombre_proveedor" placeholder="Nombre del proveedor" required>
                <input type="text" name="direccion_proveedor" placeholder="Dirección del proveedor" required>
                <input type="text" name="telefono_proveedor" placeholder="Nro de Teléfono del proveedor" required>
                <input type="text" name="rif_proveedor" placeholder="RIF del proveedor" required>
                <button type="submit" name="agregar_proveedor">Agregar Proveedor</button>
            </form>
        </div>
        <div id="pantalla-productos" class="pantalla">
            <h2>Registrar Productos</h2>
            <form id="form-producto">
                <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
                <input type="text" name="descripcion_producto" placeholder="Descripción" required>
                <input type="number" step="0.01" name="precio_producto" placeholder="Precio" required>
                <button type="submit" name="agregar_producto">Registrar Producto</button>
            </form>
        </div>
        <div id="pantalla-entrada" class="pantalla">
            <h2>Registrar Entrada de Productos</h2>
            <form>
                <input type="text" placeholder="Producto" required>
                <input type="number" placeholder="Cantidad" required>
                <button type="submit">Registrar Entrada</button>
            </form>
        </div>
        <div id="pantalla-salida" class="pantalla">
            <h2>Registrar Salida de Productos</h2>
            <form>
                <input type="text" placeholder="Producto" required>
                <input type="number" placeholder="Cantidad" required>
                <button type="submit">Registrar Salida</button>
            </form>
        </div>
        <div id="pantalla-inventario" class="pantalla">
            <h2>Manejar Inventario</h2>
            <p>Inventario actual...</p>
        </div>
        <div id="pantalla-ordenes" class="pantalla">
            <h2>Crear Órdenes de Compra</h2>
            <form>
                <input type="text" placeholder="Proveedor" required>
                <input type="text" placeholder="Producto" required>
                <input type="number" placeholder="Cantidad" required>
                <button type="submit">Crear Orden</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
