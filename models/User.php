<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 3;
    const ROLE_ADMIN = 1;

    public $password1;
    public $password3;
    public $password_old;
    public $password_check;
    public $password2_old;
    public $captcha;
    public $old_username;
    public $useBaodan;

    public $dynTableName = '{{%user}}';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $mod = new User();
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'referer',
                ],
                'value' => function ($event) {
                        if (!$this->referer) {
                            return Yii::$app->user->identity->id;
                        } else {
                            return $this->referer;
                        }
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'level',
                ],
                'value' => function ($event) {
                         return $this->calculateLevel();
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'added_by',
                ],
                'value' => function ($event) {
                        return Yii::$app->user->identity->id;
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key',
                ],
                'value' => function ($event) {
                        return sha1(rand());
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'duichong_invest',
                ],
                'value' => function ($event) {
                    return ($this->useBaodan) ?  $this->duichong_invest : 0;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'locked',
                ],
                'value' => function ($event) {
                    return 0;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'investment',
                ],
                'value' => function ($event) {
                        return $this->investment * 10000;
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token',
                ],
                'value' => function ($event) {
                        return sha1(rand());
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'password_check',
                ],
                'value' => function ($event) {
                        if (!Yii::$app->user->identity->isAdmin()) {

                        }
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'password',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'password',
                ],
                'value' => function ($event) {
                        if ($this->password1) {
                            return sha1($this->password);
                        } else {
                            return $this->password;
                        }
                    },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'password2',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'password2',
                ],
                'value' => function ($event) {
                        if ($this->password3) {
                            return sha1($this->password2);
                        } else {
                            return $this->password2;
                        }
                    },
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($insert) {
            $id = System::loadConfig('last_id');
            Yii::$app->db->createCommand('UPDATE system set value=' . ($id+1) . ' where id=5' )->execute();
            $this->id = $id . rand(1, 20);
            $this->password1 = null;
            $this->password3 = null;
            $this->save(false);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password','title', 'password2',  'identity', 'phone',  'investment', 'bank', 'cardname', 'cardnumber', 'bankaddress'], 'required'],
            [['username', 'password'], 'string', 'max' => 100],
            [['password1'], 'compare', 'compareAttribute' => 'password'],
            [['password3'], 'compare', 'compareAttribute' => 'password2'],
            [['password3', 'password', 'password1', 'password2'], 'string', 'min' => 6],
            [['approved_at'], 'string'],
            [['referer', 'added_by', 'achievements', 'suggest_by', 'locked'], 'trim'],
            [['role_id', 'merited', 'level', 'add_member', 'stop_bonus'], 'number'],
            [['bonus_total', 'merit_total', 'duichong_invest', 'duichong_total', 'duichong_remain', 'baodan_total'], 'double'],
            [['bonus_remain', 'baodan_remain'], 'number', 'min' => 0, 'tooSmall' => '会员' . $this->id . '分工余额不足', 'on' => 'cancel'],
            [['merit_remain'], 'number', 'min' => 0, 'tooSmall' => '会员' . $this->id . '绩效余额不足', 'on' => 'cancel'],
            [['bonus_total', 'merit_total', 'duichong_total', 'duichong_remain'], 'double'],
            [['email'], 'email'],
            [['qq', 'useBaodan'], 'number'],
            [['duichong_invest'], 'checkBaodanInvest'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '会员编号',
            'username' => '网络昵称',
            'password' => '一级密码',
            'password1' => '一级密码确认',
            'password_check' => '您的二级密码',
            'old_username' => '原登录名',
            'password2' => '二级密码',
            'password3' => '二级密码确认',
            'identity' => '证件号码',
            'phone' => '手机号码',
            'duichong_invest'  => '对冲帐户抵扣金额',
            'useBaodan' => '使用对冲帐户',
            'title' => '称谓',
            'referer' => '接点人',
            'suggest_by' => '推荐人',
            'investment' => '投资额',
            'add_member' => '开放报单员权限',
            'bank' => '银行名称',
            'level' => '会员等级',
            'cardname' => '开户名',
            'cardnumber' => '银行账号',
            'bankaddress' => '开户地址',
            'merit_remain' => '绩效余额',
            'bonus_remain' => '分红余额',
            'mall_remain' => '商城币余额',
            'baodan_remain' => '服务费余额',
            'merit_total' => '绩效总额',
            'bonus_total' => '分红总额',
            'mall_total' => '商城币总额',
            'baodan_total' => '服务费总额',
            'email' => 'Email',
            'qq' => '常用QQ',
            'official' => '是否正式',
            'locked' => '是否锁定',
            'approved_at' => '审核日期',
            'created_at' => '注册时间',
            'baodan_id' => '报单员编号',
            'password_old' => '原一级密码',
            'password2_old' => '原二级密码',
            'duichong_total' => '对冲帐户总额',
            'duichong_remain' => '对冲帐户余额'
        ];
    }

    /** INCLUDE USER LOGIN VALIDATION FUNCTIONS**/
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    /* modified */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /* removed
        public static function findIdentityByAccessToken($token)
        {
            throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        }
    */
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by Id
     *
     * @param  string      Id
     * @return static|null
     */
    public static function findById($username)
    {
        return static::findOne(['id' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === sha1($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = ($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword2($password)
    {
        $this->password2 = ($password);
    }

    public function validatePassword2($password)
    {
        return $this->password2 === sha1($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    /** EXTENSION MOVIE **/

    public function isAdmin()
    {
        return 1==$this->role_id;
    }

    public function getStatus()
    {
        return ($this->role_id == 3) ? '正式' : ( ($this->role_id == 4) ? '拒绝' : '待审核');
    }

    public function getLockedOptions()
    {
        return array(
            '' => '锁定状态',
            0 => '未锁定',
            1 => '锁定'
        );
    }

    public function getParennt()
    {
        return $this->hasOne(User::className(), ['id' => 'referer'])->from(User::tableName().' us');// from设置别名，尽量避免手写表名称，会要求手动添加表前缀
    }

    public function getChild()
    {
        return $this->hasOne(User::className(), ['referer' => 'id'])->from(User::tableName().' us');// from设置别名，尽量避免手写表名称，会要求手动添加表前缀
    }

    public function getSuggest()
    {
        return $this->hasOne(User::className(), ['id' => 'suggest_by'])->from(User::tableName().' us');// from设置别名，尽量避免手写表名称，会要求手动添加表前缀
    }


    public function getBankNames($filter = false)
    {
        return  $filter ? array('' => '不限', 'ICBC' => '工商银行', 'ABC' => '农业银行') : array('ICBC' => '工商银行', 'ABC' => '农业银行');
    }

    public function getLevelOptions($filter = false)
    {
        if ($filter) {
            return array(
                '' => '不限',
                1 => '实习生',
                2 => '业务员',
                3 => '主任',
                4 => '经理',
                5 => '一级总监',
                6 => '二级总监',
                7 => '三级总监',
            //    8 => '区域总监',
                9 => '全国总监',
                10 => '钻石级总监',
            );
        } else {
            return array(
                1 => '实习生',
                2 => '业务员',
                3 => '主任',
                4 => '经理',
                5 => '一级总监',
                6 => '二级总监',
                7 => '三级总监',
             //   8 => '区域总监',
                9 => '全国总监',
                10 => '钻石级总监',
            );
        }
    }

    public function getMeritRate($level = false)
    {
        $merits = array(
            1 => 0.03,
            2 => 0.06,
            3 => 0.09,
            4 => 0.12,
            5 => 0.16,
            6 => 0.19,
            7 => 0.22,
          //  8 => 0.25,
            9 => 0.25
        );
        return $merits[$level ? $level : $this->level];
    }

    public function diamondLevel()
    {
         $users = User::find()->where(['=', 'referer', $this->id])->andWhere(['=', 'role_id', 3])->andWhere(['=', 'merited', 1])->orderBy(['achievements' => SORT_ASC])->limit(3)->all();

        if (count($users) == 3 ) {
            return $users[0]->achievements;
        } else {
            return 0;
        }
    }

    public function isDiamondLevel()
    {
        $users = User::find()->where(['=', 'referer', $this->id])->andWhere(['=', 'level', 9])->andWhere(['=', 'merited', 1])->orderBy(['achievements' => SORT_ASC])->limit(3)->all();
        return (count($users) == 3) ? true : false;
    }

    public function calculateLevel()
    {
        $achievements = $this->achievements ? $this->achievements : $this->investment;
        if (500000 > $achievements && $achievements< 500000) {
            $level = 1;
        } elseif ( $achievements < 1000000 ) {
            $level = 2;
        } elseif ( $achievements < 2000000) {
            $level = 3;
        } elseif ( $achievements < 9000000) {
            $level = 4;
        } else {
            $minAchivements = $this->diamondLevel();
            if ($minAchivements < 3000000) {
                $level = 4;
            } elseif ($minAchivements < 6000000) {
                $level = 5;
            } elseif ($minAchivements < 10000000) {
                $level = 6;
            } elseif ($minAchivements < 20000000) {
                $level = 7;
            } elseif ($minAchivements < 20000000&&false) {
                $level = 8;
            } elseif ($this->isDiamondLevel()) {
                $level = 10;
            } else {
                $level = 9;
            }
        }
        return $level;
    }

    static public function isExist($id)
    {
        $user = User::findOne($id);
        return ($user && $user->id) ?  true : false;
    }

    public function resetPasword()
    {
        $this->password = '123456';
        $this->password2 = '123456';
        $this->password1 = '123456';
        $this->password3 = '123456';
        return $this->save();
    }

    public function haveTree()
    {
        if (System::loadConfig('open_member_tree')) {
            return true;
        }

        if (System::loadConfig('open_baodan_tree')  &&  (Yii::$app->user->identity->add_member == 2 )) {
            return true;
        }

        return false;
    }

    public function openSuggestion()
    {
        if (System::loadConfig('open_suggest_list')) {
            return true;
        }
        return false;
    }


    public function listParents($user, &$parents)
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        $parent = $user->getParennt()->one();
        if ($parent && $parent->role_id != 1) {
            $parents[] = $parent;

            $this->listParents($parent, $parents);
        }
    }

    public function reduceAchivement($amount)
    {
        $this->investment = ($this->investment - $amount) ? $this->investment - $amount : $this->investment;
        $this->achievements = $this->achievements - $amount;

        $parents = array();

        $this->listParents($this, $parents);

        foreach ($parents as $parent) {
            if ($parent && $parent->role_id != 1) {
                $parent->achievements = $parent->achievements - $amount;
                if (!$parent->save(true, array('achievements'))) {
                    throw new Exception('Failed to save user ' . User::arrayToString($parent->getErrors()));
                }
            }
        }

    }

    public function reduceMerit($investment)
    {
        $revenus = Revenue::find()->andFilterWhere(['like', 'note', '追加投资 - ' . $investment->id . ''])->all();
        foreach ($revenus as $re) {
            $merit_amount = $re->merit;
            $user = User::findById($re->user_id);
            if($merit_amount) {
                $merit_amount = round($merit_amount, 2);
                $merit_remain = round($merit_amount * 0.9);

                $user->mall_remain -= ($merit_amount - $merit_remain);
                $user->mall_total -= ($merit_amount - $merit_remain);
                $user->merit_total -= $merit_amount;
                $user->merit_remain -= $merit_remain;

                    $meritData = array(
                        'user_id' => $re->user_id,
                        'note' => '错误报单,撤销会员[' .$investment->user_id . ']的追加投资'.$investment->amount.' - ' . $investment->id .'单,绩效扣除:' . $re->id,
                        'amount' => $merit_remain,
                        'type' => 5,
                        'status' => 2,
                        'total' => $user->merit_remain
                    );

                    $merit = new Cash();
                    $merit->load($meritData, '');

                    $mallData = array(
                        'user_id' => $re->user_id,
                        'note' => '错误报单,撤销会员[' .$investment->user_id . ']的追加投资'.$investment->amount.' - ' . $investment->id .'单,商城币扣除:' . $re->id,
                        'amount' => ($merit_amount - $merit_remain),
                        'type' => 7,
                        'status' => 2,
                        'total' => $user->mall_remain
                    );
                    $mall = new Cash();
                    $mall->load($mallData, '');
                    $user->setScenario('cancel');

                    if(!$user->save(true, array('mall_remain','mall_total', 'merit_total','merit_remain')) || !$merit->save() || !$mall->save()) {
                        throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($merit->getErrors()). User::arrayToString($mall->getErrors()));
                        break;
                    }
            }
        }
    }

    public function reduceBaodan($investment)
    {
        $revenu = Revenue::find()->andFilterWhere(['like', 'note', '追加投资' . $investment->id . '的报单奖励'])->one();
        if ($revenu && $revenu->id && $revenu->baodan) {
            $amount = $revenu->baodan;
            $user = User::findById($revenu->user_id);
            if ($user) {
                $user->baodan_remain -= $amount;
                $user->baodan_total -= $amount;
                $meritData = array(
                    'user_id' => $revenu->user_id,
                    'note' => '错误报单,撤销会员[' .$investment->user_id . ']的追加投资'.$investment->amount.' - ' . $investment->id .'单,报单费扣除:' . $revenu->id,
                    'amount' => $amount,
                    'type' => 6,
                    'status' => 2,
                    'total' => $user->baodan_remain
                );

                $merit = new Cash();
                $merit->load($meritData, '');
                if(!$user->save(true, array('baodan_remain','baodan_total')) || !$merit->save() ) {
                    throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()). User::arrayToString($merit->getErrors()));
                }
            }

        }

        $revenu = Cash::find()->andFilterWhere(['like', 'note', '追加投资' . $investment->id . ', 使用对冲帐户金额'])->one();
        if ($revenu && $revenu->id && $revenu->amount) {
            $baodan_amount = $revenu->amount;
            $user = User::findById($revenu->user_id);
            $user->duichong_remain += $baodan_amount;

            $mallData = array(
                'user_id' => $revenu->user_id,
                'note' => '错误报单,撤销会员[' .$this->id . '],投资'.$revenu->amount.', 对冲金额返回:' . $revenu->id,
                'duichong' => $baodan_amount,
                'type' => 2,
                'total' => $user->duichong_remain
            );
            $mall = new Revenue();
            $mall->load($mallData, '');
            $user->setScenario('cancel');

            if (!$mall->save()  || !$user->save(true, array('duichong_remain'))) {
                throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($mall->getErrors()));
            }

        }

    }


    public function reduceMeritForNewMember($amount)
    {
        $revenus = Revenue::find()->andFilterWhere(['like', 'note', '新会员绩效 -  ' . $this->id])->all();

        foreach ($revenus as $re) {
            $merit_amount = $re->merit;
            $user = User::findById($re->user_id);
            if($merit_amount) {
                $merit_amount = round($merit_amount, 2);
                $merit_remain = round($merit_amount * 0.9);

                $user->mall_remain -= ($merit_amount - $merit_remain);
                $user->mall_total -= ($merit_amount - $merit_remain);
                $user->merit_total -= $merit_amount;
                $user->merit_remain -= $merit_remain;

                $meritData = array(
                    'user_id' => $re->user_id,
                    'note' => '错误报单,撤销会员[' .$this->id . '],投资'.$amount.',绩效扣除: ' . $re->id,
                    'amount' => $merit_remain,
                    'type' => 5,
                    'status' => 2,
                    'total' => $user->merit_remain
                );

                $merit = new Cash();
                $merit->load($meritData, '');

                $mallData = array(
                    'user_id' => $re->user_id,
                    'note' => '错误报单,撤销会员[' .$this->id . '],投资'.$amount.'商城币扣除:' . $re->id,
                    'amount' => ($merit_amount - $merit_remain),
                    'type' => 7,
                    'status' => 2,
                    'total' => $user->mall_remain
                );
                $mall = new Cash();
                $mall->load($mallData, '');
                $user->setScenario('cancel');
                if(!$user->save(true, array('mall_remain', 'mall_total','merit_total', 'merit_remain')) || !$merit->save() || !$mall->save()) {
                    throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($merit->getErrors()). User::arrayToString($mall->getErrors()));
                    break;
                }
            }
        }

        $revenu = Revenue::find()->andFilterWhere(['like', 'note',  $this->id . '的报单奖励'])->one();
        if($revenu && $revenu->baodan) {
            $baodan_amount = $revenu->baodan;
            $user = User::findById($revenu->user_id);
            $user->baodan_total -= $baodan_amount;
            $user->baodan_remain -= $baodan_amount;

            $mallData = array(
                'user_id' => $revenu->user_id,
                'note' => '错误报单,撤销会员[' .$this->id . '],投资'.$amount.'保单费扣除:' . $revenu->id,
                'amount' => $baodan_amount,
                'type' => 6,
                'status' => 2,
                'total' => $user->baodan_remain
            );
            $mall = new Cash();
            $mall->load($mallData, '');
            $user->setScenario('cancel');

            if (!$mall->save()  || !$user->save(true, array('baodan_total', 'baodan_remain'))) {
                throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($mall->getErrors()));
            }
        }

    }

    public function reduceDuicong()
    {
        $revenu = Cash::find()->andFilterWhere(['like', 'note', '报单:' . $this->id . ', 使用对冲帐户金额'])->one();
        if ($revenu && $revenu->id && $revenu->amount) {
            $baodan_amount = $revenu->amount;
            $user = User::findById($revenu->user_id);
            $user->duichong_remain += $baodan_amount;

            $mallData = array(
                'user_id' => $revenu->user_id,
                'note' => '撤销会员[' .$this->id . '], 对冲金额返回:' . $revenu->id,
                'duichong' => $baodan_amount,
                'type' => 2,
                'total' => $user->duichong_remain
            );
            $mall = new Revenue();
            $mall->load($mallData, '');
            $user->setScenario('cancel');

            if (!$mall->save()  || !$user->save(true, array('duichong_remain'))) {
                throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($mall->getErrors()));
            }

        }
    }


    public function reduceBonus($investment)
    {
        $bouns = 0;

        $revenus = Revenue::find()
            ->where(['=', 'user_id', $this->id])
            ->andFilterWhere(['like', 'note', '分红结算'])
            ->andWhere(['>', 'created_at', $investment->created_at])
            ->orderBy(array('created_at' => SORT_ASC))
            ->All();

        $bonusIds = '';

        foreach ($revenus as $key => $re) {
            $investments = $this->investment;
            $time =  $re->created_at;

            $investmentss = Investment::find()->where(['=', 'user_id', $this->id])->andWhere(['>', 'created_at', $time])->all();

            foreach ($investmentss as $inv) {
                $investments -= $inv->amount;
            }

            $rate = 1;
            $days = 15;


            if (!$key) {
                $days = (strtotime(date('Y-m-d', strtotime($re->created_at))) - strtotime(date('Y-m-d', strtotime($investment->created_at)))) / 86400;
            }


            if ($days < 15) {
                $rate = $days / 15;
            }

            if (date('Y-m-d', strtotime($investment->created_at)) >=  '2016-06-05') {
                if ($investments >= 200000) {
                    $amount =  $investment->amount * 0.015;
                } else {
                    $amount =  $investment->amount * 0.01;
                }
            } else {
                if ($investments < 100000) {
                    $amount =  $investment->amount * 0.01;
                } else if ($investments < 200000) {
                    $amount =  $investment->amount * 0.015;
                } else {
                    $amount =  $investment->amount * 0.02;
                }
            }

            $amount = $amount * $rate;

            $bouns += $amount;
            $bonusIds .= $re->id . ':' . $amount . ';';
        }

        if ($bouns) {
            $this->bonus_total -= $bouns;
            $this->bonus_remain -= $bouns;
            $meritData = array(
                'user_id' => $this->id,
                'note' => '错误报单,撤销会员[' .$this->id . ']的追加投资'.$investment->amount.' - ' . $investment->id .'单,分红扣除:' . $bonusIds,
                'amount' => $bouns,
                'type' => 4,
                'status' => 2,
                'total' => $this->bonus_remain
            );

            $revenus = new Cash();
            $revenus->load($meritData, '');
            if (!$revenus->save()) {
                throw new Exception('分红扣除失败 ' . User::arrayToString($revenus->getErrors()));
            }
        }
    }

    public static function arrayToString($errors) {
        $result = '';

        foreach ($errors as $r) {
            if (is_array($r)) {
                $result .= implode(', ', $r);
            } else if (is_string($r)) {
                $result .=  $r;
            }

        }

        return $result;
    }

    public function isBaodan()
    {
        return ((3==$this->role_id)  && $this->add_member);
    }

    public function checkBaodanInvest($attribute, $params) {

        if ($this->useBaodan && $this->isNewRecord) {
            $this->duichong_invest = floatval($this->duichong_invest);
            if ($this->duichong_invest <=  0) {
                $this->addError('duichong_invest', '对冲帐户金额必须大于0');
            } else if ($this->duichong_invest > Yii::$app->user->identity->duichong_remain) {
                $this->addError('duichong_invest', '对冲帐户余额不足: ' .  Yii::$app->user->identity->duichong_remain);
            }
        }

    }



    public function beforeSave($insert)
    {
        if (!$insert) {
            $user  =  User::findById($this->id);
            if ($user && $user->id) {
                $oldInfo = $user->getAttributes();
                $newInfo = $this->getAttributes();

                $from = array_diff_assoc($oldInfo, $newInfo);
                $to = array_diff_assoc($newInfo, $oldInfo);

                $action = "属性更新:" . json_encode($from) . 'TO:' . json_encode($to);
                $action .= Yii::$app->request->isConsoleRequest ? "Script: " . json_encode(Yii::$app->request->getParams()) :  "URL: " . Yii::$app->request->getAbsoluteUrl() ;

                Log::add('USER' . $this->id, '更新信息', true, $action);

            }

        }

        return parent::beforeSave($insert);
    }

    public function listParentsAddMerit($user, &$parents)
    {
        $parent = $user->getSuggest()->one();

        if ($parent && $parent->role_id != 1) {

            if  (!$parent->locked) {
                if (isset($parents[$parent->id])) {
                    throw new Exception('用户:' . $user->id . '的推荐人存在死循环');
                } else {
                    $parents[$parent->id] = $parent->id;
                    $this->listParentsAddMerit($parent, $parents);

                }
            }


        }
    }

    public function listParentsAddInvestment($user, &$parents )
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        $parent = $user->getParennt()->one();
        if ($parent && $parent->role_id != 1) {
            if (isset($parents[$parent->id])) {
                throw new Exception('用户:' . $user->id . '的接点人存在死循环');
            } else {
                $parents[$parent->id] = $parent->id;
                $this->listParentsAddInvestment($parent, $parents);
            }

        }
    }

    public function canApproved()
    {
        $parents = array();
        $this->listParentsAddMerit($this, $parents);

        $parents = array();
        $this->listParentsAddInvestment($this, $parents);

        return true;

    }
}
