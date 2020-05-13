<?php

namespace shop\services\auth;

use shop\entities\User\User;
use shop\forms\auth\SignupForm;
use shop\repositories\UserRepository;
use yii\mail\MailerInterface;

class SignupService
{
    private $_mailer;
    private $_users;

    public function __construct(MailerInterface $mailer, UserRepository $users)
    {
        $this->_mailer = $mailer;
        $this->_users = $users;
    }

    public function confirm($token): void
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }

        $user = $this->_users->getByEmailConfirmToken($token);
        $user->confirmSignup();
        $this->_users->save($user);    }

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

        $this->_users->save($user);

        $sent = $this->_mailer
            ->compose(
                ['html' => 'auth/signup/confirm-html', 'text' => 'auth/signup/confirm-text'],
                ['user' => $user]
            )
            ->setTo($form->email)
            ->setSubject('Signup confirm for ' . \Yii::$app->name)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Email sending error.');
        }
    }
}