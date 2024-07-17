<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$env = parse_ini_file('.env');

require 'vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['destinatario'])) {
        $destinatario = $data['destinatario'];
        $nombre_destinatario = 'Cliente'; // Nombre del destinatario hardcodeado
        $asunto = 'Solicitud de cotización';
        $cuerpo = "Hola $nombre_destinatario, ha sido enviado";

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

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'El mensaje ha sido enviado']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El destinatario no está definido']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
