<?php

namespace app\services\mail;

use PHPMailer\PHPMailer\PHPMailer;

class MailService
{

	private PHPMailer $_mailer;

	public function __construct() {

		$this->_mailer = new PHPMailer();
		$this->_mailer->isSMTP();
		$this->_mailer->Host = env('MAIL_HOST', 'localhost');
		$this->_mailer->SMTPAuth = true;
		$this->_mailer->Username = env('MAIL_USERNAME', 'username@gmail.com');
		$this->_mailer->Password = env('MAIL_PASSWORD', 'password');
		$this->_mailer->SMTPSecure = 'ssl';
		$this->_mailer->setFrom(env('MAIL_USERNAME', 'username@gmail.com'));
		$this->_mailer->Port = (env('MAIL_PORT', 465));

	}

	public function sendMail(string $to, string $subject, string $message, array $attachments = []): bool {

		$this->_mailer->addAddress($to);
		$this->_mailer->isHTML(true);
		$this->_mailer->Subject = $subject;
		$this->_mailer->Body = $message;

		if (!empty($attachments)) {
			foreach ($attachments as $attachment) {
				$this->_mailer->AddAttachment($attachment['path'], $attachments['name']);
			}
		}

		return $this->_mailer->send();

	}

}