<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function notifyAdmin(string $fullName, string $email, string $company): bool|string
    {
        $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_TYPED) ?: [];
        $adminEmail = $env['ADMIN_EMAIL'];

        require_once __DIR__ . '/vendor/autoload.php';

        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $env['SMTP_HOST'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $env['SMTP_USERNAME'];
            $mailer->Password = $env['SMTP_PASSWORD'];
            $mailer->SMTPSecure = $env['SMTP_ENCRYPTION'];
            $mailer->Port = $env['SMTP_PORT'] ?? 587;
            $mailer->setFrom($env['SMTP_FROM_EMAIL'], $env['SMTP_FROM_NAME']);
            $mailer->addAddress($adminEmail);
            $mailer->isHTML(true);
            $mailer->Subject = 'New Registration';
            $mailer->Body = sprintf(
                '<p><b>Ad:</b> %s</p><p><b>Email:</b> %s</p><p><b>Company:</b> %s</p>',
                htmlspecialchars($fullName, ENT_QUOTES),
                htmlspecialchars($email, ENT_QUOTES),
                htmlspecialchars($company, ENT_QUOTES)
            );
            return $mailer->send();
        } catch (Exception $e) {
            return 'MAIL_ERROR: ' . $mailer->ErrorInfo ?: $e->getMessage();
        }
    }
}