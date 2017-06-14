<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use backend\models\SearchForm;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $search = new searchForm();
        $models = Goods::find();
        $search->search($models);

        $count = $models->count();
        $page = new Pagination([
            'totalCount'=>$count,
            'pageSize'=>2,
        ]);
        $models = $models->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['models'=>$models,'page'=>$page,'search'=>$search]);
    }
    public function actionAdd(){
        $model = new Goods();
        $content = new GoodsIntro();
        if ($model->load(\Yii::$app->request->post()) && $content->load(\Yii::$app->request->post())){
            if ($model->validate() && $content->validate()){
                $model->save(false);
                $content->goods_id = $model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods/index']);
            }
        }
        $goods_category = GoodsCategory::find()->asArray()->all();
        return $this->render('add',['model'=>$model,'content'=>$content,'goods_category'=>$goods_category]);
    }
    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $content = GoodsIntro::findOne(['goods_id'=>$id]);
        if ($model->load(\Yii::$app->request->post()) && $content->load(\Yii::$app->request->post())){
            if ($model->validate() && $content->validate()){
                $model->save(false);
                $content->goods_id = $model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }
        }
        $goods_category = GoodsCategory::find()->asArray()->all();
        return $this->render('add',['model'=>$model,'content'=>$content,'goods_category'=>$goods_category]);
    }
    public function actionDelete($id){
        $model = Goods::findOne(['id'=>$id]);
        $model_intro =  GoodsIntro::findOne(['goods_id'=>$id]);
        $model->delete();
        $model_intro->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods/index']);
    }
    public function actionIntro($id){
        $model = Goods::findOne(['id'=>$id]);
        return $this->render('intro',['model'=>$model]);
    }
}
