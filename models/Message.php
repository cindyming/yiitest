<?php

namespace app\models;

use yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class Message extends ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
                'value' => function ($event) {
                        return Yii::$app->user->id;
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
                'value' => function ($event) {
                        return Yii::$app->user->id;
                    },
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'title', 'content'], 'required'],
            [['replied_content'], 'trim'],
            [['replied_content'], 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'type' => '留言类型',
            'title' => '留言标题',
            'user_id' => '留言人',
            'replied_content' => '回复内容',
            'content' => '留言内容',
            'created_at' => '留言日期',
            'updated_at' => '回复日期',
        ];
    }

    public function getTypeoptions()
    {
        return [
            1 => '付款问题',
            2 => '代理问题',
            3 => '账户问题',
            4 => '技术问题',
            5 => '建议问题',
            6 => '体现问题',
            7 => '其他问题',
        ];
    }

    public function isReplied()
    {
        return ($this->replied_content) ? '已回复' : '未回复';
    }
}
