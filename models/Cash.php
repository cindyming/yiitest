<?php

namespace app\models;

use yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class Cash extends ActiveRecord
{
    public $password2;

    public $dynTableName = '{{%cach}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new Cash();
        return $mod->dynTableName;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                        return 1;
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
            [[ 'bank', 'cardname', 'cardnumber', 'bankaddress', 'amount'], 'required'],
            [['user_id'], 'trim'],
            [['type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => '编号',
            'user_id' => '编号',
            'type'  => '提现类型',
            'status' => '状态',
            'bank' => '开户银行',
            'cardname' => '开户名',
            'cardnumber' => '银行卡号',
            'bankaddress' => '开户支行',
            'amount' => '提现金额',
            'password2' => '二级密码',
            'created_at' => '日期'
        ];
    }

    public function getBankNames($options= false)
    {
        return  $options ? array('' => '不限', 'ICBC' => '工商银行', 'ABC' => '农业银行') : array('ICBC' => '工商银行', 'ABC' => '农业银行');
    }

    public function getTypes($filter = false)
    {
        return  $filter ? array(''=> '不限', 1 => '分红提现', 2 => '绩效提现') : array(1 => '分红提现', 2 => '绩效提现');
    }

    public function getStatus($filter =false)
    {
        return  $filter ? array(''=> '不限', 1 => '未处理', 2 => '已发放', 3 => '拒绝') : array(1 => '未处理', 2 => '已发放', 3 => '拒绝');
    }
}
