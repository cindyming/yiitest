<?php

namespace app\models;

use yii\db\ActiveRecord;

class Backup  extends ActiveRecord
{

    public $dynTableName = '{{%backup}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new Backup();
        return $mod->dynTableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'created_at'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filename'   => '文件名',
            'created_at' => '备份时间'
        ];
    }

}
