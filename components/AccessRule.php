<?php
namespace app\components;
use app\models\User;
class AccessRule extends \yii\filters\AccessRule {
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (count($this->roles) === 0) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif (!$user->getIsGuest() && $role === $user->identity->role_id) {
                return true;
            }
        }
        return false;
    }
}
