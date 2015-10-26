<?php

namespace app\models;

use yii\db\ActiveRecord;

class Message extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'title', 'content'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => '留言类型',
            'title' => '留言标题',
            'user_id' => '留言人',
            'replied' => '是否回复',
            'content' => '留言内容',
            'created_at' => '留言日期',
            'updated_at' => '回复日期',
        ];
    }
}
