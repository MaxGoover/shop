<?php
namespace frontend\tests\unit\forms;

use frontend\forms\ContactForm;
use yii\mail\MessageInterface;

class ContactFormTest extends \Codeception\Test\Unit
{
    public function testSuccess()
    {
        $form = new ContactForm();

        $form->attributes = [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
        ];

        expect_that($form->validate());
    }
}
