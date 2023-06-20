<?php
namespace Sy;

use PHPMailer\PHPMailer\PHPMailer;

class Mail {

	/**
	 * @var PHPMailer
	 */
	private $mailer;

	/**
	 * @var string
	 */
	private $to;

	/**
	 * @var string
	 */
	private $from;

	/**
	 * @var string
	 */
	private $replyTo;

	/**
	 * @var string
	 */
	private $cc;

	/**
	 * @var string
	 */
	private $bcc;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @param string $to
	 * @param string $subject
	 * @param mixed $message String or Object with __toString method
	 */
	public function __construct($to = '', $subject = '', $message = '') {
		$this->mailer = new PHPMailer(true);
		$this->mailer->CharSet = 'utf-8';
		$this->mailer->isHTML();

		$this->to      = $to;
		$this->from    = '';
		$this->replyTo = '';
		$this->subject = $subject;
		$this->body    = method_exists($message, '__toString') ? $message->__toString() : $message;
		$this->text    = '';
		$this->cc      = '';
		$this->bcc     = '';
	}

	/**
	 * SMTP configuration
	 *
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $encryption
	 * @param integer $port
	 */
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

	/**
	 * @return string
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @return string
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string $to
	 */
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

	/**
	 * @param string $replyTo
	 */
	public function setReplyTo($replyTo) {
		$this->replyTo = $replyTo;
	}

	/**
	 * @param string $cc
	 */
	public function setCc($cc) {
		$this->cc = $cc;
	}

	/**
	 * @param string $bcc
	 */
	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @param mixed $body String or Object with __toString method
	 */
	public function setBody($body) {
		$this->body = method_exists($body, '__toString') ? $body->__toString() : $body;
	}

	/**
	 * @param mixed $body String or Object with __toString method
	 */
	public function addBody($body) {
		$this->body .= method_exists($body, '__toString') ? $body->__toString() : $body;
	}

	/**
	 * @param string $text
	 */
	public function addText($text) {
		$this->text .= $text;
	}

	/**
	 * @param string $path
	 * @param string $name
	 */
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

			// Auto plain text if not set
			$text = $this->text;
			if (empty($text)) {
				$text = implode("\n", array_map('trim', array_filter(
					explode("\n", strip_tags(html_entity_decode(preg_replace('/<head>.*<\/?head>/ms', '', $this->body), ENT_QUOTES, 'UTF-8'))),
					'trim'
				)));
			}
			$this->mailer->AltBody = $text;

			$this->mailer->send();
		} catch (\PHPMailer\PHPMailer\Exception $e) {
			throw new Mail\Exception($e->getMessage(), 0, $e);
		}
	}

}

namespace Sy\Mail;

class Exception extends \Exception {}