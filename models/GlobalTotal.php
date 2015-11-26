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
            [['total_in', 'total_out', 'mall', 'bonus', 'merit', 'baodan'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'total_in'   => '公司总账收入',
            'total_out' => '公司总账支出',
            'mall'   => '商城币总额',
            'bonus' => '分红总额',
            'merit'   => '绩效总额',
            'baodan' => '服务费总额',
            'created_at' => '计算时间'
        ];
    }
}
