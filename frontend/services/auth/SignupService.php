<?php

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;
use yii\mail\MailerInterface;

class SignupService
{
    private $_mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->_mailer = $mailer;
    }

    public function confirm($token): void
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }

        $user = $this->_getByEmailConfirmToken($token);
        $user->confirmSignup();
        $this->_save($user);
    }

    public function signup(SignupForm $form): User
    {
        if (User::find()->andWhere(['username' => $form->username])) {
            throw new \DomainException('This username has already been taken.');
        }

        if (User::find()->andWhere(['email' => $form->email])) {
            throw new \DomainException('This email address has already been taken.');
        }

        $user = User::requestSignup(
            $form->username,
            $form->email,
            $form->password
        );

        $this->_save($user);

        $sent = $this->_mailer
            ->compose(
                ['html' => 'emailConfirmToken-html', 'text' => 'emailConfirmToken-text'],
                ['user' => $user]
            )
            ->setTo($form->email)
            ->setSubject('Signup confirm for ' . \Yii::$app->name)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Email sending error.');
        }
    }

    private function _getByEmailConfirmToken(string $token): User
    {
        if (!$user = User::findOne(['email_confirm_token' => $token])) {
            throw new \DomainException('User is not found.');
        }
        return $user;
    }

    private function _save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }
}