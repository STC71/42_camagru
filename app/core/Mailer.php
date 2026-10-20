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
        $context = stream_context_create(array(
            "ssl" => array(
                "verify_peer" => true,
                "verify_peer_name" => true,
                "peer_name" => $host
            )
        ));

        $socket = @stream_socket_client(
            "tcp://" . $host . ":" . (int) $port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            error_log("SMTP connection failed: $errstr ($errno)");
            return false;
        }

        stream_set_timeout($socket, 30);

        try {
            $readResponse = function () use ($socket) {
                $response = "";

                while (($line = fgets($socket, 515)) !== false) {
                    $response .= $line;

                    if (preg_match('/^[0-9]{3} /', $line)) {
                        return array(
                            "code" => (int) substr($line, 0, 3),
                            "text" => trim($response)
                        );
                    }
                }

                return false;
            };

            $writeAll = function ($data) use ($socket) {
                $length = strlen($data);
                $written = 0;

                while ($written < $length) {
                    $result = fwrite($socket, substr($data, $written));

                    if ($result === false || $result === 0) {
                        return false;
                    }

                    $written += $result;
                }

                return true;
            };

            $command = function ($text, $expected) use (
                $writeAll,
                $readResponse
            ) {
                if (!$writeAll($text . "\r\n")) {
                    error_log("SMTP: failed to write command.");
                    return false;
                }

                $response = $readResponse();

                if ($response === false ||
                    !in_array($response["code"], (array) $expected, true)) {
                    $code = $response === false ? "no response" : $response["text"];
                    error_log("SMTP: unexpected server response: " . $code);
                    return false;
                }

                return $response;
            };

            $greeting = $readResponse();

            if ($greeting === false || $greeting["code"] !== 220) {
                error_log("SMTP: invalid server greeting.");
                return false;
            }

            if ($command("EHLO camagru.local", 250) === false) {
                return false;
            }

            if ($command("STARTTLS", 220) === false) {
                return false;
            }

            $crypto = @stream_socket_enable_crypto(
                $socket,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            );

            if ($crypto !== true) {
                error_log("SMTP: TLS negotiation failed.");
                return false;
            }

            if ($command("EHLO camagru.local", 250) === false) {
                return false;
            }

            if ($command("AUTH LOGIN", 334) === false) {
                return false;
            }

            if ($command(base64_encode($username), 334) === false) {
                return false;
            }

            if ($command(base64_encode($password), 235) === false) {
                return false;
            }

            $from = Config::get("MAIL_FROM");

            if ($command("MAIL FROM: <$from>", 250) === false) {
                return false;
            }

            if ($command("RCPT TO: <$to>", array(250, 251)) === false) {
                return false;
            }

            if ($command("DATA", 354) === false) {
                return false;
            }

            $message = "To: <$to>\r\nSubject: $subject\r\n" . $headers . "\r\n" . $body;
            $message = str_replace(array("\r\n", "\r"), "\n", $message);
            $message = str_replace("\n", "\r\n", $message);
            $message = preg_replace('/(?m)^\./', '..', $message);
            $message = rtrim($message, "\r\n") . "\r\n.\r\n";

            if (!$writeAll($message)) {
                error_log("SMTP: failed to write message completely.");
                return false;
            }

            if ($commandResponse = $readResponse()) {
                if ($commandResponse["code"] !== 250) {
                    error_log("SMTP: message rejected: " . $commandResponse["text"]);
                    return false;
                }
            } else {
                error_log("SMTP: no confirmation after message submission.");
                return false;
            }

            $command("QUIT", 221);
            return true;
        } finally {
            fclose($socket);
        }
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
