<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = Brand::find()->count();
        $page = new Pagination([
            'pageSize'=>2,
            'totalCount'=>$count,
        ]);
        $brands = Brand::find()->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['brands'=>$brands,'page'=>$page]);
    }
    //添加
    public function actionAdd(){
        $model = new Brand();
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
                if ($model->imgFile){
                    $filename = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    $model->logo = $filename;
                }
                $model->save();
                \yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
                if ($model->imgFile){
                    $filename = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    $model->logo = $filename;
                }
                $model->save();
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);
    }
}
