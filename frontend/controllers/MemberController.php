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
        $categorys = GoodsCategory::find()->where(['parent_id'=>0])->all();
        return $this->render('index',['categorys'=>$categorys]);
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
            return $this->redirect(['member/index']);
        }
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
