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
            [['role', 'action', 'result'], 'required'],
            [['note'], 'trim'],
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
            'result' => '结果',
            'note' => '摘要',
            'created_at' => '时间',
        ];
    }

    static function add($role, $action, $result= false, $note = '')
    {
        $log = new Log();
        $log->role =  $role;
        $log->action = $action;
        $log->result = ($result) ? $result : '成功';
        $log->note = ($note) ? $note : '';
        $log->save();
    }
}
