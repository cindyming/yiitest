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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'cardnumber',
                ],
                'value' => function ($event) {
                    if ($this->cardnumber) {
                        return trim(Yii::$app->user->identity->cardnumber);
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
                        if ($this->cash_type  == 3) {
                            return  $this->amount * (1 - floatval(3 / 100));
                        } else {
                            return  $this->amount * (1 - floatval(System::loadConfig('cash_factorage')  / 100));
                        }
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
            [['amount', 'type'], 'required'],
            [['cardnumber'], 'number'],
            [['amount'], 'double'],
            [['cardname'], 'trim'],
            [['cardnumber'], 'string', 'max' => 19],
            [['cardnumber', 'cardname', 'bank', 'bankaddress', 'password2'], 'required', 'on' => 'create'],
            [['stack_number', 'password2'], 'required', 'on' => 'transfer'],
            [['user_id', 'password2'], 'required', 'on' => 'baodan'],
            [['sc_account', 'telephone', 'password2'], 'required', 'on' => 'mallmoney'],
            [['sc_account', 'telephone'], 'checkAccount', 'on' => 'mallmoney'],
            [['user_id'], 'required', 'on' => 'cuohe'],
            [['user_id'], 'required', 'on' => 'manual'],
            [['user_id', 'note', 'bank', 'status', 'cash_type', 'baodan_id', 'stack_number', 'cardname', 'cardnumber', 'bankaddress', 'real_amount', 'total', 'sc_account'], 'trim'],
            [['type'], 'integer'],
            [['baodan_id'], 'validateBaodan']
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
            'type'  => '出账账户',
            'status' => '状态',
            'bank' => '开户银行',
            'cardname' => '开户名',
            'cardnumber' => '银行卡号',
            'bankaddress' => '开户支行',
            'stack_number' => '股票会员编号',
            'amount' => '提现金额',
            'baodan_id' => '报单员编号',
            'total' => '出账后余额',
            'note' => '摘要',
            'password2' => '二级密码',
            'created_at' => '日期',
            'sc_account' => '商城登录名',
            'telephone' => '商城用户手机号码'
        ];
    }

    public function getBankNames($options= false)
    {
        return  $options ? array('' => '不限', 'ICBC' => '工商银行', 'ABC' => '农业银行') : array('ICBC' => '工商银行', 'ABC' => '农业银行');
    }

    public function getTypes($filter = false)
    {
        return  $filter ? array(''=> '不限', 1 => '分红提现', 2 => '绩效提现', 3 => '服务费', 4 => '分红支出', 5 => '绩效支出', '6' => '服务费支出', '7' => '商城币支出', 8=>'对冲帐户', 9=>'商城币提现', 11 => '自由股兑换')
            : array(1 => '分红提现', 2 => '绩效提现', 3 => '服务费提现', 4 => '分红支出', 5 => '绩效支出', '6' => '服务费支出', '7' => '商城币支出',  8=>'对冲帐户', 9=>'商城币提现', 11 => '自由股兑换');
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


    public function validateBaodan($attribute, $params) {

        if ($this->baodan_id && $this->isNewRecord) {
            $this->baodan_id = trim($this->baodan_id);
            $user = User::findById(trim($this->baodan_id));

            if ($user && $user->add_member) {
                $this->baodan_id = $user->id;
            } else {
                $this->addError('baodan_id', '报单员不存在,请确认后输入');
            }
        }

    }

    public static function getCachType($type = null) {
        $data = array(
            '' => '不限',
            1 => '提现至股票',
            2 => '银行卡提现',
            3 => '提现至报单员',
            4 => '提现至商城',
            5 => '提现至撮合',
            6 => '自由股兑换'
        );

        return $type ? (isset($data[$type]) ? $data[$type] : '未知类型') : $data;
    }

    public function validaccount()
    {
        $service_url = Yii::$app->params['valid_account_url'];
        $data = array('account='.$this->sc_account, 'mobile='. $this->telephone, 'key=' . Yii::$app->params['sc_key']);
        $sign = strtoupper(md5(implode('&', $data)));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $service_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('account' => $this->sc_account, 'mobile' => $this->telephone, 'sign' => $sign)
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $response = json_decode($response);
        curl_close($curl);

        Log::add('会员(' . $this->user_id . ')' , ' 商城信息验证' , '返回' , json_encode($response) . ':' . $service_url);

        return $response;
    }

    public function checkAccount($attribute, $params) {

        if ($this->sc_account && $this->telephone && $this->isNewRecord) {
            $response = $this->validaccount();
            if ($response && $response->errorCode) {
                $this->addError('telephone', '商城用户名和手机号码不匹配请确认后输入');
            }
        }

    }

    public function getCashInfo()
    {
        $info = '';
        switch ($this->cash_type) {
            case 2:
                $info = '<ul>';
                $info .= '<li><label>银行名称: </label>' . (isset($this->getBankNames()[$this->bank]) ? $this->getBankNames()[$this->bank] : '') . '</li>';
                $info .= '<li><label>开户名: </label>'. $this->cardname .'</li>';
                $info .= '<li><label>银行卡号: </label>'. $this->cardnumber .'</li>';
                $info .= '<li><label>开户行: </label>'. $this->bankaddress .'</li>';
                $info .= '</ul>';
                break;
            case 1:
                $info = '<ul>';
                $info .= '<li><label>股票会员编号: </label>'. $this->stack_number .'</li>';
                $info .= '</ul>';
                break;
            case 3:
                $info = '<ul>';
                $info .= '<li><label>报单员编号: </label>'. $this->baodan_id .'</li>';
                $info .= '</ul>';
                break;
            case 4:
                $info = '<ul>';
                $info .= '<li><label>商城登录名: </label>'. $this->sc_account .'</li>';
                $info .= '</ul>';
                break;
            case 5:
                break;
        }
        return $info;
    }

    public function transterToCuohe($user)
    {
        $pass = false;

        $service_url = Yii::$app->params['cuohe_url'] . 'api/user/exchange';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $user->access_token); //Your credentials goes here
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('exchange' => $this->real_amount, 'token' => $user->access_token)));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate


        $curl_response = curl_exec($curl);
        $response = (array)json_decode($curl_response);
        curl_close($curl);

        Log::add('会员(' . $this->user_id . ')' , '撮合转账' , '返回' , $curl_response);
        if (is_array($response) && isset($response['code']) && ($response['code'] == 200)) {
            $pass = true;
            $this->note = $this->note . ($this->note ?  ';' : '' ) .  '撮合转账成功, id:' . $response['data'];
        } else {
            Log::add('会员(' . $this->user_id . ')', '撮合转账', '失败', $curl_response);
        }

        return $pass;
    }
}
