<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use app\models\Revenue;
use yii\behaviors\AttributeBehavior;

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
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'referer',
                ],
                'value' => function ($event) {
                        return Yii::$app->user->id;
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password','title', 'password2', 'identity', 'phone', 'referer', 'investment', 'bank', 'cardname', 'cardnumber', 'bankaddress'], 'required'],
            [['username', 'password'], 'string', 'max' => 100],
            [['password1'], 'compare', 'compareAttribute' => 'password'],
            [['password3'], 'compare', 'compareAttribute' => 'password2'],
            [['approved_at'], 'string'],
            [['role_id', 'merited', 'level'], 'number'],
            [['email'], 'email'],
            [['qq'], 'number']
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
            'password2' => '二级密码',
            'password3' => '二级密码确认',
            'identity' => '证件号码',
            'phone' => '手机号码',
            'title' => '称谓',
            'referer' => '推荐人',
            'investment' => '投资额',
            'bank' => '银行名称',
            'cardname' => '开户名',
            'cardnumber' => '银行账号',
            'bankaddress' => '开户地址',
            'email' => 'Email',
            'qq' => '常用QQ',
            'official' => '是否正式',
            'locked' => '是否锁定',
            'approved_at' => '审核日期',
            'created_at' => '注册时间',
            'password_old' => '原一级密码',
            'password2_old' => '原二级密码'
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
        return ($this->approved_at) ? '正式' : '非正式';
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

    public function getBankNames()
    {
        return  array('ICBC' => '工商银行', 'ABC' => '农业银行');
    }

    public function getLevelOptions()
    {
        return [
            1 => '普通会员',
            2 => 'VIP会员',
            3 => '主任',
            4 => '经理',
            5 => '一级总监',
            6 => '二级总监',
            7 => '三级总监',
        ];
    }

    public function getMeritRate()
    {
        $merits = array(
            3 => 0.05,
            4 => 0.1,
            5 => 0.16,
            6 => 0.23,
            7 => 0.31,
        );
        return $merits[$this->level];
    }

    public function diamondLevel()
    {
         $users = User::find()->where(['=', 'referer', $this->id])->orderBy(['achievements' => SORT_ASC])->limit(3)->all();
        if (count($users) > 3 ) {
            return $users[0]->achievements;
        } else {
            return 0;
        }
    }

    public function calculateLevel()
    {
        if ($this->achievements) {
            $level = 2;
            if (500000 > $this->achievements) {
                $level = ($this->investment < 200000) ? '1' : '2';
            } elseif ($this->achievements < 1500000) {
                $level = 3;
            } elseif ($this->achievements < 12000000) {
                $level = 4;
            } else {
                $minAchivements = $this->diamondLevel();
                if ($minAchivements < 4000000) {
                    $level = 4;
                } elseif ( $minAchivements < 10000000) {
                    $level = 5;
                } elseif ($this->achievements < 20000000) {
                    $level = 6;
                } else {
                    $level = 7;
                }
            }
            return $level;
        } else {
            return ($this->investment < 200000) ? '1' : '2';
        }
    }
}
