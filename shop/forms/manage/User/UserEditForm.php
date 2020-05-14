<?php

namespace shop\forms\manage\User;

use shop\entities\User\User;
use yii\base\Model;

class UserEditForm extends Model
{
    private $_username;
    private $_email;
    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->_username = $user->username;
        $this->_email = $user->email;
        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['username', 'email'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [['username', 'email'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id]],
        ];
    }
}