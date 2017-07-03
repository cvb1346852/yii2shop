<?php
namespace frontend\models;

use yii\base\Model;

class UpdatePwdForm extends Model
{
    public $id;
    public $old_password;
    public $password_1;
    public $password_2;
    public $code;

    public function rules()
    {
        return [
            [['id','old_password', 'password_1', 'password_2'], 'required'],
            ['password_2','compare','compareAttribute'=>'password_1','message'=>'两次密码必须一致'],
            ['old_password', 'validatePwd'],
//            ['code','captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => '旧密码：',
            'password_1' => '新密码：',
            'password_2' => '确认新密码：',
            'code' => '验证码：',
        ];
    }
    public function validatePwd(){
        $user = Member::findOne(['id'=>$this->id]);
        if ($user && \Yii::$app->security->validatePassword($this->old_password,$user->password_hash)){
            $user->password_hash = \Yii::$app->security->generatePasswordHash($this->password_1);
            $user->save(false);
        }else{
            $this->addError('username','原密码错误');
        }
    }
}