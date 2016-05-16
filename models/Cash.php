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
                        if ($this->status) {
                            return $this->status;
                        } else {
                            return 1;
                        }

                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'cash_type',
                ],
                'value' => function ($event) {
                    if ($this->cash_type) {
                        return $this->cash_type;
                    } else {
                        return 2;
                    }

                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'real_amount',
                ],
                'value' => function ($event) {
                    if ($this->type == 2) {
                        return  $this->amount * (1 - floatval(System::loadConfig('cash_factorage')  / 100));
                    } else {
                        return $this->amount;
                    }
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
                'value' => function ($event) {
                        if (!$this->user_id) {
                            return Yii::$app->user->id;
                        } else {
                            return $this->user_id;
                        }

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
            [['amount'], 'required'],
            [['user_id', 'note', 'bank', 'status', 'cash_type', 'stack_number', 'cardname', 'cardnumber', 'bankaddress', 'real_amount', 'total'], 'trim'],
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
            'user_id' => '会员编号',
            'type'  => '账户类型',
            'status' => '状态',
            'bank' => '开户银行',
            'cardname' => '开户名',
            'cardnumber' => '银行卡号',
            'bankaddress' => '开户支行',
            'stack_number' => '股票会员编号',
            'amount' => '提现金额',
            'total' => '出账后余额',
            'note' => '摘要',
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
        return  $filter ? array(''=> '不限', 1 => '分红提现', 2 => '绩效提现', 3 => '服务费', 4 => '分红支出', 5 => '绩效支出', '6' => '服务费支出', '7' => '商城币支出')
            : array(1 => '分红提现', 2 => '绩效提现', 3 => '服务费提现', 4 => '分红支出', 5 => '绩效支出', '6' => '服务费支出', '7' => '商城币支出');
    }

    public function getStatus($filter =false)
    {
        return  $filter ? array(''=> '不限', 1 => '未处理', 2 => '已发放', 3 => '拒绝') : array(1 => '未处理', 2 => '已发放', 3 => '拒绝');
    }

    public function getType()
    {
        $types = $this->getTypes();
        return isset($types[$this->type]) ? $types[$this->type] : '';
    }


}
