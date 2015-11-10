<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

class News extends ActiveRecord
{

    public $dynTableName = '{{%news}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new News();
        return $mod->dynTableName;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
            [['title', 'be_top', 'content'], 'required'],
            [['public_at'], 'trim']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => '编号',
            'title' => '公告标题',
            'be_top' => '是否置顶',
            'content' => '信息内容',
            'public_at' => '发布日期',
        ];
    }

    public function getBetopOptions()
    {
            return [
                '' => '不限',
                0 => '正常',
                1 => '置顶',
            ];
    }
}
