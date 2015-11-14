<?php

namespace app\models;

use yii\db\ActiveRecord;

class GlobalTotal  extends ActiveRecord
{

    public $dynTableName = '{{%global_total}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%global_total}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_in', 'total_out'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'total_in'   => '总收入',
            'total_out' => '总支出',
            'created_at' => '计算时间'
        ];
    }
}
