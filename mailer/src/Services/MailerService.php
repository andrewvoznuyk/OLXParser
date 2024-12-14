<?php

namespace Root\App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{

    /**
     * @var PHPMailer
     */
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    /**
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function sendEmail(string $subject, string $body): bool
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = '68c341c89fef68';
            $this->mailer->Password = '47f75779339bf8';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 2525;

            $this->mailer->setFrom('mailer@gmail.com', 'Mailer');
            $this->mailer->addAddress('lymphbizkit@gmail.com', 'Recipient');

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();

            echo 'Message has been sent to lymphbizkit@gmail.com';
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            return false;
        }
    }
}
