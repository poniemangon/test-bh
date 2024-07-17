<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$env = parse_ini_file('.env');





require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destinatario = $_POST['destinatario'];
    $nombre_destinatario = $_POST['nombre_destinatario'];
    $asunto = 'Solicitud de cotización';
    $cuerpo = "Hola $nombre_destinatario, te enviamos el PDF con tu cotización";

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // Servidor SMTP de Outlook
        $mail->SMTPAuth = true;
        $mail->Username = $env['MAIL_USERNAME']; // Tu dirección de correo de Outlook
        $mail->Password = $env['MAIL_PASSWORD']; // Contra
        $mail->SMTPSecure = 'tls'; // Encriptación TLS
        $mail->Port = 587; // Puerto TCP para TLS

        // Destinatarios
        $mail->setFrom('test-bh@outlook.com', 'Peruano');
        $mail->addAddress($destinatario, $nombre_destinatario); // Añade el destinatario desde el formulario

        // Cabeceras adicionales para evitar spam
        $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->addCustomHeader('X-Originating-IP', $_SERVER['REMOTE_ADDR']);
        $mail->addCustomHeader('List-Unsubscribe', '<mailto:unsubscribe@tudominio.com>');

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo;
        $mail->AltBody = strip_tags($cuerpo); // Elimina las etiquetas HTML para el cuerpo alternativo en texto plano

        // Adjuntar archivo PDF
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['archivo']['tmp_name'], $_FILES['archivo']['name']);
        }

        $mail->send();
        echo 'El mensaje ha sido enviado';
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    // Mostrar el formulario HTML si no se ha enviado el formulario
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de Envío de Correo</title>
    </head>
    <body>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="destinatario">Correo del destinatario:</label>
            <input type="email" id="destinatario" name="destinatario" required>
            <br><br>

            <label for="nombre_destinatario">Nombre del destinatario:</label>
            <input type="text" id="nombre_destinatario" name="nombre_destinatario" required>
            <br><br>

            <label for="archivo">Seleccionar PDF:</label>
            <input type="file" id="archivo" name="archivo" accept="application/pdf" required>
            <br><br>

            <input type="submit" value="Enviar">
        </form>
    </body>
    </html>';
}
?>

