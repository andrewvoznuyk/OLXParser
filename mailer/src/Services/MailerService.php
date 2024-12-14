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
        $decodedBody = json_decode($body, true);
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = getenv('MAILER_USER_HOST');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = getenv('MAILER_USERNAME');
            $this->mailer->Password = getenv('MAILER_USER_PASSWORD');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = getenv('MAILER_PORT');
            $this->mailer->setFrom(getenv('MAILER_EMAIL_ADDRESS'), getenv('MAILER_EMAIL_NAME'));
            $this->mailer->addAddress($decodedBody['email'], 'Recipient');
            $this->mailer->isHTML();
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->send();
            echo 'Message has been sent to: ' . $decodedBody['email'];
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            return false;
        }
    }

}