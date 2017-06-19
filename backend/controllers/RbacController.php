<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class RbacController extends BackendController
{
    //添加权限
    public function actionAddPermission(){
        $model = new PermissionForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->addPermission()){
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['rbac/index-permission']);
            }
        }
//        var_dump($model);exit;
        return $this->render('add-permission',['model'=>$model]);
    }
    //权限列表
    public function actionIndexPermission()
    {
        $permissions = \Yii::$app->authManager->getPermissions();
        return $this->render('index-permission',['permissions'=>$permissions]);
    }
    //修改权限
    public function actionEditPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
        if ($permission == null){
            throw new NotFoundHttpException('未找到该权限');
        }
        $model = new PermissionForm();
        $model->loadData($permission);
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->editPermission($name)){
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['rbac/index-permission']);
            }
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //删除权限
    public function actionDelPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
        if ($permission == null){
            throw new NotFoundHttpException('未找到该权限');
        }
        if(\Yii::$app->authManager->remove($permission)){
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['rbac/index-permission']);
        }
    }
    //添加角色
    public function actionAddRole(){
        $model = new RoleForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->addRole()){
                \Yii::$app->session->setFlash('success','添加角色成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
        return $this->render('add-role',['model'=>$model]);
    }
    public function actionIndexRole(){
        $roles = \Yii::$app->authManager->getRoles();
        return $this->render('index-role',['roles'=>$roles]);
    }
    public function actionEditRole($name){
        $role = \Yii::$app->authManager->getRole($name);
        if ($role == null){
            throw new NotFoundHttpException('角色未找到');
        }
        $model = new RoleForm();
        $model->loadData($role);
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->editRole($name)){
                \Yii::$app->session->setFlash('success','修改角色成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
        return $this->render('add-role',['model'=>$model]);
    }
    public function actionDelRole($name){
        $role = \Yii::$app->authManager->getRole($name);
        if ($role == null){
            throw new NotFoundHttpException('角色未找到');
        }
        \Yii::$app->authManager->removeChildren($role);
        \Yii::$app->authManager->remove($role);
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['rbac/index-role']);
    }
}
