<?php
require_once "../modelos/ModeloRecuperacion.php";
require '../lib/PHPMailer-master/src/Exception.php';
require '../lib/PHPMailer-master/src/PHPMailer.php';
require '../lib/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['correo'])) {
        echo json_encode(["success" => false, "message" => "Correo no proporcionado."]);
        exit();
    }

    $correo = trim($_POST['correo']);
    $usuario = new Recuperacion();
    $datosUsuario = $usuario->obtenerUsuarioPorCorreo($correo);

    if (!$datosUsuario) {
        echo json_encode(["success" => false, "message" => "El correo no está registrado."]);
        exit();
    }

    $nom = $datosUsuario['Nombre'];
    $apellidoP = $datosUsuario['ApellidoP'];
    $apellidoM = $datosUsuario['ApellidoM'];

 
    $nuevaPass = bin2hex(random_bytes(4)); 
    $hashPass = password_hash($nuevaPass, PASSWORD_DEFAULT);

    // Actualizar la nueva contraseña en la BD
    if (!$usuario->actualizarContraseña($correo, $hashPass)) {
        echo json_encode(["success" => false, "message" => "Error al actualizar la contraseña."]);
        exit();
    }
    $mail = new PHPMailer(true);
try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'mail.yolocaltexmelucan.com';  // Host SMTP de tu proveedor
    $mail->SMTPAuth = true;
    $mail->Username = 'contacto@yolocaltexmelucan.com';  // Tu correo completo
    $mail->Password = 'A3FeiEog@IC*NYBQ';  // La contraseña de tu correo
    $mail->SMTPSecure = 'ssl';  // Usar SSL ya que el puerto es 465
    $mail->Port = 465;  // Puerto para SSL

    // Dirección del remitente
    $mail->setFrom('contacto@yolocaltexmelucan.com', 'Gestión de YoLocal');
    // Dirección del destinatario
    $mail->addAddress($correo, $nom);
    $mail->Subject = 'RECUPERACIÓN DE CONTRASEÑA';

    // Agregar imagen embebida
    $mail->addEmbeddedImage('../assets/img/LogoYolocal.png', 'LogoYolocal');
    
    // Configuración del correo en formato HTML
    $mail->isHTML(true);
    $mail->Body = "
    <html>
    <body style='font-family: Arial, sans-serif; color: black;'>
        <div style='text-align: center; padding: 20px;'>
            <h1 style='color: #5821b8ff;'>Hola, $nom</h1>
            <p>Se ha generado una nueva contraseña temporal para tu cuenta:</p>
            <p style='font-size: 18px; font-weight: bold;'>$nuevaPass</p>
            <p>Por favor, inicia sesión y cambia tu contraseña lo antes posible.</p>
            <img src='cid:LogoYolocal' alt='LogoYoLocal' style='width:100px; height:auto;'>
        </div>
    </body>
    </html>";

    // Intentar enviar el correo
    $mail->send();
    echo json_encode(["success" => true, "message" => "Correo enviado con éxito."]);
} catch (Exception $e) {
    // En caso de error, devolver el mensaje de error
    echo json_encode(["success" => false, "message" => "Error al enviar el correo: {$mail->ErrorInfo}"]);
}
}
?>