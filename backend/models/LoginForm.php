<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $code;
    public $rememberMe;

    public function rules()
    {
        return [
            ['rememberMe','integer'],
            [['username','password'],'required'],
            ['username','validateUsername'],
            ['code','captcha'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'rememberMe'=>'记住我',
        ];
    }
    public function validateUsername(){
            $user = User::findOne(['username' => $this->username]);
            if ($user) {
                if (\Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                    $user->touch('last_login_time');
                    $duration = $this->rememberMe?7*24*3600:0;
                    \Yii::$app->user->login($user,$duration);
                    if ($this->rememberMe){

                    }
                } else {
                    $this->addError('username', '账号或密码错误');
                }
            } else {
                $this->addError('username', '账号或密码错误');
            }
    }
}