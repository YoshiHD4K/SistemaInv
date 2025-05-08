<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Correo'])) {
    $correo = $_POST['Correo'] ?? '';

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

    // Consulta para verificar si el correo existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirigir a una nueva página con el correo como variable en la URL
        header("Location: correo-forgotpassword.php?correo=" . urlencode($correo));
        exit();
    } else {
        echo "<script>alert('El correo no está registrado o esta mal escrito.');</script>";
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidaste la contraseña</title>
    <link rel="stylesheet" href="src/css/styles-login.css">
</head>

<body>

    <div class="parteizquierda">
        <h1>Bienvenidos</h1>
    </div>
    <div class="partederecha">
        <form action="forgotpassword.php" method="post">
            <div>
                <a href="index.php" class="btnvolver"><img src="src/images/volver.png" alt=""></a>
            </div>
            <h1>Recuperar Contraseña</h1>
            <div class="textInputWrapper">
                <input placeholder="Correo Electrónico" type="text" class="textInput" name="Correo" required>
            </div>
            <button class="btn" type="submit"> Recuperar Contraseña </button>
        </form>
    </div>

</body>

</html>