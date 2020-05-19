<?php

namespace shop\useCases\auth;

use shop\forms\auth\PasswordResetRequestForm;
use shop\forms\auth\ResetPasswordForm;
use shop\repositories\UserRepository;
use Yii;
use yii\mail\MailerInterface;

class PasswordResetService
{
    private $_mailer;
    private $_users;

    public function __construct(MailerInterface $mailer, UserRepository $users)
    {
        $this->_mailer = $mailer;
        $this->_users = $users;
    }

    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = $this->_users->getByPasswordResetToken($token);
        $user->resetPassword($form->password);
        $this->_users->save($user);
    }

    public function request(PasswordResetRequestForm $form): void
    {
        $user = $this->_users->getByEmail($form->email);

        if (!$user->isActive()) {
            throw new \DomainException('User is not active.');
        }

        $user->requestPasswordReset();
        $this->_users->save($user);

        $sent = $this->_mailer
            ->compose(
                ['html' => 'auth/reset/confirm-html', 'text' => 'auth/reset/confirm-text'],
                ['user' => $user]
            )
            ->setTo($user->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }

    public function validateToken(string $token): void
    {
        if (empty($token) || !\is_string($token)) {
            throw new \DomainException('Password reset token cannot be blank.');
        }
        if (!$this->_users->existsByPasswordResetToken($token)) {
            throw new \DomainException('Wrong password reset token.');
        }
    }
}