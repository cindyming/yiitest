<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;
    public $captcha;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['captcha', 'captcha','captchaAction'=>'site/captcha' ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'   => '会员编号',
            'password' => '密码',
            'captcha' => '验证码'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        if ($this->_user === null) {
            $this->_user = User::findById($this->username);
        }

        if ($this->_user->role_id == 4) {
            $this->_user = null;
            $this->addError('username',  '会员不存在, 请确认后再登陆');
        }

        if (($this->_user && ($this->_user->role_id != 1) && ($this->_user->role_id != 3) && !System::loadConfig('enable_memmber_login')) || ($this->_user && $this->_user->locked)) {
            $locked = (($this->_user && $this->_user->locked)) ? true : false;
            $this->_user = null;
            $this->addError('password', $locked ? '会员被锁定,请联系管理员.' : '系统关闭了会员登录功能，请联系管理员');
        }
        
        return $this->_user;
    }
}
