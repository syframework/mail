# sy/mail

Simple mail library

## Installation

Install the latest version with

```bash
$ composer require sy/mail
```

## Basic Usage

Use php mail() function by default

```php
<?php

use Sy\Mail;

$mail = new Mail('syone7@gmail.com', 'Hello world', 'This is a test mail!');
$mail->send();
```

Use a SMTP server

```php
<?php

use Sy\Mail;

$mail = new Mail('syone7@gmail.com', 'Hello world', 'This is a test mail!');
$mail->setSmtp('smtp.gmail.com', 'example@gmail.com', 'password', 'tls', 587);
$mail->send();
```

## Responsive Simple HTML Email Template

```php
<?php

use Sy\Mail;
use Sy\Mail\Template\Html;

$html = new Html('This is a simple html email template!');

$mail = new Mail('syone7@gmail.com', 'Simple html template', $html);
$mail->send();
```

```php
<?php

use Sy\Mail;
use Sy\Mail\Template\Html;

$html = new Html();
$html->addImage('https://picsum.photos/600/300', 'Image Alt');
$html->addParagraph('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
$html->addButton('Go to website now!', 'https://example.com');
$html->setFooterLink('Unsubscribe', 'https://example.com', "Don't like these emails?");
$html->setPoweredBy('HTML email', 'https://example.com', 'Powered by');

$mail = new Mail('syone7@gmail.com', 'Simple html template', $html);
$mail->send();
```