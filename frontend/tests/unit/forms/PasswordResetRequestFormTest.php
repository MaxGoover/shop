<?php

namespace frontend\tests\unit\forms;

use Yii;
use frontend\forms\PasswordResetRequestForm;
use common\fixtures\UserFixture as UserFixture;
use common\entities\User;

class PasswordResetRequestFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

    public function testWithWrongEmailAddress()
    {
        $form = new PasswordResetRequestForm();
        $form->email = 'not-existing-email@example.com';
        expect_not($form->validate());
    }

    public function testInactiveUser()
    {
        $user = $this->tester->grabFixture('user', 1);
        $form = new PasswordResetRequestForm();
        $form->email = $user['email'];
        expect_not($form->validate());
    }

    public function testSuccessfully()
    {
        $userFixture = $this->tester->grabFixture('user', 0);

        $form = new PasswordResetRequestForm();
        $form->email = $userFixture['email'];
//        $user = User::findOne(['password_reset_token' => $userFixture['password_reset_token']]);

        expect_not($form->validate());
    }
}
