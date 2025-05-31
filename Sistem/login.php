<?php
session_start();
$_SESSION['usuario'] = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['Usuario'] ?? '';
    $contraseña = $_POST['Contraseña'] ?? '';

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root"; // Cambia si tienes un usuario diferente
    $password = ""; // Cambia si tienes una contraseña para el usuario root
    $dbname = "sisteminv";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta para verificar usuario y contraseña
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Nombre_Usuario = ? AND Contraseña = ?");
    $stmt->bind_param("ss", $usuario, $contraseña);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['tipo'] = $user['Tipo'];
        if ($user['Tipo'] == 'cajero') {
            echo "<script>
                alert('Inicio de sesión exitoso. Bienvenido, " . htmlspecialchars($usuario) . "!');
                window.location.href = 'cajero.php';
                </script>";
        } elseif ($user['Tipo'] == 'regular') {
            echo "<script>
                alert('Inicio de sesión exitoso. Bienvenido, " . htmlspecialchars($usuario) . "!');
                window.location.href = 'regular.php';
                </script>";
        } else {
            echo "<script>
                alert('Tipo de usuario no reconocido.');
                window.location.href = 'index.php';
                </script>";
        }
    } else {
        // Mostrar un alert de error y redirigir a index.php
        echo "<script>
                alert('Usuario o contraseña incorrectos.');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
}
?>