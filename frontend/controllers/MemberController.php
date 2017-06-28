<?php

namespace frontend\controllers;


use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;

use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;

class MemberController extends \yii\web\Controller
{
    public $layout='login';
    public function actionIndex()
    {
        $this->layout='Index';
        return $this->render('index');
    }
    public function actionRegister(){
        $model = new Member();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save(false);
            \Yii::$app->session->setFlash('success','添加用户成功');
            return $this->redirect(['member/index']);
        }
        return $this->render('register',['model'=>$model]);
    }
    public function actionEdit(){

    }
    //用户登录
    public function actionLogin(){

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
//            \Yii::$app->session->setFlash('success','登录成功');
            $model->addCart();
            if (\Yii::$app->user->getReturnUrl() == '/'){
                return $this->redirect(['member/index']);//返回主页
            }else{
                return $this->goBack();//返回登录前页面
            }
        }
        \Yii::$app->user->setReturnUrl(\Yii::$app->request->referrer);//设置goBack为登录前页面
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        return $this->redirect(['member/index']);
    }
    //短信验证
    public function actionSmsSend(){
        $tel = \Yii::$app->request->get('tel');
        $code = rand(1000,9999);
        echo $code;
        $rs = 1;
//        $rs = \Yii::$app->sms->setNum($tel)->setCode($code)->send();
        if ($rs){
            \Yii::$app->session->set('tel'.$tel,$code);
            return true;
        }else{
            return false;
        }
    }

}
