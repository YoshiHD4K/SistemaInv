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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_proveedor'])) {
    $nombre = $_POST['nombre_proveedor'];
    $direccion = $_POST['direccion_proveedor'];
    $telefono = $_POST['telefono_proveedor'];
    $rif = $_POST['rif_proveedor'];
    $stmt = $conn->prepare("INSERT INTO provedores (nombre del proveedor, direccion del proveedor, nro de telefono del proveedor, rif del proveedor) VALUES (?, ?, ?, ?)");
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
    <style>
        body {
            background: #f4f6fb;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 230px;
            height: 100%;
            background: #2d3e50;
            color: #fff;
            padding-top: 30px;
            transition: width 0.3s ease;
            z-index: 100;
            box-shadow: 2px 0 8px #bfc9d9;
            overflow: hidden;
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar .toggle-btn {
            position: absolute;
            top: 15px;
            right: 10px;
            background:#f9d923;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            border-radius: 50%;
            width: 40px;
            height: 40px;
    
            transition: right 0.3s;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1em;
            letter-spacing: 1px;
            transition: opacity 0.3s ease;
        }
        .sidebar.collapsed h2 {
            opacity: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.08em;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
            overflow: hidden;
        }

        .sidebar ul li a i {
            min-width: 20px;
            font-size: 1.2em;
        }

        .sidebar ul li a span {
            margin-left: 10px;
            white-space: nowrap;
            transition: opacity 0.3s ease, max-width 0.3s ease;
            opacity: 1;
            max-width: 150px;
            display: inline-block;
            overflow: hidden;
        }

        .sidebar.collapsed ul li a span {
            opacity: 0;
            max-width: 0;
            margin-left: 0;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #f9d923;
            color: #2d3e50;
        }

        .main-content {
            margin-left: 230px;
            padding: 30px 40px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 60px;
        }

        .modulo-regular-titulo {
            color: #2d3e50;
            background: #e3e7ef;
            padding: 20px 0 10px 0;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 8px #bfc9d9;
        }

        .pantalla {
            display: none;
            background: #fff;
            margin: 30px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #bfc9d9;
            padding: 25px 30px;
        }

        .pantalla.active {
            display: block;
        }

        .pantalla h2 {
            color: #2d3e50;
            margin-bottom: 15px;
        }

        form input,
        form button {
            margin: 8px 0;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #bfc9d9;
            font-size: 1em;
        }

        form button {
            background: rgb(66, 107, 150);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            box-shadow:
                0 8px 24px rgba(66, 107, 150, 0.35),   /* sombra principal más profunda */
                0 2.5px 0 #bfc9d9;                     /* borde inferior más marcado */
            /* Sombra para efecto 3D */
            position: relative;
        }

        form button:hover {
            background: #f9d923;
            color: #2d3e50;
            box-shadow:
                0 12px 32px rgba(249, 217, 35, 0.35),  /* sombra más profunda al hacer hover */
                0 4px 0 #bfc9d9;
            transform: translateY(-3px) scale(1.06);
        }

        form button:active {
            background: #e3e7ef;
            color: #2d3e50;
            box-shadow:
                0 4px 12px rgba(66, 107, 150, 0.22),
                0 1.5px 0 #bfc9d9;
            transform: translateY(2px) scale(0.97);
        }

        @media (max-width: 800px) {
            .main-content {
                margin-left: 60px;
                padding: 15px 5px;
            }
            .sidebar {
                width: 60px;
            }
            .sidebar.collapsed {
                width: 0;
            }
        }
    </style>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('collapsed');
        }

        function showScreen(screenId, link) {
            document.querySelectorAll('.pantalla').forEach(function(sec) {
                sec.classList.remove('active');
            });
            document.getElementById(screenId).classList.add('active');
            document.querySelectorAll('.sidebar ul li a').forEach(function(a) {
                a.classList.remove('active');
            });
            link.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('pantalla-proveedores').classList.add('active');
            document.querySelectorAll('.sidebar ul li a[data-screen]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    showScreen(this.getAttribute('data-screen'), this);
                });
            });
        });
    </script>
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
