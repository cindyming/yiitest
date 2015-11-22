<?php
namespace app\components;
use app\models\User;
use Yii;
use app\models\System;
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
                if (($user->identity->role_id != 1) && (!System::loadConfig('enable_memmber_login'))) {
                    Yii::$app->user->logout();
                    Yii::$app->getResponse()->redirect('/site/login');
                }
                return true;
            }
        }
        return false;
    }
}
