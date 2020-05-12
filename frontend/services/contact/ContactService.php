<?php

namespace frontend\services\contact;

use frontend\forms\ContactForm;
use yii\mail\MailerInterface;

class ContactService
{
    private $_adminEmail;
    private $_mailer;

    public function __construct($adminEmail, MailerInterface $mailer)
    {
        $this->_adminEmail = $adminEmail;
        $this->_mailer = $mailer;
    }

    public function send(ContactForm $form): void
    {
        $sent = $this->_mailer
            ->compose()
            ->setTo($this->_adminEmail)
            ->setSubject($form->subject)
            ->setTextBody($form->body)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }
}
