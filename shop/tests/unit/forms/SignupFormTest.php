<?php
namespace shop\tests\unit\forms;

use common\fixtures\UserFixture;
use shop\forms\SignupForm;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    public function _before() // заполняем данными из фикстур tests/_data/user
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

    public function testCorrectSignup()
    {
        $form = new SignupForm([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
        ]);

        expect_that($form->validate());
    }

    public function testNotCorrectSignup()
    {
        $form = new SignupForm([
            'username' => 'troy.becker',
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);

        expect_not($form->validate());
        expect_that($form->getErrors('username'));
        expect_that($form->getErrors('email'));

        expect($form->getFirstError('username'))
            ->equals('This username has already been taken.');
        expect($form->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }
}
