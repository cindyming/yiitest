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
    public $useBaodan;

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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                    return 1;
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
            [['be_stack'], 'number'],
            [['merited', 'note', 'added_by', 'status', 'duichong_invest', 'useBaodan', 'stack'], 'trim'],
            [['added_by'], 'validateAddedBy']
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
            'duichong_invest'  => '对冲帐户抵扣金额',
            'useBaodan' => '使用对冲帐户',
            'added_by' => '报单人编号',
            'created_at' => '追加时间',
            'updated_at' => '更新时间',
            'stack' => '等值股票数'
        ];
    }

    public function validateAddedBy($attribute, $params) {

        if ($this->added_by && $this->isNewRecord && ($this->added_by != '00000')) {
            $this->added_by = trim($this->added_by);
            $user = User::findById(trim($this->added_by));

            if ($user && $user->add_member) {
                $this->added_by = $user->id;
            } else {
                $this->addError('added_by', '报单员不存在,请确认后输入');
            }
        }

    }


    public function  getStatus()
    {
        $status = '正常';
        if  ($this->status == 2) {
            $status = '已兑换';
        } else if ($this->be_stack) {
            $status = '可兑换';
        } else if ($this->status == 1) {
            $status = '锁定中';
        } else if (!$this->status) {
            $status = '已取消';
        }

        return $status;
    }

}
