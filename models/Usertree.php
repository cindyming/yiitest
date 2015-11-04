<?php
namespace app\models;

use Yii;

class Usertree extends \kartik\tree\models\Tree
{
    public $lvl = 'referer';
    public $rgt = 'referer';
    public $lft = 'referer';
    public $name = 'id';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * Override isDisabled method if you need as shown in the
     * example below. You can override similarly other methods
     * like isActive, isMovable etc.
     */
    public function isDisabled()
    {
        if (Yii::$app->user->identity->isAdmin()) {
            return true;
        }
        return parent::isDisabled();
    }
}