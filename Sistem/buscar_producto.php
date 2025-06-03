<?php
// buscar_producto.php
header('Content-Type: application/json');

if (!isset($_GET['term'])) {
    echo json_encode(["error" => "No se recibió el parámetro 'term'"]);
    exit;
}

$term = $_GET['term'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sisteminv";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

$stmt = $conn->prepare("SELECT Nombre FROM productos WHERE Nombre LIKE CONCAT('%', ?, '%') LIMIT 10");
if (!$stmt) {
    echo json_encode(["error" => "Error en prepare: " . $conn->error]);
    $conn->close();
    exit;
}
$stmt->bind_param("s", $term);
if (!$stmt->execute()) {
    echo json_encode(["error" => "Error en execute: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$result = $stmt->get_result();
if (!$result) {
    echo json_encode(["error" => "Error en get_result: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = ["nombre" => $row["Nombre"]];
}
$stmt->close();
$conn->close();
echo json_encode($productos);
