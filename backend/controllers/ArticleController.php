<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use yii\data\Pagination;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = Article::find()->count();
        $page = new Pagination([
            'totalCount'=>$count,
            'pageSize'=>2,
        ]);
        $articles = Article::find()->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['articles'=>$articles,'page'=>$page]);
    }
    public function actionAdd(){
        $article_category = ArticleCategory::find()->all();
        $model = new Article();
        if ($model->load(\yii::$app->request->post())){
            if ($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'article_category'=>$article_category]);
    }
    public function actionEdit($id){
        $article_category = ArticleCategory::find()->all();
        $model = Article::findOne(['id'=>$id]);
        if ($model->load(\yii::$app->request->post())){
            if ($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'article_category'=>$article_category]);
    }
    public function actionDelete($id){
        $model = Article::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);
    }
}
