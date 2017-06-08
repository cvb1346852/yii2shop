<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;

class ArticleCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = ArticleCategory::find()->count();
        $page = new Pagination([
            'totalCount'=>$count,
            'pageSize'=>2,
        ]);
        $categorys = ArticleCategory::find()->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['categorys'=>$categorys,'page'=>$page]);
    }
    public function actionAdd(){
        $model = new ArticleCategory();
        if ($model->load(\yii::$app->request->post())){
            if ($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        if ($model->load(\yii::$app->request->post())){
            if ($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);
    }
}
