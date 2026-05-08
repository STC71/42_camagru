<?php

/**
 * Envía correos de la aplicación usando SMTP cuando hay credenciales
 * disponibles, o `mail()` como respaldo en desarrollo.
 */
class Mailer {
    
    public static function send($to, $subject, $body, $isHtml = true) {
        $from = Config::get('MAIL_FROM');
        $host = Config::get('MAIL_HOST');
        $port = Config::get('MAIL_PORT');
        $username = Config::get('MAIL_USER');
        $password = Config::get('MAIL_PASS');

        // Cabeceras base del mensaje.
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
        }

        // Si hay credenciales SMTP, usamos ese canal.
        if ($host && $port && $username && $password) {
            return self::sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password);
        }

        // En caso contrario, caemos al envío nativo.
        return mail($to, $subject, $body, $headers);
    }

    private static function sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password) {
        // Implementación SMTP simple, suficiente para desarrollo y Mailtrap.
        $socket = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP connection failed: $errstr ($errno)");
            return false;
        }

        $from = Config::get('MAIL_FROM');

        // Comunicación SMTP básica.
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
        
        // Enviar correo
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

    public static function sendVerificationEmail($email, $username, $token) {
        $verifyUrl = Config::get('APP_URL') . "/verify?token=$token";
        
        $subject = "Verifica tu cuenta de Camagru";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 24px; background-color: #4CAF50; 
                         color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>¡Bienvenido a Camagru, $username!</h2>
                <p>Gracias por registrarte. Por favor, verifica tu correo electronico haciendo clic en el boton inferior:</p>
                <p><a href='$verifyUrl' class='button'>Verificar Correo</a></p>
                <p>O copia este enlace: <br>$verifyUrl</p>
                <p>Si no creaste una cuenta, por favor ignora este correo.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }

    public static function sendPasswordResetEmail($email, $username, $token) {
        // Enlace temporal para restablecer la contraseña.
        $resetUrl = Config::get('APP_URL') . "/reset-password?token=$token";
        
        $subject = "Restablece tu contraseña de Camagru";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 24px; background-color: #2196F3; 
                         color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Solicitud de Restablecimiento de Contraseña</h2>
                <p>Hola $username,</p>
                <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el boton inferior para establecer una nueva contraseña:</p>
                <p><a href='$resetUrl' class='button'>Restablecer Contraseña</a></p>
                <p>O copia este enlace: <br>$resetUrl</p>
                <p>Este enlace expirará en 1 hora.</p>
                <p>Si no solicitaste un restablecimiento de contraseña, por favor ignora este correo.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }

    public static function sendCommentNotification($email, $username, $imageId, $commenter) {
        // Enlace a la foto comentada.
        $imageUrl = Config::get('APP_URL') . "/gallery#image-$imageId";
        
        $subject = "Nuevo comentario en tu foto";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 24px; background-color: #FF5722; 
                         color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Nuevo Comentario en Tu Foto</h2>
                <p>Hola $username,</p>
                <p><strong>$commenter</strong> comento en una de tus fotos.</p>
                <p><a href='$imageUrl' class='button'>Ver Comentario</a></p>
                <p>Puedes cambiar tus preferencias de notificacion en la configuracion de tu cuenta.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }
}
