<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\web\NotFoundHttpException;

class MenuController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $models = new Menu();
        $models = $models->sort();
        return $this->render('index',['models'=>$models]);
    }
    public function actionAdd(){
        $model = new Menu();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','添加菜单成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','修改菜单成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = Menu::findOne(['id'=>$id]);
        if (Menu::find()->where(['parent_id'=>$id])->all()){
            throw new NotFoundHttpException('不能删除有子分类的');
            return false;
        }
        $model->delete();
        \Yii::$app->session->setFlash('success','删除菜单成功');
        return $this->redirect(['menu/index']);
    }
}
