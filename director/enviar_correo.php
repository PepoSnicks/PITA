<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../php/conexion.php");
include("./php/correoTemplate.php");

require '../php/PHPMailer/Exception.php';
require '../php/PHPMailer/PHPMailer.php';
require '../php/PHPMailer/SMTP.php';

if (isset($_POST['titulo']) && isset($_POST['encabezado']) && isset($_POST['parrafo'])) {
    $titulo = $_POST['titulo'];
    $encabezado = $_POST['encabezado'];
    $parrafo = $_POST['parrafo'];

    // Obtiene los correos electrónicos de todos los alumnos
    $consulta = "SELECT email, nombre FROM alumnos WHERE active = 1";
    $resultado = $conn->query($consulta);

    if ($resultado->num_rows > 0) {
        // Recorre todos los correos electrónicos y envía el correo
        while ($row = $resultado->fetch_assoc()) {
            $email = $row['email'];
            $nombre = $row['nombre'];

            // Configura y envía el correo
            $mail = new PHPMailer(true);

            try {
                // Configura el correo
                $mail->setFrom('proyectouptex@gmail.com', 'Universidad Politécnica de Texcoco');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $titulo;
                $logoUrl = 'https://i.imgur.com/HUZkpai.png'; // Reemplaza con la URL de tu logo
                $logopita = 'https://i.imgur.com/pVYsTa7.png'; // Logo PITA
                $mail->Body = correoTemplate($logoUrl, $logopita, $nombre, $encabezado, $parrafo);

                // Envía el correo
                $mail->send();
            } catch (Exception $e) {
                // Si no se pudo enviar el correo, muestra un mensaje de error
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        }
        // Redirige al usuario a una página de éxito
        echo "success";
    } else {
        // Si no hay alumnos en la base de datos, muestra un mensaje de error
        echo "No hay alumnos en la base de datos";
    }
} else {
    // Si no se recibieron los datos del formulario, redirige al usuario al formulario
    header("location:./correo.php");
}
