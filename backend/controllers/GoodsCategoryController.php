<?php

namespace backend\controllers;


use backend\models\GoodsCategory;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsCategoryController extends BackendController
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
        $category = GoodsCategory::find()->asArray()->all();
        $category = array_merge([['name'=>'最上级','id'=>0,'parent_id'=>0]],$category);
        return $this->render('add',['model'=>$model,'category'=>$category]);
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
        $category = GoodsCategory::find()->asArray()->all();
        $category = array_merge([['name'=>'最上级','id'=>0,'parent_id'=>0]],$category);
        return $this->render('add',['model'=>$model,'category'=>$category]);
    }
    public function actionDelete($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods-category/index']);
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
