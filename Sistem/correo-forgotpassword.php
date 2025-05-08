<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '..\vendor\autoload.php'; // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['correo'])) {
    $correo = $_GET['correo'];

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

    // Consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT Nombre_Usuario, Contraseña FROM usuarios WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc(); // Obtener los datos del usuario
        $nombreUsuario = $usuario['Nombre_Usuario'];
        $contraseña = $usuario['Contraseña'];

        // Configuración de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Cambia esto según tu proveedor de correo
            $mail->SMTPAuth = true;
            $mail->Username = 'sistemainventario30@gmail.com'; // Tu correo electrónico
            $mail->Password = 'lcqo skti nggt hpar'; // Tu contraseña (o App Password si usas Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom('sistemainventario30@gmail.com', 'Sistema Inventario'); // Remitente
            $mail->addAddress($correo); // Destinatario

            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de clave de acceso';
            $mail->CharSet = 'UTF-8'; // Establecer la codificación de caracteres a UTF-8
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color:rgb(209, 209, 209); padding: 20px; border-radius: 5px;'>
                    <h1 style='color: #4CAF50; text-align: center;'>Hola $nombreUsuario,</h1>
                    <p style='font-size: 16px;'>
                        Has solicitado recuperar tu contraseña. A continuación, encontrarás tus datos de acceso:
                    </p>
                    <div style='background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                        <p><strong>Nombre de usuario:</strong> $nombreUsuario</p>
                        <p><strong>Contraseña:</strong> $contraseña</p>
                    </div>
                    <p style='font-size: 14px; color: #555;'>
                        Si no solicitaste esto, ignora este correo. Si tienes alguna duda, contáctanos.
                    </p>
                    <footer style='text-align: center; margin-top: 20px; font-size: 12px; color: #888;'>
                        <p>© 2025 SistemaInv. Todos los derechos reservados.</p>
                    </footer>
                </div>";

            // Enviar correo
            $mail->send();
            echo "<script>alert('Correo enviado exitosamente a $correo');
            window.location.href = 'index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Error al enviar el correo: {$mail->ErrorInfo}');
            window.location.href = 'index.php';
            </script>";
        }
    } else {
        echo "<script>alert('No se encontró un usuario con ese correo.');
        window.location.href = 'index.php';
        </script>";
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('No se proporcionó un correo válido.');</script>";
}
?>