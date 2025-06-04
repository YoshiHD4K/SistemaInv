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

// Registrar proveedor en la tabla 'Proveedores'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_proveedor'])) {
    $nombre = $_POST['nombre_proveedor'];
    $direccion = $_POST['direccion_proveedor'];
    $prefijo = $_POST['prefijo_telefono'];
    $numero = $_POST['numero_telefono'];
    $telefono = $prefijo . $numero;
    $prefijo_rif = $_POST['prefijo_rif'];
    $rif_num = $_POST['rif_proveedor'];
    $rif = $prefijo_rif . $rif_num;
    // Verificar si ya existe un proveedor con el mismo nombre o RIF
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM proveedores WHERE Nombre = ? OR Rif = ?");
    $check_stmt->bind_param("ss", $nombre, $rif);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();
    if ($count > 0) {
        echo "<script>alert('Ya existe un proveedor con ese nombre o RIF');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO proveedores (`Nombre`, `Direccion`, `Telefono`, `Rif`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $direccion, $telefono, $rif);
        if ($stmt->execute()) {
            echo "<script>alert('Proveedor registrado exitosamente');</script>";
        } else {
            echo "<script>alert('Error al registrar proveedor');</script>";
        }
        $stmt->close();
    }
}

// Registrar producto en la tabla 'Productos'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion_producto = $_POST['descripcion_producto'];
    $precio_producto = $_POST['precio_producto'];

    // Verificar si ya existe un producto con el mismo nombre
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM productos WHERE Nombre = ?");
    $check_stmt->bind_param("s", $nombre_producto);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "<script>alert('Ya existe un producto con ese nombre');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO productos (Nombre, Descripcion, Precio) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $nombre_producto, $descripcion_producto, $precio_producto);
        if ($stmt->execute()) {
            echo "<script>alert('Producto registrado exitosamente');</script>";
        } else {
            echo "<script>alert('Error al registrar producto');</script>";
        }
        $stmt->close();
    }
}

// Registrar entrada de productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_entrada']) && isset($_POST['cantidad_entrada'])) {
    $nombre_producto = $_POST['producto_entrada'];
    $cantidad = intval($_POST['cantidad_entrada']);
    // Buscar si el producto existe
    $stmt = $conn->prepare("SELECT Cantidad FROM productos WHERE Nombre = ?");
    $stmt->bind_param("s", $nombre_producto);
    $stmt->execute();
    $stmt->bind_result($cantidad_actual);
    if ($stmt->fetch()) {
        $stmt->close();
        // Sumar la cantidad
        $nueva_cantidad = $cantidad_actual + $cantidad;
        $update_stmt = $conn->prepare("UPDATE productos SET Cantidad = ? WHERE Nombre = ?");
        $update_stmt->bind_param("is", $nueva_cantidad, $nombre_producto);
        if ($update_stmt->execute()) {
            echo "<script>alert('Entrada registrada y cantidad actualizada');</script>";
        } else {
            echo "<script>alert('Error al actualizar la cantidad');</script>";
        }
        $update_stmt->close();
    } else {
        $stmt->close();
        echo "<script>alert('Producto no encontrado');</script>";
    }
}

