<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
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
//        $article_category = ArticleCategory::find()->all();
        $model = new Article();
        $content = new ArticleDetail();
        if ($model->load(\yii::$app->request->post()) &&  $content->load(\yii::$app->request->post())){
            if ($model->validate() && $content->validate()){
                $model->save();
                $content->article_id = $model->id;
                $content->save();
                \yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'content'=>$content]);
    }
    public function actionEdit($id){
//        $article_category = ArticleCategory::find()->all();
        $model = Article::findOne(['id'=>$id]);
        $content = ArticleDetail::findOne(['article_id'=>$id]);
        if ($model->load(\yii::$app->request->post()) &&  $content->load(\yii::$app->request->post())){
            if ($model->validate() && $content->validate()){
                $model->save();
//                $content->article_id = $model->id;
                $content->save();
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'content'=>$content]);
    }
    public function actionDelete($id){
        $model = Article::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);
    }
    public function actionDetail($id){
        $model = Article::findOne(['id'=>$id]);
        $detail = ArticleDetail::findOne(['article_id'=>$id]);
        return $this->render('detail',['model'=>$model,'detail'=>$detail]);
    }
}
