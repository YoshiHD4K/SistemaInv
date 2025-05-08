<?php
// Phpmailer y enviar mensaje de whatsapp V 0.0.5
require __DIR__ . '/vendor/autoload.php';  // Cargar PHPMailer con Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Función para enviar correo con imagen y mensaje opcional
function sendEmail($destinatario, $asunto, $rutaImagen, $mensajeOpcional) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; //servidor SMTP       
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mbart1852@gmail.com'; // Tu email
         $mail->Password   = 'sweopfuerijnqxyi'; // Contraseña de app gmail SMTP              
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;;
        $mail->Port       = 465;

        $mail->setFrom('mbart1852@gmail.com', 'Daniel Diaz');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $asunto;