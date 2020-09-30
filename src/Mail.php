<?php
namespace Sy;

use PHPMailer\PHPMailer\PHPMailer;

class Mail {

	/**
	 * @var PHPMailer
	 */
	private $mailer;

	private $to;
	private $from;
	private $replyTo;
	private $cc;
	private $bcc;
	private $subject;
	private $body;
	private $text;

	public function __construct($to = '', $subject = '', $message = '') {
		$this->mailer = new PHPMailer(true);
		$this->mailer->CharSet = 'utf-8';
		$this->mailer->isHTML();

		$this->to      = $to;
		$this->from    = '';
		$this->replyTo = '';
		$this->subject = $subject;
		$this->body    = $message;
		$this->text    = '';
		$this->cc      = '';
		$this->bcc     = '';
	}

	public function setSmtp($host, $username, $password, $encryption = 'ssl', $port = 465) {
		$this->mailer->isSMTP();
		$this->mailer->Host       = $host;
		$this->mailer->SMTPAuth   = true;
		$this->mailer->Username   = $username;
		$this->mailer->Password   = $password;
		$this->mailer->SMTPSecure = $encryption;
		$this->mailer->Port       = $port;
		if (empty($this->from)) {
			$this->setFrom($username);
		}
	}

	public function getTo() {
		return $this->to;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getBody() {
		return $this->body;
	}

	public function setTo($to) {
		$this->to = $to;
	}

	/**
	 * @param string $from foo@bar.com or Foo <foo@bar.com> 
	 * @param string $name
	 */
	public function setFrom($from, $name = '') {
		$this->from = empty($name) ? $from : "$name <$from>";
	}

	public function setReplyTo($replyTo) {
		$this->replyTo = $replyTo;
	}

	public function setCc($cc) {
		$this->cc = $cc;
	}

	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setBody($body) {
		$this->body = $body;
	}

	public function addBody($body) {
		$this->body .= $body;
	}

	public function addText($text) {
		$this->text .= $text;
	}

	public function addAttachment($path, $name = '') {
		$this->mailer->addAttachment($path, $name);
	}

	public function send() {
		try {
			// From
			$from = current(PHPMailer::parseAddresses($this->from));
			if (!empty($from)) {
				$this->mailer->setFrom($from['address'], $from['name']);
			}

			$addPersons = function ($persons, $method) {
				foreach ($persons as $person) {
					$this->mailer->$method($person['address'], $person['name']);
				}
			};

			// To
			$addPersons(PHPMailer::parseAddresses($this->to), 'addAddress');

			// Reply to
			$addPersons(PHPMailer::parseAddresses($this->replyTo), 'addReplyTo');

			// CC
			$addPersons(PHPMailer::parseAddresses($this->cc), 'addCC');

			// BCC
			$addPersons(PHPMailer::parseAddresses($this->bcc), 'addBCC');

			$this->mailer->Subject = $this->subject;
			$this->mailer->Body    = $this->body;
			$this->mailer->AltBody = empty($this->text) ? strip_tags(html_entity_decode($this->body, ENT_QUOTES, 'UTF-8')) : $this->text;

			$this->mailer->send();
		} catch(\PHPMailer\PHPMailer\Exception $e) {
			throw new Mail\Exception($e->getMessage());
		}
	}

}

namespace Sy\Mail;

class Exception extends \Exception {}