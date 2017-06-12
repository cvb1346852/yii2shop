<?php

namespace backend\controllers;


use backend\models\GoodsCategory;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
       $models = GoodsCategory::find()->orderBy(['tree'=>SORT_ASC,'lft'=>SORT_ASC])->all();
        return $this->render('index',['models'=>$models]);
    }

    public function actionAdd(){
        $model = new GoodsCategory();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->parent_id){
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                $model->prependTo($parent);
            }else{
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-category/index']);
        }
        $categoty = GoodsCategory::find()->asArray()->all();
        $categoty = array_merge([['name'=>'最上级','id'=>0,'parent_id'=>0]],$categoty);
        return $this->render('add',['model'=>$model,'categoty'=>$categoty]);
    }
    public function actionEdit($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        if (empty($model)){
            throw new NotFoundHttpException('分类不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->parent_id){
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                $model->prependTo($parent);
            }else{
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['goods-category/index']);
        }
        $categoty = GoodsCategory::find()->asArray()->all();
        $categoty = array_merge([['name'=>'最上级','id'=>0,'parent_id'=>0]],$categoty);
        return $this->render('add',['model'=>$model,'categoty'=>$categoty]);
    }

    public function actionTest(){
        //添加1级分类
//        $jydq = new GoodsCategory();
//        $jydq->name = '家用电器';
//        $jydq->parent_id = 0;
//        $jydq->makeRoot();
         // 添加二级及以上分类
//        $parent = GoodsCategory::findOne(['id'=>1]);
//        $xjd = new GoodsCategory(['name'=>'大家电','parent_id'=>$parent->id]);
//        $xjd->prependTo($parent);
        //所有一级分类
//        $roots = GoodsCategory::find()->roots()->all();
//        var_dump($roots);
//        //获取该分类下的所有子分类
//        $parent = GoodsCategory::findOne(['id'=>1]);
//        $leaves =  $parent->leaves()->all();
//        var_dump($leaves);
    }
    public function actionZtree(){
        $model = GoodsCategory::find()->all();
        return $this->renderPartial('ztree',['model'=>$model]);
    }
}
