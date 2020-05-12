<?php

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\PasswordResetRequestForm;
use frontend\forms\ResetPasswordForm;
use Yii;
use yii\mail\MailerInterface;

class PasswordResetService
{
    private $_mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->_mailer = $mailer;
    }

    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = $this->_getByPasswordResetToken($token);
        $user->resetPassword($form->password);
        $this->_save($user);
    }

    public function request(PasswordResetRequestForm $form): void
    {
        $user = $this->_getByEmail($form->email);

        if (!$user->isActive()) {
            throw new \DomainException('User is not active.');
        }

        $user->requestPasswordReset();
        $this->_save($user);

        $sent = $this->_mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
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
        if (empty($token) || !is_string($token)) {
            throw new \DomainException('Password reset token cannot be blank.');
        }
        if (!$this->_existsByPasswordResetToken($token)) {
            throw new \DomainException('Wrong password reset token.');
        }
    }

    private function _existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    private function _getByEmail(string $email): User
    {
        if (!$user = User::findOne(['email' => $email])) {
            throw new \DomainException('User is not found.');
        }
        return $user;
    }

    private function _getByPasswordResetToken(string $token): User
    {
        if (!$user = User::findByPasswordResetToken($token)) {
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