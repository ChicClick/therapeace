<?php

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private $mailer;

    // Default configuration
    private $host = 'smtp.gmail.com';
    private $username = 'therapeacemanagement@gmail.com';
    private $password = 'ovzp bnem esqd nqyn';
    private $fromEmail = 'therapeacemanagement@gmail.com';
    private $fromName = 'Therapeace Team';

    public function __construct()
    {

        $this->mailer = new PHPMailer(true);

        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->username;
            $this->mailer->Password = $this->password;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = 465;

            // Default sender
            $this->mailer->setFrom($this->fromEmail, $this->fromName);
        } catch (Exception $e) {
            throw new Exception("Mailer initialization failed: " . $e->getMessage());
        }
    }

    public function sendEmail($toEmail, $subject, $body, $attachments = [])
    {
        try {
            // Recipient
            $this->mailer->addAddress($toEmail);

            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);

            foreach ($attachments as $filename => $fileContent) {
                $this->mailer->addStringAttachment($fileContent, $filename);
            }

            // Send email
            $this->mailer->send();

            return "Message sent!";
        } catch (Exception $e) {
            return "Mailer Error: " . $this->mailer->ErrorInfo;
        }
    }
}
