<?php

namespace frontend\services\contact;

use frontend\forms\ContactForm;

class ContactService
{
    private $_adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->_adminEmail = $adminEmail;
    }

    public function send(ContactForm $form): void
    {
        $sent = \Yii::$app->mailer->compose()
            ->setTo($this->_adminEmail)
            ->setSubject($form->subject)
            ->setTextBody($form->body)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }
}
