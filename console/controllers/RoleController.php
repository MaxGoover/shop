<?php

namespace console\controllers;

use shop\entities\User\User;
use shop\useCases\manage\UserManageService;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * Interactive console roles manager
 */
class RoleController extends Controller
{
    private $_userManageService;

    public function __construct(
        $id,
        $module,
        UserManageService $userManageService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_userManageService = $userManageService;
    }

    /**
     * Adds role to user
     */
    public function actionAssign(): void
    {
        $username = $this->prompt('Username:', ['required' => true]);
        $user = $this->_findModel($username);
        $role = $this->select('Role:', ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'));
        $this->_userManageService->assignRole($user->id, $role);
        $this->stdout('Done!' . PHP_EOL);
    }

    private function _findModel(string $username): User
    {
        if (!$model = User::findOne(['username' => $username])) {
            throw new Exception('User is not found');
        }
        return $model;
    }
}