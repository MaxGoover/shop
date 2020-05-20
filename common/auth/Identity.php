<?php

namespace common\auth;

use filsh\yii2\oauth2server\Module;
use OAuth2\Storage\UserCredentialsInterface;
use shop\entities\User\User;
use shop\readModels\UserReadRepository;
use Yii;
use yii\web\IdentityInterface;

class Identity implements IdentityInterface, UserCredentialsInterface
{
    private $_user;

    public function __construct(User $user)
    {
        $this->_user = $user;
    }

    public static function findIdentity($id)
    {
        $user = self::_getRepository()->findActiveById($id);
        return $user ? new self($user) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $data = self::_getOauth()->getServer()->getResourceController()->getToken();
        return !empty($data['user_id']) ? static::findIdentity($data['user_id']) : null;
    }

    public function checkUserCredentials($username, $password): bool
    {
        if (!$user = self::_getRepository()->findActiveByUsername($username)) {
            return false;
        }
        return $user->validatePassword($password);
    }

    public function getAuthKey(): string
    {
        return $this->_user->auth_key;
    }

    public function getId(): int
    {
        return $this->_user->id;
    }

    public function getUserDetails($username): array
    {
        $user = self::_getRepository()->findActiveByUsername($username);
        return ['user_id' => $user->id];
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    private static function _getOauth(): Module
    {
        return Yii::$app->getModule('oauth2');
    }

    private static function _getRepository(): UserReadRepository
    {
        return \Yii::$container->get(UserReadRepository::class);
    }
}