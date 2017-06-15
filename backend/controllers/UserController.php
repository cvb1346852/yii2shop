<?php

namespace backend\controllers;

use backend\models\User;
use backend\models\LoginForm;
use yii\data\Pagination;
use yii\filters\AccessControl;

class UserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = User::find()->count();
        $page = new Pagination([
           'totalCount'=>$count,
            'pageSize' => 2,
        ]);
        $users = User::find()->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['users'=>$users,'page'=>$page]);
    }
    public function actionAdd(){
        $user = new User(['scenario'=>User::SCENARIO_ADD]);
        if ($user->load(\Yii::$app->request->post()) && $user->validate()){
            $user->status = 1;
            $user->save(false);
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['user/index']);
        }
        return $this->render('add',['user'=>$user]);
    }
    public function actionEdit($id){
        $user = User::findOne(['id'=>$id]);
        if ($user->load(\Yii::$app->request->post()) && $user->validate()){
            if ($user->password_hash != $user->getOldAttribute('password_hash')){
                $user->password_hash = \Yii::$app->security->generatePasswordHash($user->password_hash);
            }
            $user->save(false);
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['user/index']);
        }
        return $this->render('add',['user'=>$user]);
    }
    public function actionDelete($id){
        $user = User::findOne(['id'=>$id]);
        $user->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['user/index']);
    }
    //验证码
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,//验证码最小长度
                'maxLength'=>4,//最大长度
            ],
        ];
    }
    public function actionLogin(){
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $user = \Yii::$app->user->identity;
            $user->last_login_ip = \Yii::$app->request->getUserIP();
            $user->save(false);
//          var_dump(\Yii::$app->user->isGuest);exit;
            return $this->redirect(['user/index']);
        }
        if (\Yii::$app->user->isGuest){
            return $this->render('login',['model'=>$model]);
        }else{
            return $this->redirect(['user/index']);
        }
    }
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>[
                    [
                        'allow' => true,
                        'actions' => ['login','captcha','add'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
//                        'actions' => ['add','edit','delete','index','logout'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }
}
