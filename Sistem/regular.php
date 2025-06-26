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

// --- DASHBOARD MÉTRICAS ---
/**
 * Muestra el Dashboard con métricas clave.
 * @param mysqli $conn Conexión activa a la BD.
 */
function mostrarDashboard($conn) {
    // Total de proveedores
    $totalProv = 0;
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM proveedores");
    if ($res && $row = $res->fetch_assoc()) {
        $totalProv = intval($row['cnt']);
    }

    // Total de productos
    $totalProd = 0;
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM productos");
    if ($res && $row = $res->fetch_assoc()) {
        $totalProd = intval($row['cnt']);
    }

    // Stock total (suma de Cantidad)
    $stockTotal = 0;
    $res = $conn->query("SELECT SUM(Cantidad) AS suma FROM productos");
    if ($res && $row = $res->fetch_assoc()) {
        $stockTotal = intval($row['suma']);
    }

    // Valor aproximado de inventario: SUM(Precio * Cantidad)
    $valorInv = 0.0;
    $res = $conn->query("SELECT SUM(Precio * Cantidad) AS valor FROM productos");
    if ($res && $row = $res->fetch_assoc()) {
        $valorInv = floatval($row['valor']);
    }

    // Productos bajos en stock
    $umbral = 5;
    $bajos = [];
    $stmt = $conn->prepare("SELECT Nombre, Cantidad FROM productos WHERE Cantidad < ?");
    $stmt->bind_param("i", $umbral);
    if ($stmt->execute()) {
        $res2 = $stmt->get_result();
        while ($r = $res2->fetch_assoc()) {
            $bajos[] = $r;
        }
    }
    $stmt->close();

    // Renderizar las tarjetas
    echo '<div class="dashboard-panel">';
    echo '<h2><i class="fas fa-tachometer-alt"></i> DASHBOARD</h2>';
    echo '<div class="cards-container">';
    // Tarjeta: total proveedores
    echo '<div class="card">';
    echo '<h3>Proveedores</h3>';
    echo '<p class="metric">' . htmlspecialchars($totalProv) . '</p>';
    echo '</div>';
    // Tarjeta: total productos
    echo '<div class="card">';
    echo '<h3>Productos</h3>';
    echo '<p class="metric">' . htmlspecialchars($totalProd) . '</p>';
    echo '</div>';
    // Tarjeta: stock total
    echo '<div class="card">';
    echo '<h3>Stock Total</h3>';
    echo '<p class="metric">' . htmlspecialchars($stockTotal) . '</p>';
    echo '</div>';
    // Tarjeta: valor inventario
    echo '<div class="card">';
    echo '<h3>Valor Inventario</h3>';
    echo '<p class="metric">$ ' . number_format($valorInv, 2, ',', '.') . '</p>';
    echo '</div>';
    echo '</div>'; // .cards-container

    // Si hay productos bajos en stock, mostrar breve alerta o lista pequeña
    if (!empty($bajos)) {
        echo '<div class="low-stock-alert">';
        echo '<h4>Productos con stock < ' . $umbral . '</h4>';
        echo '<ul>';
        foreach ($bajos as $item) {
            echo '<li>' . htmlspecialchars($item['Nombre']) . ' ('.intval($item['Cantidad']).')</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="low-stock-alert ok">';
        echo '<p>Todos los productos con stock suficiente 👍</p>';
        echo '</div>';
    }

    echo '</div>'; // .dashboard-panel

    // --- GRÁFICO DE INVENTARIO ---
    // Obtener productos con más y menos inventario (top 5 y bottom 5)
    $productos_mas = [];
    $productos_menos = [];
    $resMas = $conn->query("SELECT Nombre, Cantidad FROM productos ORDER BY Cantidad DESC LIMIT 5");
    while ($row = $resMas->fetch_assoc()) {
        $productos_mas[] = $row;
    }
    $resMenos = $conn->query("SELECT Nombre, Cantidad FROM productos ORDER BY Cantidad ASC LIMIT 5");
    while ($row = $resMenos->fetch_assoc()) {
        $productos_menos[] = $row;
    }

    // Preparar datos para JS
    $labels_mas = [];
    $data_mas = [];
    foreach ($productos_mas as $p) {
        $labels_mas[] = $p['Nombre'];
        $data_mas[] = intval($p['Cantidad']);
    }
    $labels_menos = [];
    $data_menos = [];
    foreach ($productos_menos as $p) {
        $labels_menos[] = $p['Nombre'];
        $data_menos[] = intval($p['Cantidad']);
    }

    echo '<div style="margin:32px 0 32px 0;display:flex;flex-wrap:wrap;gap:40px;">';
    echo '<div style="flex:1;min-width:320px;">';
    echo '<h3 style="margin-bottom:10px;">Top 5 productos con más inventario</h3>';
    echo '<canvas id="graficoMasInventario" height="180"></canvas>';
    echo '</div>';
    echo '<div style="flex:1;min-width:320px;">';
    echo '<h3 style="margin-bottom:10px;">Top 5 productos con menos inventario</h3>';
    echo '<canvas id="graficoMenosInventario" height="180"></canvas>';
    echo '</div>';
    echo '</div>';

    // Pasar datos a JS
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Más inventario
            new Chart(document.getElementById("graficoMasInventario").getContext("2d"), {
                type: "bar",
                data: {
                    labels: ' . json_encode($labels_mas) . ',
                    datasets: [{
                        label: "Cantidad",
                        data: ' . json_encode($data_mas) . ',
                        backgroundColor: "#2196F3"
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
            // Menos inventario
            new Chart(document.getElementById("graficoMenosInventario").getContext("2d"), {
                type: "bar",
                data: {
                    labels: ' . json_encode($labels_menos) . ',
                    datasets: [{
                        label: "Cantidad",
                        data: ' . json_encode($data_menos) . ',
                        backgroundColor: "#FF9800"
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>';
    
}
// --- FIN DASHBOARD MÉTRICAS ---

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
            echo "<script>alert('Proveedor registrado exitosamente');window.location.href=window.location.href;</script>";
            exit();
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
            echo "<script>alert('Producto registrado exitosamente');window.location.href=window.location.href;</script>";
            exit();
        } else {
            echo "<script>alert('Error al registrar producto');</script>";
        }
        $stmt->close();
    }
}

// Registrar entrada de productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_entrada']) && isset($_POST['cantidad_entrada']) && !isset($_POST['agregar_producto']) && !isset($_POST['agregar_proveedor'])) {
    $producto = $_POST['producto_entrada'];
    $cantidad = intval($_POST['cantidad_entrada']);
    $precio = isset($_POST['precio_entrada']) ? floatval($_POST['precio_entrada']) : 0.0;
    $fecha_ingreso = isset($_POST['fecha_ingreso']) && $_POST['fecha_ingreso'] !== '' ? $_POST['fecha_ingreso'] : date('Y-m-d');

    // Buscar si el producto existe y obtener su descripción y cantidad actual
    $stmt = $conn->prepare("SELECT Descripcion, Cantidad, Precio FROM productos WHERE Nombre = ?");
    $stmt->bind_param("s", $producto);
    $stmt->execute();
    $stmt->bind_result($descripcion, $cantidad_actual, $precio_actual);
    if ($stmt->fetch()) {
        $stmt->close();
        $nueva_cantidad = $cantidad_actual + $cantidad;

        // Actualizar cantidad y precio si es necesario
        if ($precio != $precio_actual) {
            $update_stmt = $conn->prepare("UPDATE productos SET Cantidad = ?, Precio = ? WHERE Nombre = ?");
            $update_stmt->bind_param("ids", $nueva_cantidad, $precio, $producto);
        } else {
            $update_stmt = $conn->prepare("UPDATE productos SET Cantidad = ? WHERE Nombre = ?");
            $update_stmt->bind_param("is", $nueva_cantidad, $producto);
        }

        if (!$update_stmt->execute()) {
            echo "<script>alert('Error al actualizar la cantidad');</script>";
            $update_stmt->close();
            return;
        }
        $update_stmt->close();

        // Registrar en reporte_entradas con fecha de ingreso
        $stmt_reporte = $conn->prepare("INSERT INTO reporte_entradas (`Nombre`, `Descripcion`, `Precio`, `Cantidad`, `Fecha_ingreso`) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt_reporte) {
            echo "<script>alert('Error en prepare reporte_entradas: " . $conn->error . "');</script>";
            return;
        }
        $stmt_reporte->bind_param('ssdis', $producto, $descripcion, $precio, $cantidad, $fecha_ingreso);
        if (!$stmt_reporte->execute()) {
            echo "<script>alert('Error al insertar en reporte_entradas: " . $stmt_reporte->error . "');</script>";
            $stmt_reporte->close();
            return;
        }
        $stmt_reporte->close();

        echo "<script>alert('Entrada registrada y guardada en el historial');window.location.href=window.location.href;</script>";
        exit();
    } else {
        $stmt->close();
        echo "<script>alert('Producto no encontrado');</script>";
    }
}

// Registrar salida de productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_salida']) && isset($_POST['cantidad_salida'])) {
    $nombre_producto = $_POST['producto_salida'];
    $cantidad = intval($_POST['cantidad_salida']);
    $fecha_salida = isset($_POST['fecha_salida']) && $_POST['fecha_salida'] !== '' ? $_POST['fecha_salida'] : date('Y-m-d');

    // Buscar si el producto existe y obtener descripción y precio
    $stmt = $conn->prepare("SELECT Descripcion, Cantidad, Precio FROM productos WHERE Nombre = ?");
    $stmt->bind_param("s", $nombre_producto);
    $stmt->execute();
    $stmt->bind_result($descripcion, $cantidad_actual, $precio_actual);
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
                $update_stmt->close();

                // Registrar en reporte_salidas
                $stmt_reporte = $conn->prepare("INSERT INTO reporte_salidas (`Nombre`, `Descripcion`, `Precio`, `Cantidad`, `fecha_salida`) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt_reporte) {
                    echo "<script>alert('Error en prepare reporte_salidas: " . $conn->error . "');</script>";
                    return;
                }
                $stmt_reporte->bind_param('ssdis', $nombre_producto, $descripcion, $precio_actual, $cantidad, $fecha_salida);
                if (!$stmt_reporte->execute()) {
                    echo "<script>alert('Error al insertar en reporte_salidas: " . $stmt_reporte->error . "');</script>";
                    $stmt_reporte->close();
                    return;
                }
                $stmt_reporte->close();

                echo "<script>alert('Salida registrada y guardada en el historial');window.location.href=window.location.href;</script>";
                exit();
            } else {
                echo "<script>alert('Error al actualizar la cantidad');</script>";
                $update_stmt->close();
            }
        }
    } else {
        $stmt->close();
        echo "<script>alert('Producto no encontrado');</script>";
    }
}

// --- INICIO Dashboard de Usuario ---
$sqlPedidos = "SELECT COUNT(*) as total_pedidos FROM pedidos WHERE id_usuario = ?";
$sqlUsuario = "SELECT nombre FROM usuarios WHERE id = ?";

$totalPedidos = 0;
$nombreUsuario = "Usuario";

if (isset($_SESSION['id'])) {
    // Consulta de pedidos
    $stmtPedidos = $conn->prepare($sqlPedidos);
    $stmtPedidos->bind_param("i", $_SESSION['id']);
    $stmtPedidos->execute();
    $resultPedidos = $stmtPedidos->get_result();
    if ($resultPedidos && $resultPedidos->num_rows > 0) {
        $row = $resultPedidos->fetch_assoc();
        $totalPedidos = $row['total_pedidos'];
    }
    $stmtPedidos->close();

    // Consulta de nombre de usuario
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bind_param("i", $_SESSION['id']);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();
    if ($resultUsuario && $resultUsuario->num_rows > 0) {
        $row = $resultUsuario->fetch_assoc();
        $nombreUsuario = $row['nombre'];
    }
    $stmtUsuario->close();
}
// --- FIN Dashboard de Usuario ---
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<head>
    <meta charset="UTF-8">
    <title>Módulo Regular - SistemaInv</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/regular.css">
    <script src="src/js/regular.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: #023ebe;
            color: #ffffff;
            /* Movimiento hacia adelante (escala) y ligeramente ascendente (sube) */
            transform: translateY(-8px) scale(1.10);
            box-shadow: 0 8px 24px rgba(66,107,150,0.12);
        }
    </style>
    <style>
        #logo-stockwise-bottom {
            position: fixed;
            right: 18px;
            bottom: 18px;
            width: 110px; /* Aumenta el tamaño */
            height: auto;
            z-index: 300;
            opacity: 0.70; /* Más transparencia */
            pointer-events: none;
            background: transparent;
            transition: opacity 0.3s;
        }
        #logo-stockwise-bottom:hover {
            opacity: 0.90; /* Menos transparencia al pasar el mouse */
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()" title="Expandir/Colapsar menú">&#9776;</button>
        <h2>StockWise<br><span style="font-size:0.8em;"> </span> <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
        <ul>
            <li><a href="#" data-screen="pantalla-dashboard"><i class="fas fa-chart-line"></i><span>Dashboard</span></a></li>
            <li><a href="#" data-screen="pantalla-proveedores"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>
            <li><a href="#" data-screen="pantalla-productos"><i class="fas fa-box-open"></i><span>Registrar Productos</span></a></li>
            <li><a href="#" data-screen="pantalla-entrada"><i class="fas fa-sign-in-alt"></i><span>Registrar Entrada</span></a></li>
            <li><a href="#" data-screen="pantalla-salida"><i class="fas fa-sign-out-alt"></i><span>Registrar Salida</span></a></li>
            <li><a href="#" data-screen="pantalla-inventario"><i class="fas fa-warehouse"></i><span>Inventario</span></a></li>
            <li><a href="#" data-screen="pantalla-reportes"><i class="fas fa-file-alt"></i><span>Reportes</span></a></li>
            <li><a href="#" data-screen="pantalla-configuracion"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
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
        <div id="pantalla-dashboard" class="pantalla">
            <?php mostrarDashboard($conn); ?>
        </div>
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
            <!-- Tabla de proveedores registrados -->
             <button onclick="generarPDFProveedores()" style="background-color: #023ebe; color: white; padding: 10px 18px; margin-top: 20px; margin-bottom: 10px; border: none; border-radius: 5px; cursor: pointer;">
    <i class="fas fa-file-pdf"></i> Descargar Proveedores en PDF
</button>

            <h3 style="margin-top:30px;">Proveedores Registrados</h3>
            <table id="tabla-proveedores" class="tabla-inventario">

                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>RIF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultProv = $conn->query("SELECT Nombre, Direccion, Telefono, Rif FROM proveedores");
                    if ($resultProv && $resultProv->num_rows > 0) {
                        while ($prov = $resultProv->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($prov['Nombre']) . '</td>';
                            echo '<td>' . htmlspecialchars($prov['Direccion']) . '</td>';
                            echo '<td>' . htmlspecialchars($prov['Telefono']) . '</td>';
                            echo '<td>' . htmlspecialchars($prov['Rif']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No hay proveedores registrados</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="pantalla-productos" class="pantalla">
            <h2>Registrar Productos</h2>
            <form id="form-producto" method="post">
                <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
                <input type="text" name="descripcion_producto" placeholder="Descripción" required>
                <input type="number" step="0.01" name="precio_producto" placeholder="Precio" required>
                <button type="submit" name="agregar_producto">Registrar Producto</button>
            </form>
            <!-- Tabla de productos registrados -->
            <h3 style="margin-top:30px;">Productos Registrados</h3>
            <table class="tabla-inventario">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultProd = $conn->query("SELECT Nombre, Descripcion, Precio FROM productos");
                    if ($resultProd && $resultProd->num_rows > 0) {
                        while ($prod = $resultProd->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($prod['Nombre']) . '</td>';
                            echo '<td>' . htmlspecialchars($prod['Descripcion']) . '</td>';
                            echo '<td>$ ' . htmlspecialchars($prod['Precio']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">No hay productos registrados</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="pantalla-entrada" class="pantalla">
            <h2>Registrar Entrada de Productos</h2>
            <form method="post">
                <div class="autocomplete-producto">
                    <input type="text" id="producto_entrada" name="producto_entrada" placeholder="Producto" autocomplete="off" required
                        value="<?php echo isset($_POST['producto_entrada']) ? htmlspecialchars($_POST['producto_entrada']) : ''; ?>">
                    <div id="busqueda-productos" class="busqueda-productos"></div>
                </div>
                <input type="number" name="cantidad_entrada" placeholder="Cantidad" required
                    value="<?php echo isset($_POST['cantidad_entrada']) ? htmlspecialchars($_POST['cantidad_entrada']) : ''; ?>">
                <?php
                // Mostrar el precio actual si el producto existe
                $precio_mostrar = '';
                if (isset($_POST['producto_entrada']) && $_POST['producto_entrada'] !== '') {
                    $nombre_producto = $_POST['producto_entrada'];
                    $stmt = $conn->prepare("SELECT Precio FROM productos WHERE Nombre = ?");
                    $stmt->bind_param("s", $nombre_producto);
                    $stmt->execute();
                    $stmt->bind_result($precio_bd);
                    if ($stmt->fetch()) {
                        $precio_mostrar = $precio_bd;
                    }
                    $stmt->close();
                }
                ?>
                <input type="number" step="0.01" name="precio_entrada" placeholder="Precio" 
                    value="<?php echo htmlspecialchars($precio_mostrar); ?>" required>
                <input type="date" name="fecha_ingreso" placeholder="Fecha de ingreso" value="<?php echo isset($_POST['fecha_ingreso']) ? htmlspecialchars($_POST['fecha_ingreso']) : date('Y-m-d'); ?>" required>
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
                <input type="date" name="fecha_salida" placeholder="Fecha de salida" value="<?php echo isset($_POST['fecha_salida']) ? htmlspecialchars($_POST['fecha_salida']) : date('Y-m-d'); ?>" required>
                <button type="submit">Registrar Salida</button>
            </form>
        </div>
        <div id="pantalla-inventario" class="pantalla">
            <h2>Manejar Inventario</h2>
            <button onclick="generarPDFInventario()" style="background-color: #023ebe; color: white; padding: 10px 18px; margin-bottom: 18px; border: none; border-radius: 5px; cursor: pointer;">
    <i class="fas fa-file-pdf"></i> Descargar Inventario en PDF
</button>

            <table id="tabla-inventario" class="tabla-inventario">

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
        <div id="pantalla-reportes" class="pantalla">
    <h2>Generar Reporte de Movimientos</h2>
    <form method="post" action="Reportes.php" target="_blank">
        <label>
            Tipo de reporte:
            <select name="tipo_reporte" required>
                <option value="entradas">Entradas de Productos</option>
                <option value="salidas">Salidas de Productos</option>
            </select>
        </label>
        <label>
            Fecha:
            <input type="date" name="fecha_reporte" required>
        </label>
        <button type="submit" name="generar_reporte"><i class="fas fa-file-pdf"></i> Generar Reporte</button>
    </form>
    <p style="margin-top:18px;color:#888;font-size:0.98em;">
        Selecciona el tipo de movimiento y la fecha para descargar un reporte detallado en PDF.
    </p>
</div>
        <div id="pantalla-configuracion" class="pantalla">
            <h2>Configuración de Usuario - Cambiar Contraseña</h2>
            <form method="post">
                <input type="password" name="contrasena_actual" placeholder="Contraseña Actual" required>
                <input type="password" name="nueva_contrasena" placeholder="Nueva Contraseña" minlength="6" required>
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar Nueva Contraseña" required>
                <button type="submit" name="cambiar_contrasena">Cambiar Contraseña</button>
            </form>
        </div>
    </div>

    <!-- Logo en la esquina inferior derecha -->
    <img src="src/images/white bg no bg.png" alt="StockWise Logo" id="logo-stockwise-bottom">

    <!-- Mensaje de copyright -->
    <div id="footer-msg" style="
        position: fixed;
        left: 250px;
        bottom: 22px;
        font-size: 1.08em;
        color: #2d3e50;
        background: rgba(255,255,255,0.85);
        padding: 7px 18px 7px 14px;
        border-radius: 8px;
        box-shadow: 0 2px 8px #bfc9d9;
        z-index: 301;
        opacity: 0.92;
        pointer-events: none;
        ">
        2025 StockWise &#169
    </div>

</body>

<?php
// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    $usuario = $_SESSION['usuario'];
    $actual = $_POST['contrasena_actual'];
    $nueva = $_POST['nueva_contrasena'];
    $confirmar = $_POST['confirmar_contrasena'];

    if (strlen($nueva) < 6) {
        echo "<script>alert('La nueva contraseña debe tener al menos 6 caracteres.');</script>";
    } elseif ($nueva !== $confirmar) {
        echo "<script>alert('La confirmación no coincide con la nueva contraseña.');</script>";
    } else {
        $stmt = $conn->prepare("SELECT Contraseña FROM usuarios WHERE `Nombre_Usuario` = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->bind_result($contrasena_bd);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($actual === $contrasena_bd) {
                $update_stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE `Nombre_Usuario` = ?");
                $update_stmt->bind_param("ss", $nueva, $usuario);
                if ($update_stmt->execute()) {
                    echo "<script>alert('Cambio de contraseña exitoso.');</script>";
                } else {
                    echo "<script>alert('Error al actualizar la contraseña.');</script>";
                }
                $update_stmt->close();
            } else {
                echo "<script>alert('La contraseña actual es incorrecta.');</script>";
            }
        } else {
            echo "<script>alert('Usuario no encontrado.');</script>";
        }
    }
}
?><?php
$conn->close();
?>

<script>
document.addEventListener('scroll', function() {
    // Calcula el porcentaje de scroll vertical
    const scrollTop = window.scrollY || window.pageYOffset;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const percent = docHeight > 0 ? scrollTop / docHeight : 0;
    // Mueve el fondo en función del scroll
    document.body.style.backgroundPosition = `0 ${percent * 100}%`;
    document.body.classList.add('scrolling-gradient');
});
</script>
<script>
    async function generarPDFInventario() {
        const { jsPDF } = window.jspdf;

        const tabla = document.querySelector("#tabla-inventario");
        const fecha = new Date();
        const fechaHora = fecha.toLocaleString();

        const logoImg = new Image();
        logoImg.src = 'src/images/StockWise 4 white bg.png'; // Ajusta la ruta si es diferente

        logoImg.onload = () => {
            html2canvas(tabla, {
                backgroundColor: "#ffffff", // fondo blanco
                scale: 2
            }).then(canvas => {
                const pdf = new jsPDF({
                    orientation: "portrait",
                    unit: "mm",
                    format: "a4"
                });

                const imgDataTabla = canvas.toDataURL("image/png");
                const pdfWidth = pdf.internal.pageSize.getWidth();

                const logoWidth = 50;
                const logoHeight = (logoImg.height / logoImg.width) * logoWidth;
                const centerX = (pdfWidth - logoWidth) / 2;

                pdf.addImage(logoImg, 'PNG', centerX, 10, logoWidth, logoHeight);
                pdf.setFontSize(12);
                pdf.text(`Inventario generado el: ${fechaHora}`, 10, logoHeight + 20);

                const imgProps = pdf.getImageProperties(imgDataTabla);
                const pdfHeight = (imgProps.height * (pdfWidth - 20)) / imgProps.width;
                pdf.addImage(imgDataTabla, 'PNG', 10, logoHeight + 30, pdfWidth - 20, pdfHeight);

                pdf.save(`Inventario_${fecha.toISOString().slice(0, 10)}.pdf`);
            });
        };

        logoImg.onerror = () => {
            alert("Error al cargar el logo de StockWise. Verifica la ruta.");
        };
    }
</script>
<script>
    async function generarPDFProveedores() {
        const { jsPDF } = window.jspdf;

        const tabla = document.querySelector("#tabla-proveedores");
        const fecha = new Date();
        const fechaHora = fecha.toLocaleString();

        const logoImg = new Image();
        logoImg.src = 'src/images/StockWise 4 white bg.png'; // Verifica esta ruta

        logoImg.onload = () => {
            html2canvas(tabla, {
                backgroundColor: "#ffffff", // fondo blanco
                scale: 2
            }).then(canvas => {
                const pdf = new jsPDF({
                    orientation: "portrait",
                    unit: "mm",
                    format: "a4"
                });

                const imgDataTabla = canvas.toDataURL("image/png");
                const pdfWidth = pdf.internal.pageSize.getWidth();

                const logoWidth = 50;
                const logoHeight = (logoImg.height / logoImg.width) * logoWidth;
                const centerX = (pdfWidth - logoWidth) / 2;

                pdf.addImage(logoImg, 'PNG', centerX, 10, logoWidth, logoHeight);
                pdf.setFontSize(12);
                pdf.text(`Listado de Proveedores generado el: ${fechaHora}`, 10, logoHeight + 20);

                const imgProps = pdf.getImageProperties(imgDataTabla);
                const pdfHeight = (imgProps.height * (pdfWidth - 20)) / imgProps.width;
                pdf.addImage(imgDataTabla, 'PNG', 10, logoHeight + 30, pdfWidth - 20, pdfHeight);

                pdf.save(`Proveedores_${fecha.toISOString().slice(0, 10)}.pdf`);
            });
        };

        logoImg.onerror = () => {
            alert("Error al cargar el logo de StockWise. Verifica la ruta.");
        };
    }
</script>

<style>
  .tabla-inventario th {
    background: #023ebe !important;
    color: #ffffff !important;
  }
</style>

</html>