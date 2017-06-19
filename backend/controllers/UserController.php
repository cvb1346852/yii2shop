<?php

namespace backend\controllers;

use backend\components\RbacFilter;
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
            $user->save(false);
            var_dump($user->roles);exit;
            $user->addRoldes();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['user/index']);
        }
        return $this->render('add',['user'=>$user]);
    }
    public function actionEdit($id){
        $user = User::findOne(['id'=>$id]);
        if ($user->load(\Yii::$app->request->post()) && $user->validate()){
            $user->save(false);
            $user->editRoldes();
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['user/index']);
        }
        $user->getRolesByUser();
//        var_dump($user->roles);exit;
        return $this->render('add',['user'=>$user]);
    }
    public function actionDelete($id){
        $user = User::findOne(['id'=>$id]);
        \Yii::$app->authManager->revokeAll($user->id);
        $user->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['user/index']);
    }
    public function actionLogin(){
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $user = \Yii::$app->user->identity;
            $user->last_login_ip = \Yii::$app->request->getUserIP();
            $user->save(false);
//          var_dump(\Yii::$app->user->isGuest);exit;
            return $this->redirect(['goods/index']);
        }
        if (\Yii::$app->user->isGuest){
            return $this->render('login',['model'=>$model]);
        }else{
            return $this->redirect(['goods/index']);
        }
    }
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['add','index','edit','delete'],
            ]
        ];
    }
}
