<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsPhoto;
use xj\uploadify\UploadAction;
use yii\web\Controller;

class GoodsPhotoController extends Controller
{
    public function actionIndex($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        $models = GoodsPhoto::findAll(['goods_id'=>$id]);
        return $this->render('index',['models'=>$models,'goods'=>$goods]);
    }
    public function actionAdd(){
        $model = new GoodsPhoto();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-photo/index','id'=>$model->goods_id]);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsPhoto::findOne(['id'=>$id]);
        $r = $model->delete();
//        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
//            $model->save();
//            \Yii::$app->session->setFlash('success','添加成功');
//            return $this->redirect(['goods-photo/index','id'=>$model->goods_id]);
//        }
        if ($r){
            return 'success';
        }else{
            return 'fail';
        }

    }
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                // 'format' => [$this, 'methodName'],
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
                    $fileext = $action->uploadfile;//->getExtension();
                    $filehash = sha1(uniqid() . time());
//                    $p1 = substr($filehash, 0, 2);
//                    $p2 = substr($filehash, 2, 2);
//                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
//                    return date('Ym')."/{$filehash}.{$fileext}";
                    return date('Ym')."/{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $model = new GoodsPhoto();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->photo = $action->getWebUrl();
                    $model->save();
//                    $imgUrl = $action->getWebUrl();//文件地址
                    $action->output['fileUrl'] = $action->getWebUrl();//回调
                    $action->output['id'] = $model->id;//回调
                    //上传到七牛云
                    //$qiniu = \yii::$app->qiniu;
                    //$qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取七牛云地址
                    //$url = $qiniu->getLink($imgUrl);
//                    $action->output['fileUrl'] = $url;//回调
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
}
