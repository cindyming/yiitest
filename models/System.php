<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

class System extends ActiveRecord
{

    public $dynTableName = '{{%system}}';
    public $open_member_tree;
    public $enable_memmber_login;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new System();
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

    public function attributeLabels()
    {
        return [
            'open_member_tree' => '会员推荐图功能',
            'enable_memmber_login' => '会员登录功能'
            ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
        ];
    }

    static function loadConfig($name = '')
    {

        $configs = unserialize(Yii::$app->cache->get('SYSTEM_CONFIG'));
        if (!count($configs) || true) {
            $configs = array();
            $values = System::find()->all();
            foreach ($values as $val) {
                $configs[$val['name']] = $val['value'];
            }
            Yii::$app->cache->set('SYSTEM_CONFIG', serialize($configs));
        }
        return ($name) ? (isset($configs[$name]) ? $configs[$name] : '') : $configs;
    }
}
