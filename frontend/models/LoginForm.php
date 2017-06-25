<?php
namespace frontend\models;

use backend\models\Goods;
use yii\base\Model;
use yii\web\Cookie;

class LoginForm extends Model{
    public $username;
    public $password;
    public $rememberMe;
    public $code;

    public function rules(){
        return [
            [['username','password'],'required'],
            ['username','validateUsername'],
            ['rememberMe','integer'],
            ['code','captcha'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'账号：',
            'password'=>'密码：',
            'code'=>'验证码：',
            'rememberMe'=>'记住我',
        ];
    }
    public function validateUsername(){
        $user = Member::findOne(['username'=>$this->username]);
        if ($user && \Yii::$app->security->validatePassword($this->password,$user->password_hash)){
            $user->last_login_time = time();
            $user->last_login_ip = \Yii::$app->request->getUserIP();
            $user->save(false);
            $remember = $this->rememberMe ? 7*24*3600 : 0;
            \Yii::$app->user->login($user,$remember);
        }else{
            $this->addError('username','账号或密码错误');
        }
    }
    public function addCart(){
        $cookies = \Yii::$app->request->cookies;
        $cookie = $cookies->get('cart');
        if ($cookie != null){
            $cart = unserialize($cookie);
            foreach ($cart as $k=>$amount){
                $member_id = \Yii::$app->user->getId();
                if ($cart = Cart::findOne(['goods_id'=>$k,'member_id'=>$member_id])){
                    $cart->amount = $amount;
                    $cart->save();
                }else{
                    $cart = new Cart();
                    $cart->amount = $amount;
                    $cart->goods_id = $k;
                    $cart->member_id = \Yii::$app->user->getId();
                    if ($cart->validate()){
                        $cart->save();
                    }
                }
            }
            $cookie = new Cookie([
                'name'=>'cart','value'=>''
            ]);
            $cookies = \Yii::$app->response->cookies;
            $cookies->remove($cookie);//清空购物车
        }
    }

}