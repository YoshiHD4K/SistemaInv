<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'regular') {
    echo "<script>alert('Acceso denegado.');window.close();</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['fecha_reporte']) || !isset($_POST['tipo_reporte'])) {
    echo "Acceso inválido.";
    exit();
}

$fecha = $_POST['fecha_reporte'];
$tipo_reporte = $_POST['tipo_reporte'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sisteminv";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($tipo_reporte === 'entradas') {
    $titulo = "Reporte de Entradas de Productos";
    // Consulta para obtener los datos desde fecha_ingreso
    $sql = "SELECT Nombre AS producto, Descripcion, Precio, Cantidad, Fecha_ingreso 
            FROM reporte_entradas 
            WHERE DATE(Fecha_ingreso) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $column_fecha = 'Fecha_ingreso';
    $th_fecha = 'Fecha de Ingreso';
    $color = '#023ebe';
} else {
    $titulo = "Reporte de Salidas de Productos";
    // Consulta para obtener los datos de reporte_salidas para esa fecha
    $sql = "SELECT Nombre AS producto, Descripcion, Precio, Cantidad, fecha_salida 
            FROM reporte_salidas 
            WHERE DATE(fecha_salida) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $column_fecha = 'fecha_salida';
    $th_fecha = 'Fecha de Salida';
    $color = '#b30000';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fbff; margin: 0; padding: 30px;}
        h2 { color: <?= $color ?>; }
        table { border-collapse: collapse; width: 100%; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #bfc9d9; overflow: hidden;}
        th, td { padding: 10px 12px; border-bottom: 1px solid #e0e6ef; text-align: left;}
        th { background: <?= $color ?>; color: #fff;}
        tr:last-child td { border-bottom: none; }
        .logo { width: 120px; margin-bottom: 18px;}
        .footer { margin-top: 32px; color: #888; font-size: 0.98em;}
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <img src="src/images/StockWise 4 white bg.png" class="logo" alt="StockWise">
    <h2><?= htmlspecialchars($titulo) ?></h2>
    <p><strong>Fecha seleccionada:</strong> <?= htmlspecialchars($fecha) ?></p>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th><?= htmlspecialchars($th_fecha) ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['producto']) ?></td>
                <td><?= htmlspecialchars($row['Descripcion']) ?></td>
                <td><?= number_format($row['Precio'], 2) ?></td>
                <td><?= intval($row['Cantidad']) ?></td>
                <td><?= htmlspecialchars($row[$column_fecha]) ?></td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td colspan="5">
                    <?php
                    if ($tipo_reporte === 'entradas') {
                        echo "No hay registros para esta fecha.";
                    } else {
                        echo "No hay registros de salidas para esta fecha.";
                    }
                    ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <button class="no-print" onclick="window.print()" style="margin-top:22px;padding:10px 18px;background:<?= $color ?>;color:#fff;border:none;border-radius:5px;cursor:pointer;">
        Imprimir o Guardar como PDF
    </button>
    <div class="footer">
        Reporte generado por StockWise &copy; <?= date('Y') ?>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
