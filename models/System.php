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
    public $lowest_cash_amount;
    public $cash_factorage;
    public $stop_banus_times;
    public $open_baodan_tree;
    public $opend_investment_duichong_baodan_fee;
    public $opend_duichong_baodan_fee;
    public $maintenance;
    public $open_suggest_list;
    public $duichong_audit;
    public $open_cash;
    public $open_mall_transfer;
    public $mall_audit;

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
            'open_member_tree' => '会员网络图功能',
            'enable_memmber_login' => '会员登录功能',
            'open_suggest_list' => '会员推荐列表功能',
            'stop_banus_times' => '分红封顶倍数',
            'lowest_cash_amount' => '最低提现额',
            'cash_factorage' => '绩效提现手续费',
            'open_baodan_tree' => '报单员网络图',
            'maintenance' => '系统维护中',
            'duichong_audit' => '对冲转账审核',
            'open_cash' => '允许提现',
            'open_mall_transfer' => '允许商城转账',
            'mall_audit' => '商城转账审核',
            'opend_duichong_baodan_fee' => '新会员使用对冲账号是否有报单费',
            'opend_investment_duichong_baodan_fee' => '追加投资使用对冲账号是否有报单费'
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
