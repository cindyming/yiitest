<?php

namespace app\models;

use yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class Investment extends ActiveRecord
{
    public $bonus_total;
    public $merit_total;
    public $type;

    public $dynTableName = '{{%investment}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new Investment();
        return $mod->dynTableName;
    }


    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'merited',
                ],
                'value' => function ($event) {
                        return 0;
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'amount',
                ],
                'value' => function ($event) {
                        return $this->amount * 10000;
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'added_by',
                ],
                'value' => function ($event) {
                        return Yii::$app->user->identity->id;
                    },
            ],
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
            [['user_id', 'amount'], 'required'],
            [['merited', 'note', 'added_by'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'user_id' => '会员编号',
            'amount' => '追加投资额',
            'note' => '备注',
            'created_at' => '追加时间',
            'updated_at' => '更新时间',
        ];
    }

}