// Registrar salida de productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_salida']) && isset($_POST['cantidad_salida'])) {
    $nombre_producto = $_POST['producto_salida'];
    $cantidad = intval($_POST['cantidad_salida']);
    // Buscar si el producto existe
    $stmt = $conn->prepare("SELECT Cantidad FROM productos WHERE Nombre = ?");
    $stmt->bind_param("s", $nombre_producto);
    $stmt->execute();
    $stmt->bind_result($cantidad_actual);
    if ($stmt->fetch()) {
        $stmt->close();
        if ($cantidad_actual < $cantidad) {
            echo "<script>alert('No hay suficiente stock para realizar la salida');</script>";
        } else {
            // Restar la cantidad
            $nueva_cantidad = $cantidad_actual - $cantidad;
            $update_stmt = $conn->prepare("UPDATE productos SET Cantidad = ? WHERE Nombre = ?");
            $update_stmt->bind_param("is", $nueva_cantidad, $nombre_producto);
            if ($update_stmt->execute()) {
                echo "<script>alert('Salida registrada y cantidad actualizada');</script>";
            } else {
                echo "<script>alert('Error al actualizar la cantidad');</script>";
            }
            $update_stmt->close();
        }
    } else {
        $stmt->close();
        echo "<script>alert('Producto no encontrado');</script>";
    }
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
    <style>
        /* Mensaje de bienvenida pequeño y en la esquina superior derecha */
        .bienvenida-mini {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 1em;
            color: #2d3e50;
            background: rgba(255, 255, 255, 0.85);
            padding: 6px 18px;
            border-radius: 6px;
            box-shadow: 0 2px 8px #bfc9d9;
            z-index: 200;
            margin: 0;
        }

        .main-content {
            position: relative;
        }

        /* Animación de movimiento para las opciones de la barra lateral */
        .sidebar ul li a {
            transition: background 0.2s, color 0.2s, transform 0.25s cubic-bezier(.4,2,.6,1);
            /* Efecto suave y rebote */
        }
        .sidebar ul li a:hover, .sidebar ul li a:focus {
            background: #f9d923;
            color: #2d3e50;
            transform: translateX(12px) scale(1.06) rotate(-2deg);
            box-shadow: 0 4px 16px rgba(66,107,150,0.10);
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()" title="Expandir/Colapsar menú">&#9776;</button>
        <h2>StockWise<br><span style="font-size:0.8em;"> </span> <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
        <ul>
            <li><a href="#" data-screen="pantalla-proveedores" class="active"><i class="fas fa-truck"></i><span>Crear Proveedores</span></a></li>
            <li><a href="#" data-screen="pantalla-productos"><i class="fas fa-box-open"></i><span>Registrar Productos</span></a></li>
            <li><a href="#" data-screen="pantalla-entrada"><i class="fas fa-sign-in-alt"></i><span>Registrar Entrada</span></a></li>
            <li><a href="#" data-screen="pantalla-salida"><i class="fas fa-sign-out-alt"></i><span>Registrar Salida</span></a></li>
            <li><a href="#" data-screen="pantalla-inventario"><i class="fas fa-warehouse"></i><span>Inventario</span></a></li>
            <!-- <li><a href="#" data-screen="pantalla-ordenes"><i class="fas fa-file-invoice"></i><span>Órdenes de Compra</span></a></li> -->
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="bienvenida-mini">
            Bienvenido a StockWise, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
        </div>
        <!-- Elimina o comenta el h1 de bienvenida grande -->
        <!-- <div class="modulo-regular-titulo">
            <h1> Bienvenido a StockWise <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Usuario Regular)</h1>
        </div> -->
        <div id="pantalla-proveedores" class="pantalla">
            <h2>Crear Proveedores</h2>
            <form method="post">
                <div class="nombre">
                    <input type="text" name="nombre_proveedor" placeholder="Nombre del Proveedor" required>
                </div>
                <div class="direccion">
                    <input type="text" name="direccion_proveedor" placeholder="Dirección del Proveedor" required>
                </div>
                <div class="telefono">
                    <select name="prefijo_telefono" required>
                        <option value="0412">0412</option>
                        <option value="0414">0414</option>
                        <option value="0416">0416</option>
                        <option value="0424">0424</option>
                        <option value="0426">0426</option>
                        <option value="0212">0212</option>
                    </select>
                    <input type="text" name="numero_telefono" placeholder="Nro de Teléfono" pattern="[0-9]{7}" maxlength="7" required>
                </div>
                <div class="rif">
                    <select name="prefijo_rif" required>
                        <option value="V">V</option>
                        <option value="E">E</option>
                        <option value="J">J</option>
                        <option value="G">G</option>
                    </select>
                    <input type="text" name="rif_proveedor" placeholder="RIF del Proveedor" required>
                </div>
                <button type="submit" name="agregar_proveedor">Agregar Proveedor</button>
            </form>
        </div>
        <div id="pantalla-productos" class="pantalla">
            <h2>Registrar Productos</h2>
            <form id="form-producto" method="post">
                <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
                <input type="text" name="descripcion_producto" placeholder="Descripción" required>
                <input type="number" step="0.01" name="precio_producto" placeholder="Precio" required>
                <button type="submit" name="agregar_producto">Registrar Producto</button>
            </form>
        </div>
        <div id="pantalla-entrada" class="pantalla">
            <h2>Registrar Entrada de Productos</h2>
            <form method="post">
                <div class="autocomplete-producto">
                    <input type="text" id="producto_entrada" name="producto_entrada" placeholder="Producto" autocomplete="off" required>
                    <div id="busqueda-productos" class="busqueda-productos"></div>
                </div>
                <input type="number" name="cantidad_entrada" placeholder="Cantidad" required>
                <button type="submit">Registrar Entrada</button>
            </form>
        </div>
        <div id="pantalla-salida" class="pantalla">
            <h2>Registrar Salida de Productos</h2>
            <form method="post">
                <div class="autocomplete-producto">
                    <input type="text" id="producto_salida" name="producto_salida" placeholder="Producto" autocomplete="off" required>
                    <div id="busqueda-productos-salida" class="busqueda-productos"></div>
                </div>
                <input type="number" name="cantidad_salida" placeholder="Cantidad" required>
                <button type="submit">Registrar Salida</button>
            </form>
        </div>
        <div id="pantalla-inventario" class="pantalla">
            <h2>Manejar Inventario</h2>
            <table class="tabla-inventario">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Costo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT Nombre, Descripcion, Precio, Cantidad FROM productos");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['Nombre']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Descripcion']) . '</td>';
                            echo '<td>' .'$ '. htmlspecialchars($row['Precio']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Cantidad']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No hay productos en inventario</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- <div id="pantalla-ordenes" class="pantalla">
            <h2>Crear Órdenes de Compra</h2>
            <form>
                <input type="text" placeholder="Proveedor" required>
                <input type="text" placeholder="Producto" required>
                <input type="number" placeholder="Cantidad" required>
                <button type="submit">Crear Orden</button>
            </form>
        </div> -->
    </div>
</body>

</html>

<?php $conn->close(); ?>