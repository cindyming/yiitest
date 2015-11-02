<?php

namespace app\models;

use yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class Revenue extends ActiveRecord
{
    public $bonus_total;
    public $merit_total;

    public $dynTableName = '{{%revenue}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new Revenue();
        return $mod->dynTableName;
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['bonus', 'merit', 'note'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'user_id' => '编号',
            'bonus' => '分红',
            'merit' => '绩效',
            'approved' => '状态',
            'bonus_total' => '分红',
            'merit_total' => '绩效',
            'note' => '备注',
            'created_at' => '结算时间',
            'updated_at' => '发放时间',
        ];
    }

    public function getStatus()
    {
        return [
            0 => '未发放',
            1 => '已发放',
        ];
    }
}
