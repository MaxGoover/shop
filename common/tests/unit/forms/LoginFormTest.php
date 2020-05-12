<?php

namespace common\tests\unit\forms;

use Yii;
use common\forms\LoginForm;
use common\fixtures\UserFixture;

/**
 * Login form test
 */
class LoginFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;


    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }

    public function testBlank()
    {
        $form = new LoginForm([
            'username' => '',
            'password' => '',
        ]);

        expect_not($form->validate());
    }

    public function testCorrect()
    {
        $form = new LoginForm([
            'username' => 'bayer.hudson',
            'password' => 'password_0',
        ]);

        expect_that($form->validate());
    }
}
