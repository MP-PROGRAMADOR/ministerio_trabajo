<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ajusta las rutas según dónde hayas guardado la carpeta de PHPMailer
require '../libs/PHPMailer/src/Exception.php';
require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';

function enviarCorreoVerificacion($correo_destino, $nombre_usuario, $enlace_verificacion) {
    $mail = new PHPMailer(true);

    $mail = new PHPMailer(true);
$mail->SMTPDebug = 2; // <-- AÑADE ESTA LÍNEA TEMPORALMENTE

    try {
        // Configuración del Servidor SMTP (Ejemplo con Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                                 // Activar autenticación SMTP
        $mail->Username   = 'mpprogramacion22@gmail.com';  // Tu correo emisor
        $mail->Password   = 'lprlsrqadlrrcnai';        // Tu contraseña de aplicación (Generada en Google)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Encriptación TLS segura
        $mail->Port       = 587;                                  // Puerto para TLS
        $mail->CharSet    = 'UTF-8';

        // Destinatarios
        $mail->setFrom('mpprogramacion22@gmail.com', 'Sistema Nacional de Empleo');
        $mail->addAddress($correo_destino, $nombre_usuario);

        // Contenido del Correo (Diseño HTML Institucional)
        $mail->isHTML(true);
        $mail->Subject = 'Verificación de Cuenta — Portal de Empleo';
        
        $mail->Body    = "
        <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #1a426a; padding: 20px; text-align: center; color: white;'>
                <h2 style='margin: 0;'>Sistema Nacional de Empleo</h2>
                <p style='margin: 5px 0 0 0; font-size: 12px; opacity: 0.8;'>Sección de Registro Estatal</p>
            </div>
            <div style='padding: 30px; background-color: #ffffff; color: #333333;'>
                <p>Estimado/a <strong>" . htmlspecialchars($nombre_usuario) . "</strong>,</p>
                <p>Gracias por solicitar su alta en el Portal del Trabajador. Para completar el proceso de registro y activar sus credenciales de acceso de forma segura, es necesario que confirme su dirección de correo electrónico.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . $enlace_verificacion . "' style='background-color: #17a2b8; color: white; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 5px; display: inline-block;'>Verificar Dirección de Correo</a>
                </div>
                
                <p style='font-size: 12px; color: #666666;'>Si el botón no funciona, copie y pegue el siguiente enlace en su navegador:</p>
                <p style='font-size: 11px; word-break: break-all; color: #007bff;'>" . $enlace_verificacion . "</p>
                
                <hr style='border: 0; border-top: 1px solid #eeeeee; margin: 20px 0;'>
                <p style='font-size: 11px; color: #999999; text-align: center;'>Este es un mensaje automático, por favor no responda a este correo. &copy; 2026 Ministerio de Trabajo.</p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        return false;
    }
}