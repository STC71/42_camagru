<?php

/**
 * Mailer Class
 * Handles email sending functionality
 */
class Mailer {
    
    public static function send($to, $subject, $body, $isHtml = true) {
        $from = Config::get('MAIL_FROM');
        $host = Config::get('MAIL_HOST');
        $port = Config::get('MAIL_PORT');
        $username = Config::get('MAIL_USER');
        $password = Config::get('MAIL_PASS');

        // Headers
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
        }

        // For development with SMTP (like Mailtrap)
        if ($host && $port && $username && $password) {
            return self::sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password);
        }

        // Fallback to PHP mail()
        return mail($to, $subject, $body, $headers);
    }

    private static function sendSMTP($to, $subject, $body, $headers, $host, $port, $username, $password) {
        // Simple SMTP implementation
        $socket = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP connection failed: $errstr ($errno)");
            return false;
        }

        $from = Config::get('MAIL_FROM');
        
        // SMTP communication
        fgets($socket);
        fputs($socket, "EHLO " . Config::get('APP_URL') . "\r\n");
        fgets($socket);
        
        // Authentication
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket);
        fputs($socket, base64_encode($username) . "\r\n");
        fgets($socket);
        fputs($socket, base64_encode($password) . "\r\n");
        fgets($socket);
        
        // Send email
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
        
        $subject = "Verify your Camagru account";
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
                <h2>Welcome to Camagru, $username!</h2>
                <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
                <p><a href='$verifyUrl' class='button'>Verify Email</a></p>
                <p>Or copy this link: <br>$verifyUrl</p>
                <p>If you didn't create an account, please ignore this email.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }

    public static function sendPasswordResetEmail($email, $username, $token) {
        $resetUrl = Config::get('APP_URL') . "/reset-password?token=$token";
        
        $subject = "Reset your Camagru password";
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
                <h2>Password Reset Request</h2>
                <p>Hi $username,</p>
                <p>We received a request to reset your password. Click the button below to set a new password:</p>
                <p><a href='$resetUrl' class='button'>Reset Password</a></p>
                <p>Or copy this link: <br>$resetUrl</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request a password reset, please ignore this email.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }

    public static function sendCommentNotification($email, $username, $imageId, $commenter) {
        $imageUrl = Config::get('APP_URL') . "/gallery#image-$imageId";
        
        $subject = "New comment on your photo";
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
                <h2>New Comment on Your Photo</h2>
                <p>Hi $username,</p>
                <p><strong>$commenter</strong> commented on one of your photos.</p>
                <p><a href='$imageUrl' class='button'>View Comment</a></p>
                <p>You can change your notification preferences in your account settings.</p>
            </div>
        </body>
        </html>";
        
        return self::send($email, $subject, $body);
    }
}
