<?php
namespace Sy\Mail\Template;

use Sy\Component\WebComponent;

class Html extends WebComponent {

	private $body;

	/**
	 * @param mixed $message
	 */
	public function __construct($message = '') {
		parent::__construct();
		$this->setTemplateFile(__DIR__ . '/Html.html');
		$this->body = method_exists($message, '__toString') ? $message->__toString() : "<p>$message</p>";
	}

	public function __toString() {
		$this->setVar('BODY', $this->body);
		return parent::__toString();
	}

	/**
	 * @param string $text
	 */
	public function addParagraph($text) {
		$p = new WebComponent();
		$p->setTemplateFile(__DIR__ . '/Paragraph.html');
		$p->setVar('TEXT', $text);
		$this->body .= $p->__toString();
	}

	/**
	 * @param string $url
	 * @param string $alt
	 * @param mixed $width
	 * @param mixed $height
	 */
	public function addImage($url, $alt, $width = '100%', $height = '') {
		$img = new WebComponent();
		$img->setTemplateFile(__DIR__ . '/Image.html');
		$img->setVars([
			'URL'    => $url,
			'ALT'    => $alt,
			'WIDTH'  => $width,
			'HEIGHT' => $height,
		]);
		$this->body .= $img->__toString();
	}

	/**
	 * @param string $label
	 * @param string $url
	 */
	public function addButton($label, $url) {
		$button = new WebComponent();
		$button->setTemplateFile(__DIR__ . '/Button.html');
		$button->setVars([
			'LABEL' => $label,
			'URL'   => $url,
		]);
		$this->body .= $button->__toString();
	}

	/**
	 * @param string $text
	 */
	public function setPreheader($text) {
		$this->setVar('PREHEADER', $text);
	}

	/**
	 * @param string $label
	 * @param string $url
	 * @param string $message
	 */
	public function setFooterLink($label, $url, $message = '') {
		$this->setVars([
			'FOOTER_LINK_LABEL'   => $label,
			'FOOTER_LINK_URL'     => $url,
			'FOOTER_LINK_MESSAGE' => $message,
		]);
		$this->setBlock('FOOTER_LINK_BLOCK');
	}

	/**
	 * @param string $label
	 * @param string $url
	 * @param string $message
	 */
	public function setPoweredBy($label, $url, $message = '') {
		$this->setVars([
			'POWERED_LABEL'   => $label,
			'POWERED_URL'     => $url,
			'POWERED_MESSAGE' => $message,
		]);
		$this->setBlock('POWERED_BLOCK');
	}

}