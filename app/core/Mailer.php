<?php

/**
 * =============================================================================
 * Mailer.php - Envío de correos electrónicos
 * =============================================================================
 * Esta clase se encarga de enviar todos los emails de la aplicación:
 * - Correo de verificación de cuenta
 * - Correo de recuperación de contraseña
 * - Notificaciones de nuevos comentarios
 *
 * Analogía cotidiana:
 * Es como el servicio de mensajería de la empresa. Tú le das el sobre
 * (destinatario + asunto + contenido) y él se encarga de enviarlo.
 *
 * Funciona de dos formas:
 * 1. Si hay credenciales SMTP configuradas → usa SMTP (Mailtrap, Gmail, etc.)
 * 2. Si no hay → usa la función mail() de PHP (útil en desarrollo)
 * =============================================================================
 */
class Mailer {

    /**
     * Método principal de envío
     *
     * @param string $to      Dirección del destinatario
     * @param string $subject Asunto del correo
     * @param string $body    Contenido (puede ser HTML)
     * @param bool   $isHtml  true = el cuerpo es HTML
     */
    public static function send($to, $subject, $body, $isHtml = true) {
        $from     = Config::get('MAIL_FROM');
        $host     = Config::get('MAIL_HOST');
        $port     = Config::get('MAIL_PORT');
        $username = Config::get('MAIL_USER');
        $password = Config::get('MAIL_PASS');

        // Cabeceras básicas del correo
        $headers  = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
        }

        // Si tenemos datos SMTP, usamos ese canal (recomendado)
        if ($host && $port && $username && $password) {
            return self::sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password);
        }

        // Si no, usamos la función nativa de PHP
        return mail($to, $subject, $body, $headers);
    }

    /**
     * Implementación sencilla de envío por SMTP
     * (suficiente para Mailtrap y la mayoría de servidores de desarrollo)
     */
    private static function sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password) {
        $socket = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP connection failed: $errstr ($errno)");
            return false;
        }

        $from = Config::get('MAIL_FROM');

        // Conversación básica con el servidor SMTP
        fgets($socket);
        fputs($socket, "EHLO " . Config::get('APP_URL') . "\r\n");
        fgets($socket);

        // Autenticación
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket);
        fputs($socket, base64_encode($username) . "\r\n");
        fgets($socket);
        fputs($socket, base64_encode($password) . "\r\n");
        fgets($socket);

        // Envío del mensaje
        fputs($socket, "MAIL FROM: <$from>\r\n");
        fgets($socket);
        fputs($socket, "RCPT TO: <$to>\r\n");
        fgets($socket);
        fputs($socket, "DATA\r\n");
        fgets($socket);

        fputs($socket, "Subject: $subject\r\n");
        fputs($socket, $headers);
        fputs($socket, "\r\n$body\r\n.\r\n");
        fgets($socket);

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    }

    /**
     * Envía el correo de verificación de cuenta
     */
    public static function sendVerificationEmail($email, $username, $token) {
        $verifyUrl = Config::get('APP_URL') . "/verify?token=$token";

        $subject = "Verifica tu cuenta de Camagru";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button {
                    display: inline-block;
                    padding: 12px 24px;
                    background-color: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>¡Bienvenido a Camagru, $username!</h2>
                <p>Gracias por registrarte. Por favor, verifica tu correo electrónico haciendo clic en el botón inferior:</p>
                <p><a href='$verifyUrl' class='button'>Verificar Correo</a></p>
                <p>O copia este enlace:<br>$verifyUrl</p>
                <p>Si no creaste una cuenta, puedes ignorar este correo.</p>
            </div>
        </body>
        </html>";

        return self::send($email, $subject, $body);
    }

    /**
     * Envía el correo de recuperación de contraseña
     */
    public static function sendPasswordResetEmail($email, $username, $token) {
        $resetUrl = Config::get('APP_URL') . "/reset-password?token=$token";

        $subject = "Restablece tu contraseña de Camagru";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button {
                    display: inline-block;
                    padding: 12px 24px;
                    background-color: #2196F3;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Solicitud de Restablecimiento de Contraseña</h2>
                <p>Hola $username,</p>
                <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón inferior para establecer una nueva:</p>
                <p><a href='$resetUrl' class='button'>Restablecer Contraseña</a></p>
                <p>O copia este enlace:<br>$resetUrl</p>
                <p>Este enlace expirará en 1 hora.</p>
                <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
            </div>
        </body>
        </html>";

        return self::send($email, $subject, $body);
    }

    /**
     * Envía una notificación cuando alguien comenta una foto
     */
    public static function sendCommentNotification($email, $username, $imageId, $commenter) {
        $imageUrl = Config::get('APP_URL') . "/gallery#image-$imageId";

        $subject = "Nuevo comentario en tu foto";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button {
                    display: inline-block;
                    padding: 12px 24px;
                    background-color: #FF5722;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Nuevo Comentario en Tu Foto</h2>
                <p>Hola $username,</p>
                <p><strong>$commenter</strong> ha comentado en una de tus fotos.</p>
                <p><a href='$imageUrl' class='button'>Ver Comentario</a></p>
                <p>Puedes cambiar tus preferencias de notificación en la configuración de tu cuenta.</p>
            </div>
        </body>
        </html>";

        return self::send($email, $subject, $body);
    }
}
