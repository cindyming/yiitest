<?php

namespace app\models;

use yii\db\ActiveRecord;

class Log  extends ActiveRecord
{

    public $dynTableName = '{{%log}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new Log();
        return $mod->dynTableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'action'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role'   => '角色',
            'action' => '动作',
            'created_at' => '时间',
        ];
    }

    static function add($role, $action)
    {
        $log = new Log();
        $log->role =  $role;
        $log->action = $action;
        $log->save();
    }
}
