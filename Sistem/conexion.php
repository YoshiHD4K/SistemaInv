<?php
// conexion.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sisteminv';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}
// $conn está disponible para los scripts que incluyan este archivo
