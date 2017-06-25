<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Locations;
use yii\helpers\Json;

class AddressController extends \yii\web\Controller
{
    public $layout='Goods';
    public function actionIndex(){
        $model = new Address();
        $member_id = \Yii::$app->user->getId();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->member_id = $member_id;//保存所属用户id
            if ($model->status){//如果勾选了默认，清空之前的默认
                $model->addressDefault();
            }
            $model->save();
            \Yii::$app->session->setFlash('success','添加收货地址成功');
            return $this->redirect(['address/index']);
        }
        $addresses = Address::findAll(['member_id'=>$member_id]);
//        var_dump($addresses);exit;
        return $this->render('index',['model'=>$model,'addresses'=>$addresses]);
    }
    public function actionEdit($id){
        $model = Address::findOne(['id'=>$id]);
        $member_id = \Yii::$app->user->getId();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->status){
                $model->addressDefault();
            }
            $model->save();
            echo '<script>alert("'.'修改成功'.'");</script>';
            return $this->redirect(['address/index']);
        }
        $addresses = Address::findAll(['member_id'=>$member_id]);
        return $this->render('index',['model'=>$model,'addresses'=>$addresses]);
    }
    public function actionDelete($id){
        $model = Address::findOne(['id'=>$id]);
        $model->delete();
        echo '<script>alert("删除成功")</script>';
//        \Yii::$app->session->setFlash('success','删除收货地址成功');
        return $this->redirect(['address/index']);
    }
    public function actionDefault($id){
        $model = Address::findOne(['id'=>$id]);
        $model->addressDefault();
        $model->status = 1;
        $model->save();

        return $this->redirect(['address/index']);
    }
    public function actionGetlist($parent_id,$level){
        $province = Locations::find()->where(['parent_id'=>$parent_id])->andWhere(['level'=>$level])->all();
        return Json::encode($province);
    }
}
