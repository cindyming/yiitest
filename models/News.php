<?php

namespace app\models;

use yii\db\ActiveRecord;

class News extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'be_top', 'content'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => '公告标题',
            'be_top' => '是否置顶',
            'content' => '信息内容',
            'public_at' => '发布日期',
        ];
    }

    public function getBetopOptions()
    {
            return [
                0 => '正常',
                1 => '置顶',
            ];
    }
}
