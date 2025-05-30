<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'regular') {
    echo "<script>
            alert('Acceso denegado. Solo usuarios REGULAR pueden ingresar.');
            window.location.href = '../index.php';
          </script>";
    exit();
}

// Conexión a la base de datos
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Regular - SistemaInv</title>
    <link rel="stylesheet" href="../src/css/cajero.css">
    <style>
        /* Personalización para distinguir el módulo regular, pero usando la base de cajero.css */
        body {
            background: #f4f6fb;
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
        .modulo-regular-nav {
            background: #2d3e50;
            padding: 10px 0;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #bfc9d9;
        }
        .modulo-regular-nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 0;
            padding: 0;
        }
        .modulo-regular-nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }
        .modulo-regular-nav a:hover {
            color: #f9d923;
        }
        section {
            background: #fff;
            margin: 30px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #bfc9d9;
            padding: 25px 30px;
        }
        section h2 {
            color: #2d3e50;
            margin-bottom: 15px;
        }
        form input, form button {
            margin: 8px 0;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #bfc9d9;
            font-size: 1em;
        }
        form button {
            background: #2d3e50;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        form button:hover {
            background: #f9d923;
            color: #2d3e50;
        }
    </style>
</head>
<body>
    <div class="modulo-regular-titulo">
        <h1>Bienvenido <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Usuario Regular)</h1>
    </div>
    <nav class="modulo-regular-nav">
        <ul>
            <li><a href="#proveedores">Crear Proveedores</a></li>
            <li><a href="#entrada">Registrar Entrada de Productos</a></li>
            <li><a href="#salida">Registrar Salida de Productos</a></li>
            <li><a href="#inventario">Manejar Inventario</a></li>
            <li><a href="#ordenes">Crear Ordenes de Compra</a></li>
        </ul>
    </nav>
    <section id="proveedores">
        <h2>Crear Proveedores</h2>
        <form method="post">
            <input type="text" name="nombre_proveedor" placeholder="Nombre del proveedor" required>
            <input type="text" name="direccion_proveedor" placeholder="Dirección del proveedor" required>
            <input type="text" name="telefono_proveedor" placeholder="Nro de Teléfono del proveedor" required>
            <input type="text" name="rif_proveedor" placeholder="RIF del proveedor" required>
            <button type="submit" name="agregar_proveedor">Agregar Proveedor</button>
        </form>
    </section>
    <section id="entrada">
        <h2>Registrar Entrada de Productos</h2>
        <form>
            <input type="text" placeholder="Producto" required>
            <input type="number" placeholder="Cantidad" required>
            <button type="submit">Registrar Entrada</button>
        </form>
    </section>
    <section id="salida">
        <h2>Registrar Salida de Productos</h2>
        <form>
            <input type="text" placeholder="Producto" required>
            <input type="number" placeholder="Cantidad" required>
            <button type="submit">Registrar Salida</button>
        </form>
    </section>
    <section id="inventario">
        <h2>Manejar Inventario</h2>
        <!-- Aquí se mostraría el inventario con opciones para editar/eliminar -->
        <p>Inventario actual...</p>
    </section>
    <section id="ordenes">
        <h2>Crear Ordenes de Compra</h2>
        <form>
            <input type="text" placeholder="Proveedor" required>
            <input type="text" placeholder="Producto" required>
            <input type="number" placeholder="Cantidad" required>
            <button type="submit">Crear Orden</button>
        </form>
    </section>
</body>
</html>
<?php $conn->close(); ?>