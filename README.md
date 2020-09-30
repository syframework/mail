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