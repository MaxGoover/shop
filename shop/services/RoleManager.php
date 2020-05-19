<?php

namespace shop\services;

use yii\rbac\ManagerInterface;

class RoleManager
{
    private $_manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->_manager = $manager;
    }

    public function assign($userId, $name): void
    {
        if (!$role = $this->_manager->getRole($name)) {
            throw new \DomainException('Role "' . $name . '" does not exist.');
        }
        $this->_manager->revokeAll($userId);
        $this->_manager->assign($role, $userId);
    }
}