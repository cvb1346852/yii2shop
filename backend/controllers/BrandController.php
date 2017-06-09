<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = Brand::find()->count();
        $page = new Pagination([
            'pageSize'=>2,
            'totalCount'=>$count,
        ]);
        $brands = Brand::find()->orderBy(['sort'=>SORT_ASC])->offset($page->offset)->limit($page->pageSize)->all();
        return $this->render('index',['brands'=>$brands,'page'=>$page]);
    }
    //添加
    public function actionAdd(){
        $model = new Brand();
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
//            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
                /*if ($model->imgFile){
                    $filename = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    $model->logo = $filename;
                }*/
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
//            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
                /*if ($model->imgFile){
                    $filename = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    $model->logo = $filename;
                }*/
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

    //上传
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $imgUrl = $action->getWebUrl();//文件地址
                    //$action->output['fileUrl'] = $action->getWebUrl();
                    //上传到七牛云
                    $qiniu = \yii::$app->qiniu;
                    $qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取七牛云地址
                    $url = $qiniu->getLink($imgUrl);
                    $action->output['fileUrl'] = $url;
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
   /* public function actionTest(){//测试七牛云
        $ak = 'CLerOU3r5b3ngp3vgjM0n441FOnHziQyGG-lnLi_';
        $sk = 'SNzf1dqdsNBfd7uveVq6EGxWggDMxNh_z6QzdCLW';
        $domain = 'http://or9rfsq68.bkt.clouddn.com';
        $bucket = 'yiishop';

        $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
        $file = \yii::getAlias('@webroot').'/upload/001.jpg';
        $key = '001.jpg';
        $qiniu->uploadFile($file,$key);
        $url = $qiniu->getLink($key);
        var_dump($url);
    }*/
}
